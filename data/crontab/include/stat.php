<?php
/**
 * 任务计划 - 统计数据缓存
 *
 * 
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class statControl {

    public function __construct(){
        register_shutdown_function(array($this,"shutdown"));
    }

	/**
     * 会员相关数据统计
     */
    public function memberstatOp(){
        $model = Model('stat');
        //查询最后统计的记录
        $latest_record = $model->getOneStatmember(array(), '', 'statm_id desc');
        $stime = 0;
        if ($latest_record){
            $start_time = strtotime(date('Y-m-d',$latest_record['statm_updatetime']));
        } else {
            $start_time = strtotime(date('Y-m-d',strtotime(C('setup_date'))));//从系统的安装时间开始统计
        }
        $j = 1;
        for ($stime = $start_time; $stime < time(); $stime = $stime+86400){
            //数据库更新数据数组
            $insert_arr = array();
            $update_arr = array();
            
            $etime = $stime + 86400 - 1;
            //避免重复统计，开始时间必须大于最后一条记录的记录时间
            $search_stime = $latest_record['statm_updatetime'] > $stime?$latest_record['statm_updatetime']:$stime;
            //统计一天的数据，如果结束时间大于当前时间，则结束时间为当前时间，避免因为查询时间的延迟造成数据遗落
            $search_etime = ($t = ($stime + 86400 - 1)) > time() ? time() : ($stime + 86400 - 1);
            
            //统计订单下单量和下单金额
            $field = ' order.order_id,add_time,buyer_id,buyer_name,order_amount';
            $where = array();
            $where['order.order_state'] = array('neq',ORDER_STATE_NEW);//去除未支付订单
            $where['order.refund_state'] = array('exp',"!(order.order_state = '".ORDER_STATE_CANCEL."' and order.refund_state = 0)");//没有参与退款的取消订单，不记录到统计中
            $where['order_log.log_time'] = array('between',array($search_stime,$search_etime));//按照订单付款的操作时间统计
            //货到付款当交易成功进入统计，非货到付款当付款后进入统计
            $where['payment_code'] = array('exp',"(order.payment_code='offline' and order_log.log_orderstate = '".ORDER_STATE_SUCCESS."') or (order.payment_code<>'offline' and order_log.log_orderstate = '".ORDER_STATE_PAY."' )");
            $orderlist_tmp = $model->statByOrderLog($where, $field, 0, 0, 'order_id');//此处由于底层的限制，仅能查询1000条，如果日下单量大于1000，则需要limit的支持
            
            $order_list = array();
            $orderid_list = array();
            foreach ((array)$orderlist_tmp as $k=>$v){
                $addtime = strtotime(date('Y-m-d',$v['add_time']));
                if ($addtime != $stime){//订单如果隔天支付的话，需要进行统计数据更新
                    $update_arr[$addtime][$v['buyer_id']]['statm_membername'] = $v['buyer_name'];
                    $update_arr[$addtime][$v['buyer_id']]['statm_ordernum'] = intval($update_arr[$addtime][$v['buyer_id']]['statm_ordernum'])+1;
                    $update_arr[$addtime][$v['buyer_id']]['statm_orderamount'] = floatval($update_arr[$addtime][$v['buyer_id']]['statm_orderamount']) + (($t = floatval($v['order_amount'])) > 0?$t:0);
                } else {
                    $order_list[$v['buyer_id']]['buyer_name'] = $v['buyer_name'];
                    $order_list[$v['buyer_id']]['ordernum'] = intval($order_list[$v['buyer_id']]['ordernum']) + 1;
                    $order_list[$v['buyer_id']]['orderamount'] = floatval($order_list[$v['buyer_id']]['orderamount']) + (($t = floatval($v['order_amount'])) > 0?$t:0);
                }
                //记录订单ID数组
                $orderid_list[] = $v['order_id'];
            }
            
            //统计下单商品件数
            if ($orderid_list){
                $field = ' add_time,order.buyer_id,order.buyer_name,goods_num ';
                $where = array();
                $where['order.order_id'] = array('in',$orderid_list);
                $ordergoods_tmp = $model->statByOrderGoods($where, $field, 0, 0, 'order.order_id');
                $ordergoods_list = array();
                foreach ((array)$ordergoods_tmp as $k=>$v){
                    $addtime = strtotime(date('Y-m-d',$v['add_time']));
                    if ($addtime != $stime){//订单如果隔天支付的话，需要进行统计数据更新
                        $update_arr[$addtime][$v['buyer_id']]['statm_goodsnum'] = intval($update_arr[$addtime][$v['buyer_id']]['statm_goodsnum']) + (($t = floatval($v['goods_num'])) > 0?$t:0);
                    } else {
                        $ordergoods_list[$v['buyer_id']]['goodsnum'] = $ordergoods_list[$v['buyer_id']]['goodsnum'] + (($t = floatval($v['goods_num'])) > 0?$t:0);
                    }
                }
            }
            
            //统计的预存款记录
            $field = ' lg_member_id,lg_member_name,SUM(IF(lg_av_amount>=0,lg_av_amount,0)) as predincrease, SUM(IF(lg_av_amount<=0,lg_av_amount,0)) as predreduce ';
            $where = array();
            $where['lg_add_time'] = array('between',array($stime,$etime));
            $predeposit_tmp = $model->getPredepositInfo($where, $field, 0, 'lg_member_id', 0, 'lg_member_id');
            $predeposit_list = array();
            foreach ((array)$predeposit_tmp as $k=>$v){
                $predeposit_list[$v['lg_member_id']] = $v;
            }
            
            //统计的积分记录
            $field = ' pl_memberid,pl_membername,SUM(IF(pl_points>=0,pl_points,0)) as pointsincrease, SUM(IF(pl_points<=0,pl_points,0)) as pointsreduce ';
            $where = array();
            $where['pl_addtime'] = array('between',array($stime,$etime));
            $points_tmp = $model->statByPointslog($where, $field, 0, 0, '', 'pl_memberid');
            $points_list = array();
            foreach ((array)$points_tmp as $k=>$v){
                $points_list[$v['pl_memberid']] = $v;
            }
            
            //处理需要更新的数据
            foreach ((array)$update_arr as $k=>$v){
                foreach ($v as $m_k=>$m_v){
                    //查询记录是否存在
                    $statmember_info = $model->getOneStatmember(array('statm_time'=>$k,'statm_memberid'=>$m_k));
                    if ($statmember_info){
                        $m_v['statm_ordernum'] = intval($statmember_info['statm_ordernum']) + $m_v['statm_ordernum'];
                        $m_v['statm_orderamount'] = floatval($statmember_info['statm_ordernum']) + $m_v['statm_orderamount'];
                        $m_v['statm_updatetime'] = $search_etime;
                        $model->updateStatmember(array('statm_time'=>$k,'statm_memberid'=>$m_k),$m_v);
                    } else {
                        $tmp = array();
                        $tmp['statm_memberid'] = $m_k;
                        $tmp['statm_membername'] = $m_v['statm_membername'];
                        $tmp['statm_time'] = $k;
                        $tmp['statm_updatetime'] = $search_etime;
                        $tmp['statm_ordernum'] = ($t = intval($m_v['statm_ordernum'])) > 0?$t:0;
                        $tmp['statm_orderamount'] = ($t = floatval($m_v['statm_orderamount']))>0?$t:0;
                        $tmp['statm_goodsnum'] = ($t = intval($m_v['statm_goodsnum']))?$t:0;
                        $tmp['statm_predincrease'] = 0;
                        $tmp['statm_predreduce'] = 0;
                        $tmp['statm_pointsincrease'] = 0;
                        $tmp['statm_pointsreduce'] = 0;
                        $insert_arr[] = $tmp;
                    }
                    unset($statmember_info);
                }
            }
            
            //处理获得所有会员ID数组
            $memberidarr_order = $order_list?array_keys($order_list):array();
            $memberidarr_ordergoods = $ordergoods_list?array_keys($ordergoods_list):array();
            $memberidarr_predeposit = $predeposit_list?array_keys($predeposit_list):array();
            $memberidarr_points = $points_list?array_keys($points_list):array();
            $memberid_arr = array_merge($memberidarr_order,$memberidarr_ordergoods,$memberidarr_predeposit,$memberidarr_points);
            //查询会员信息
            $memberid_list = Model('member')->getMemberList(array('member_id'=>array('in',$memberid_arr)), '', 0);
            //查询记录是否存在
            $statmemberlist_tmp = $model->statByStatmember(array('statm_time'=>$stime));
            $statmemberlist = array();
            foreach ((array)$statmemberlist_tmp as $k=>$v){
                $statmemberlist[$v['statm_memberid']] = $v;
            }
            foreach ((array)$memberid_list as $k=>$v){
                $tmp = array();
                $tmp['statm_memberid'] = $v['member_id'];
                $tmp['statm_membername'] = $v['member_name'];
                $tmp['statm_time'] = $stime;
                $tmp['statm_updatetime'] = $search_etime;
                //因为记录可能已经存在，所以加上之前的统计记录
                $tmp['statm_ordernum'] = intval($statmemberlist[$tmp['statm_memberid']]['statm_ordernum']) + (($t = intval($order_list[$tmp['statm_memberid']]['ordernum'])) > 0?$t:0);
                $tmp['statm_orderamount'] = floatval($statmemberlist[$tmp['statm_memberid']]['statm_orderamount']) + (($t = floatval($order_list[$tmp['statm_memberid']]['orderamount']))>0?$t:0);
                $tmp['statm_goodsnum'] = intval($statmemberlist[$tmp['statm_memberid']]['statm_goodsnum']) + (($t = intval($ordergoods_list[$tmp['statm_memberid']]['goodsnum']))?$t:0);
                $tmp['statm_predincrease'] = (($t = floatval($predeposit_list[$tmp['statm_memberid']]['predincrease']))?$t:0);
                $tmp['statm_predreduce'] = (($t = floatval($predeposit_list[$tmp['statm_memberid']]['predreduce']))?$t:0);
                $tmp['statm_pointsincrease'] = (($t = intval($points_list[$tmp['statm_memberid']]['pointsincrease']))?$t:0);
                $tmp['statm_pointsreduce'] = (($t = intval($points_list[$tmp['statm_memberid']]['pointsreduce']))?$t:0);
                $insert_arr[] = $tmp;
            }
            //删除旧的统计数据
            $model->delByStatmember(array('statm_time'=>$stime));
            $model->table('stat_member')->insertAll($insert_arr);
        }
    }

    /**
     * 执行完成提示信息
     *
     */
    public function shutdown(){
        exit("\nsuccess");
    }
}