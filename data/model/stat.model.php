<?php
/**
 * 统计管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class statModel extends Model{
    /**
     * 查询新增会员统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @param boolean $lock 是否锁定
     * @return array
     */
    public function statByMember($where, $field = '*', $page = 0, $order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('member')->field($field)->where($where)->page($page[0],$page[1])->order($order)->group($group)->select();
            } else {
                return $this->table('member')->field($field)->where($where)->page($page[0])->order($order)->group($group)->select();
            }
        } else {
            return $this->table('member')->field($field)->where($where)->page($page)->order($order)->group($group)->select();
        }  
    }
    
    /**
     * 查询新增店铺统计
     */
	public function getNewStoreStatList($condition, $field = '*', $page = 0, $order = 'store_id desc', $limit = 0, $group = '', $lock = false) {
        return $this->table('store')->field($field)->where($condition)->group($group)->select();
    }
    
    /**
     * 查询会员列表
     */
    public function getMemberList($where, $field = '*', $page = 0, $order = 'member_id desc', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('member')->field($field)->where($where)->page($page[0],$page[1])->group($group)->order($order)->select();
            } else {
                return $this->table('member')->field($field)->where($where)->page($page[0])->group($group)->order($order)->select();
            }
        } else {
            return $this->table('member')->field($field)->where($where)->page($page)->group($group)->order($order)->select();
        }
    }
    
	/**
     * 查询店铺销量统计
     */
	public function getStoreSaleStatList($condition, $field = '*', $page = 0, $order = 'order_id desc', $limit = 0, $group = '', $lock = false) {
        return $this->table('order')->field($field)->where($condition)->group($group)->order('allnum desc')->select();
    }
    /**
     * 调取店铺等级信息
     */
    public function getStoreDegree(){
    	$tmp = $this->table('store_grade')->field('sg_id,sg_name')->where(true)->select();
    	$sd_list = array();
    	if(!empty($tmp)){
	    	foreach ($tmp as $k=>$v){
	    		$sd_list[$v['sg_id']] = $v['sg_name'];
	    	}
    	}
    	return $sd_list;
    }
    /**
     * 调取订单数据表
     */
	public function getStoreOrderList($condition,$limit=true) {
		if($limit){
        	return $this->field('order_id,order_sn,store_name,buyer_name,add_time,order_amount,order_state')->table('order')->where($condition)->order('add_time desc')->page(15)->select();
		}else{
			return $this->field('order_id,order_sn,store_name,buyer_name,add_time,order_amount,order_state')->table('order')->where($condition)->order('add_time desc')->select();
		}
    }
    
    /**
     * 查询会员统计数据记录
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStatmember($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('stat_member')->field($field)->where($where)->page($page[0],$page[1])->limit($limit)->order($order)->group($group)->select();
            } else {
                return $this->table('stat_member')->field($field)->where($where)->page($page[0])->limit($limit)->order($order)->group($group)->select();
            }
        } else {
            return $this->table('stat_member')->field($field)->where($where)->page($page)->limit($limit)->order($order)->group($group)->select();
        }
    }
    
    /**
     * 查询商品数量
     */
    public function getGoodsNum($where){
    	$rs = $this->field('count(*) as allnum')->table('goods_common')->where($where)->select();
    	return $rs[0]['allnum'];
    }
    /**
     * 获取销售信息
     */
    public function getTradeInfo($type,$stime,$etime){
    	switch ($type){
    		case 'order_goods_num'://下单商品数
    			$data = $this->field('sum(goods_num) as allnum')->table('order_goods,order')->join('left join')->on('order_goods.order_id=order.order_id')->where(array('order.add_time'=>array('between', array($stime,$etime)),'order.order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order.order_state = '".ORDER_STATE_CANCEL."' and order.refund_state = 0)")))->select();
    			return $data[0]['allnum'];
    			break;
    		case 'order_num'://下单单量
    			$data = $this->field('count(*) as allnum')->table('order')->where(array('add_time'=>array('between', array($stime,$etime)),'order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order_state = '".ORDER_STATE_CANCEL."' and refund_state = 0)")))->select();
    			return $data[0]['allnum'];
    			break;
    		case 'order_buyer_num'://下单客户数
    			$data = $this->field('DISTINCT(buyer_id)')->table('order')->where(array('add_time'=>array('between', array($stime,$etime)),'order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order_state = '".ORDER_STATE_CANCEL."' and refund_state = 0)")))->select();
    			return count($data);
    			break;
    		case 'order_amount'://合计金额
    			$data = $this->field('sum(order_amount) as allnum')->table('order')->where(array('add_time'=>array('between', array($stime,$etime)),'order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order_state = '".ORDER_STATE_CANCEL."' and refund_state = 0)")))->select();
    			return $data[0]['allnum'];
    			break;
    	}
    }
    /**
     * 获取商品销售排名
     */
    public function getGoodsTradeRanking($type,$stime,$etime){
    	switch ($type){
    		case 'trade_num'://按销量
    			return $this->field('sum(goods_num) as allnum,goods_name,goods_id')->table('order_goods,order')->join('left join')->on('order_goods.order_id=order.order_id')->where(array('order.add_time'=>array('between', array($stime,$etime)),'order.order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order.order_state = '".ORDER_STATE_CANCEL."' and order.refund_state = 0)"),'payment_code'=>array('exp',"!(order.payment_code='offline' and order_state <> '".ORDER_STATE_SUCCESS."')")))->group('order_goods.goods_id')->limit(15)->order('allnum desc')->select();
    			break;
    		case 'trade_amount'://按销售额
    			return $this->field('sum(goods_price*goods_num) as allnum,goods_name,goods_id')->table('order_goods,order')->join('left join')->on('order_goods.order_id=order.order_id')->where(array('order.add_time'=>array('between', array($stime,$etime)),'order.order_state'=>array('neq',ORDER_STATE_NEW),'refund_state'=>array('exp',"!(order.order_state = '".ORDER_STATE_CANCEL."' and order.refund_state = 0)"),'payment_code'=>array('exp',"!(order.payment_code='offline' and order_state <> '".ORDER_STATE_SUCCESS."')")))->limit(15)->order('allnum desc')->group('order_goods.goods_id')->select();
    			break;
    	}
    }
    /**
     * 查询订单及地区的统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrderCommon($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order,order_common')->field($field)->join('left')->on('order.order_id=order_common.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order,order_common')->field($field)->join('left')->on('order.order_id=order_common.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }  
        } else {
            return $this->table('order,order_common')->field($field)->join('left')->on('order.order_id=order_common.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 获取预存款数据
     */
    public function getPredepositInfo($condition, $field = '*', $page = 0, $order = 'lg_add_time desc', $limit = 0, $group = '', $lock = false){
    	return $this->table('pd_log')->field($field)->where($condition)->page($page)->group($group)->order($order)->select();
    }
    /**
     * 获取商品销售明细列表
     */
    public function getGoodsTradeDetailList($condition,$page=15){
    	$condition = $this->_getRecursiveClass($condition);
    	if(intval($page) > 0){
    		$count_allnum = $this->field('count(DISTINCT goods.goods_id) as countnum')->table('goods,order_goods,goods_common,order')->join('left join')->on('goods.goods_id=order_goods.goods_id,goods.goods_commonid=goods_common.goods_commonid,order_goods.order_id=order.order_id')->where($condition)->select();
    		$count_allnum = $count_allnum[0]['countnum'];
    		return $this->field('sum(order_goods.goods_num) as gnum,count(*) as onum,sum(order_goods.goods_price*order_goods.goods_num) as pnum,goods.goods_name,goods.store_name,goods_common.goods_addtime,goods_common.goods_selltime,goods.goods_id')->table('goods,order_goods,goods_common,order')->join('left join')->on('goods.goods_id=order_goods.goods_id,goods.goods_commonid=goods_common.goods_commonid,order_goods.order_id=order.order_id')->where($condition)->group('goods.goods_id')->order('goods.goods_addtime desc')->page($page,$count_allnum)->select();
    	}else{
    		return $this->field('sum(order_goods.goods_num) as gnum,count(*) as onum,sum(order_goods.goods_price*order_goods.goods_num) as pnum,goods.goods_name,goods.store_name,goods_common.goods_addtime,goods_common.goods_selltime,goods.goods_id')->table('goods,order_goods,goods_common,order')->join('left join')->on('goods.goods_id=order_goods.goods_id,goods.goods_commonid=goods_common.goods_commonid,order_goods.order_id=order.order_id')->where($condition)->group('goods.goods_id')->order('goods.goods_addtime desc')->select();	
    	}
    }
	/**
      * 获得商品子分类的ID
      * @param array $condition
      * @return array 
      */
    private function _getRecursiveClass($condition){
        if (isset($condition['goods.gc_id']) && !is_array($condition['goods.gc_id'])) {
            $gc_list = H('goods_class') ? H('goods_class') : H('goods_class', true);
            if (!empty($gc_list[$condition['goods.gc_id']])) {
                $gc_id[] = $condition['goods.gc_id'];
                $gcchild_id = empty($gc_list[$condition['goods.gc_id']]['child']) ? array() : explode(',', $gc_list[$condition['goods.gc_id']]['child']);
                $gcchildchild_id = empty($gc_list[$condition['goods.gc_id']]['childchild']) ? array() : explode(',', $gc_list[$condition['goods.gc_id']]['childchild']);
                $gc_id = array_merge($gc_id, $gcchild_id, $gcchildchild_id);
                $condition['goods.gc_id'] = array('in', $gc_id);
            }
        }
        if (isset($condition['store.sc_id']) && !is_array($condition['store.sc_id'])) {
        	$sc_list = H('store_class') ? H('store_class') : H('store_class', true);
        	if(is_array($sc_list[$condition['store.sc_id']]['child']) && !empty($sc_list[$condition['store.sc_id']]['child'])){
        		$sc_child_string = $condition['store.sc_id'];
        		foreach ($sc_list[$condition['store.sc_id']]['child'] as $val){
        			$sc_child_string .= ','.$val;
        		} 
        		$condition['store.sc_id'] = array('in', $sc_child_string);
        	}
        }
        return $condition;
    }
    /**
     * 获取类目销售信息列表
     */
    public function getStoreTradeList($condition,$type,$limit=''){
    	$condition = $this->_getRecursiveClass($condition);
    	switch ($type){
    		case 'goods'://返回商品销售列表
    			return $this->field('sum(order_goods.goods_num) as gnum,count(*) as onum,sum(order_goods.goods_price*order_goods.goods_num) as pnum,goods.goods_name,goods.store_name,goods_class.gc_name,goods.goods_id')->table('goods,order_goods,order,goods_class')->join('left join')->on('goods.goods_id=order_goods.goods_id,order_goods.order_id=order.order_id,goods.gc_id=goods_class.gc_id')->where($condition)->group('goods.goods_id')->order('sum(order_goods.goods_price*order_goods.goods_num) desc')->limit($limit)->select();
    			break;
    		case 'store'://返回店铺销售列表
    			return $this->field('count(*) as onum,sum(order.order_amount) as pnum,store.store_name,store_class.sc_name,store.member_name')->table('store,order,store_class')->join('left join')->on('store.store_id=order.store_id,store.sc_id=store_class.sc_id')->where($condition)->group('store.store_id')->order('sum(order.order_amount) desc')->limit($limit)->select();
    			break;
    	}
    }
    /**
     * 获取结算数据
     */
    public function getBillList($condition,$type,$have_page=true){
    	switch ($type){
    		case 'os'://平台
    			return $this->field('sum(os_order_totals) as oot,sum(os_order_return_totals) as oort,sum(os_commis_totals-os_commis_return_totals) as oct,sum(os_store_cost_totals) as osct,sum(os_result_totals) as ort')->table('order_statis')->where($condition)->select();
    			break;
    		case 'ob'://店铺
    			$page = $have_page?15:'';
    			return $this->field('order_bill.*,store.member_name')->table('order_bill,store')->join('left join')->on('order_bill.ob_store_id=store.store_id')->where($condition)->page($page)->order('ob_no desc')->select();
    			break;
    	}
    }
	/**
     * 查询订单及订单商品的统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrderGoods($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order_goods,order')->field($field)->join('left')->on('order_goods.order_id=order.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order_goods,order')->field($field)->join('left')->on('order_goods.order_id=order.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }  
        } else {
            return $this->table('order_goods,order')->field($field)->join('left')->on('order_goods.order_id=order.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
	/**
     * 查询订单及订单商品的统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrderLog($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order_log,order')->field($field)->join('left')->on('order_log.order_id = order.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order_log,order')->field($field)->join('left')->on('order_log.order_id = order.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }  
        } else {
            return $this->table('order_log,order')->field($field)->join('left')->on('order_log.order_id = order.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
	/**
     * 查询退款退货统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByRefundreturn($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
	/**
     * 查询店铺动态评分统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStoreAndEvaluatestore($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = ''){
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
	 * 处理搜索时间
	 */
    public function dealwithSearchTime($search_arr){
	    //初始化时间
		//天
		if(!$search_arr['search_time']){
			$search_arr['search_time'] = date('Y-m-d', time());
		}
		$search_arr['day']['search_time'] = strtotime($search_arr['search_time']);//搜索的时间
		
		//周
		if(!$search_arr['searchweek_year']){
			$search_arr['searchweek_year'] = date('Y', time());
		}
		if(!$search_arr['searchweek_month']){
			$search_arr['searchweek_month'] = date('m', time());
		}
		if(!$search_arr['searchweek_week']){
			$search_arr['searchweek_week'] =  implode('|', getWeek_SdateAndEdate(time()));
		}
		$weekcurrent_year = $search_arr['searchweek_year'];
		$weekcurrent_month = $search_arr['searchweek_month'];
		$weekcurrent_week = $search_arr['searchweek_week'];
		$search_arr['week']['current_year'] = $weekcurrent_year;
		$search_arr['week']['current_month'] = $weekcurrent_month;
		$search_arr['week']['current_week'] = $weekcurrent_week;
		
		//月
		if(!$search_arr['searchmonth_year']){
			$search_arr['searchmonth_year'] = date('Y', time());
		}
		if(!$search_arr['searchmonth_month']){
			$search_arr['searchmonth_month'] = date('m', time());
		}
		$monthcurrent_year = $search_arr['searchmonth_year'];
		$monthcurrent_month = $search_arr['searchmonth_month'];
		$search_arr['month']['current_year'] = $monthcurrent_year;
		$search_arr['month']['current_month'] = $monthcurrent_month;
		return $search_arr;
	}
	/**
	 * 获得查询的开始和结束时间
	 */
	public function getStarttimeAndEndtime($search_arr){
	    if($search_arr['search_type'] == 'day'){
			$stime = $search_arr['day']['search_time'];//今天0点
			$etime = $search_arr['day']['search_time'] + 86400 - 1;//今天24点
		}
	    if($search_arr['search_type'] == 'week'){
	        $current_weekarr = explode('|', $search_arr['week']['current_week']);
			$stime = strtotime($current_weekarr[0]);
			$etime = strtotime($current_weekarr[1])+86400-1;
		}
	    if($search_arr['search_type'] == 'month'){
	        $stime = strtotime($search_arr['month']['current_year'].'-'.$search_arr['month']['current_month']."-01 0 month");
			$etime = getMonthLastDay($search_arr['month']['current_year'],$search_arr['month']['current_month'])+86400-1;
		}
		return array($stime,$etime);
	}
	/**
     * 查询会员统计数据单条记录
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function getOneStatmember($where, $field = '*', $order = '', $group = ''){
        return $this->table('stat_member')->field($field)->where($where)->group($group)->order($order)->find();
    }
	/**
     * 更新会员统计数据单条记录
     * 
     * @param array $condition 条件
     * @param array $update_arr 更新数组
     * @return array
     */
    public function updateStatmember($where,$update_arr){
        return $this->table('stat_member')->where($where)->update($update_arr);
    }
	/**
     * 查询订单的统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrder($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }   
        } else {
            return $this->table('order')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
	/**
     * 查询积分的统计
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByPointslog($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('points_log')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('points_log')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('points_log')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
	/**
     * 删除会员统计数据记录
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function delByStatmember($where = array()) {
        $this->table('stat_member')->where($where)->delete();   
    }
	/**
     * 店铺销售排行
     */
	public function getStoreSaleRank($condition,$type) {
		switch ($type){
			case 'sale_amount'://按销售额排行
				return $this->field('sum(order_amount) as allnum,store_name')->table('order')->where($condition)->order('allnum desc')->group('store_id')->limit(15)->select();
				break;
			case 'sale_num'://按下单量排行
				return $this->field('count(*) as allnum,store_name')->table('order')->where($condition)->order('allnum desc')->group('store_id')->limit(15)->select();
				break;
		}
    }
}