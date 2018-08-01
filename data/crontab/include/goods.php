<?php
/**
 * 任务计划 - 通用任务、促销处理
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

class goodsControl {

    public function __construct(){
        register_shutdown_function(array($this,"shutdown"));
    }

    /**
     * 更新商品促销到期状态
     */
    public function promotionOp() {
        //团购活动过期
        Model('groupbuy')->editExpireGroupbuy();
        //限时折扣过期
        Model('p_xianshi')->editExpireXianshi();
        //满即送过期
        Model('p_mansong')->editExpireMansong();
    }

    /**
     * 更新首页的商品价格信息
     */
    public function web_updateOp(){
         Model('web_config')->updateWebGoods();
    }

    /**
     * 执行通用任务
     */
    public function commonOp(){

        //查找待执行任务
        $model_cron = Model('cron');
        $cron = $model_cron->getCronList(array('exetime'=>array('elt',TIMESTAMP)));
        if (!is_array($cron)) return ;
        $cron_array = array(); $cronid = array();
        foreach ($cron as $v) {
            $cron_array[$v['type']][$v['exeid']] = $v;
        }
        foreach ($cron_array as $k=>$v) {
            if (!method_exists($this,'_cron_'.$k)) {
                $tmp = current($v);
                $cronid[] = $tmp['id'];continue;
            }
            $result = call_user_func_array(array($this,'_cron_'.$k),array($v));
            if (is_array($result)){
                $cronid = array_merge($cronid,$result);
            }
        }

        //删除执行完成的cron信息
        if (!empty($cronid) && is_array($cronid)){
            $model_cron->delCron(array('id'=>array('in',$cronid)));
        }
    }

    /**
     * 上架
     *
     * @param array $cron
     */
    private function _cron_1($cron = array()){
        $condition = array('goods_commonid' => array('in',array_keys($cron)));
        $update = Model('goods')->editProducesOnline($condition);
        if ($update){
            //返回执行成功的cronid
            $cronid = array_keys($cron);
        }else{
            return false;
        }
        return $cronid;
    }

    /**
     * 优惠套装过期
     *
     * @param array $cron
     */
    private function _cron_3($cron = array()) {
        $condition = array('store_id' => array('in', array_keys($cron)));
        $update = Model('p_bundling')->editBundlingQuotaClose($condition);
        if ($update) {
            // 返回执行成功的cronid
            $cronid = array_keys($cron);
        } else {
            return false;
        }
        return $cronid;
    }

    /**
     * 推荐展位过期
     *
     * @param array $cron
     */
    private function _cron_4($cron = array()) {
        $condition = array('store_id' => array('in', array_keys($cron)));
        $update = Model('p_booth')->editBoothClose($condition);
        if ($update) {
            // 返回执行成功的cronid
            $cronid = array_keys($cron);
        } else {
            return false;
        }
        return $cronid;
    }

    /**
     * 执行完成提示信息
     *
     */
    public function shutdown(){
        exit("\nsuccess");
    }
}