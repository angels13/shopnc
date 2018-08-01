<?php
/**
 * 交易新模型
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
class tradeModel extends Model{
	public function __construct() {
		parent::__construct();
	}
	/**
	 * 订单处理天数
	 *
	 */
	public function getMaxDay($day_type = 'all') {
		$max_data = array(
			'order_cancel' => 7,//未选择支付方式时取消订单
			'order_confirm' => 10,//买家不收货也没退款时自动完成订单
			'order_refund' => 15,//收货完成后可以申请退款退货
			'refund_confirm' => 7,//卖家不处理退款退货申请时按同意处理
			'return_confirm' => 7,//卖家不处理收货时按弃货处理
			'return_delay' => 5//退货的商品发货多少天以后才可以选择没收到
			);
		if ($day_type == 'all') return $max_data;//返回所有
		if (intval($max_data[$day_type]) < 1) $max_data[$day_type] = 1;//最小的值设置为1
		return $max_data[$day_type];
	}
	/**
	 * 订单状态
	 *
	 */
	public function getOrderState($type = 'all') {
		$state_data = array(
			'order_cancel' => ORDER_STATE_CANCEL,//0:已取消
			'order_default' => ORDER_STATE_NEW,//10:未付款
			'order_paid' => ORDER_STATE_PAY,//20:已付款
			'order_shipped' => ORDER_STATE_SEND,//30:已发货
			'order_completed' => ORDER_STATE_SUCCESS //40:已收货
			);
		if ($type == 'all') return $state_data;//返回所有
		return $state_data[$type];
	}
	/**
	 * 更新订单
	 * @param int $member_id 会员编号
	 * @param int $store_id 店铺编号
	 */
	public function editOrderPay($member_id=0, $store_id=0) {
		$order_cancel = $this->getMaxDay('order_cancel');//未选择支付方式时取消订单的天数
		$day = time()-$order_cancel*60*60*24;
		$order_confirm = $this->getMaxDay('order_confirm');//买家不收货也没锁定订单时自动完成订单的天数
		$shipping_day = time()-$order_confirm*60*60*24;
		$order_default = $this->getOrderState('order_default');//订单状态10:未付款
		$order_shipped = $this->getOrderState('order_shipped');//订单状态30:已发货
		$condition = " ((order_state='".$order_default."' and add_time<".$day.") or (order_state='".$order_shipped."' and lock_state=0 and delay_time<".$shipping_day."))";//待支付(10)和待收货(30)
		$condition_sql = "";
		if ($member_id > 0) {
			$condition_sql = " buyer_id = '".$member_id."' and ";
		}
		if ($store_id > 0) {
			$condition_sql = " store_id = '".$store_id."' and ";
		}
		$condition_sql = $condition_sql.$condition;
		$field = 'order_id,buyer_id,store_id,add_time,payment_time,delay_time,order_state';
		$order_list = $this->table('order')->field($field)->where($condition_sql)->select();
		Language::read('model_lang_index');
		Language::read('refund');
		if (!empty($order_list) && is_array($order_list)) {
			foreach($order_list as $k => $v) {
				$order_id = $v['order_id'];//订单编号
				$order_state = $v['order_state'];//订单状态
				$log_array = array();
				$log_array['log_role'] = 'system';
				$log_array['log_time'] = time();
				$log_array['order_id'] = $order_id;
				switch ($order_state) {
    			    case $order_default:
    			    	$order_time = $v['add_time'];//订单生成时间
    					if (intval($order_time) < $day) {//超期时取消订单
    						$state_info = Language::get('order_max_day').$order_cancel.Language::get('order_max_day_cancel');
    						$log_array['log_msg'] = $state_info;
    						$this->editOrderCancel($order_id, $log_array);
    					}
    			    	break;
    			    case $order_shipped:
    			    	$order_time = $v['delay_time'];
    					if (intval($order_time) < $shipping_day) {//超期时自动完成订单
    						$state_info = Language::get('order_max_day').$order_confirm.Language::get('order_max_day_confirm');
    						$log_array['log_msg'] = $state_info;
    						$this->editOrderFinnsh($order_id, $log_array);
    					}
    			    	break;
				}
			}
			return true;
		}
		return false;
	}
	/**
	 * 取消订单并退回库存
	 * @param int $order_id 订单编号
	 * @param	array	$log_array	订单记录信息
	 */
	public function editOrderCancel($order_id, $log_array) {
		$goods_list = $this->table('order_goods')->field('order_id,goods_num,goods_id')->where(array('order_id'=> $order_id))->select();//订单商品
		if (!empty($goods_list) && is_array($goods_list)) {
			foreach($goods_list as $k => $v) {
				$goods_id = $v['goods_id'];
				$goods_num = $v['goods_num'];
        	    $condition = array();
        	    $condition['goods_id'] = $goods_id;
        	    $condition['goods_salenum'] = array('egt',$goods_num);
				$data = array();
				$data['goods_storage'] = array('exp','goods_storage+'.$goods_num);//库存
				$data['goods_salenum'] = array('exp','goods_salenum-'.$goods_num);//销售记录
				$state = $this->table('goods')->where($condition)->update($data);
			}
			$order_cancel = $this->getOrderState('order_cancel');//订单状态0:已取消
        	$order_array = array();
        	$order_array['order_state'] = $order_cancel;
        	$model_order = Model('order');
        	$state = $model_order->editOrder($order_array, array('order_id'=> $order_id));//更新订单
			if ($state) {
			    $log_array['log_orderstate'] = $order_array['order_state'];
			    $state = $model_order->addOrderLog($log_array);
        	}
			return $state;
		}
		return false;
	}
	/**
	 * 更新退款申请
	 * @param int $member_id 会员编号
	 * @param int $store_id 店铺编号
	 */
	public function editRefundConfirm($member_id=0, $store_id=0) {
		Language::read('refund');
		$refund_confirm = $this->getMaxDay('refund_confirm');//卖家不处理退款申请时按同意并弃货处理
		$day = time()-$refund_confirm*60*60*24;
		$condition = " seller_state=1 and add_time<".$day;//状态:1为待审核,2为同意,3为不同意
		$condition_sql = "";
		if ($member_id > 0) {
			$condition_sql = " buyer_id = '".$member_id."'  and ";
		}
		if ($store_id > 0) {
			$condition_sql = " store_id = '".$store_id."' and ";
		}
		$condition_sql = $condition_sql.$condition;
		$refund_array = array();
		$refund_array['refund_state'] = '2';//状态:1为处理中,2为待管理员处理,3为已完成
		$refund_array['seller_state'] = '2';//卖家处理状态:1为待审核,2为同意,3为不同意
		$refund_array['return_type'] = '1';//退货类型:1为不用退货,2为需要退货
		$refund_array['seller_time'] = time();
		$refund_array['seller_message'] = Language::get('order_max_day').$refund_confirm.Language::get('order_day_refund');
		$this->table('refund_return')->where($condition_sql)->update($refund_array);

		$return_confirm = $this->getMaxDay('return_confirm');//卖家不处理收货时按弃货处理
		$day = time()-$return_confirm*60*60*24;
		$condition = " seller_state=2 and goods_state=2 and return_type=2 and delay_time<".$day;//物流状态:1为待发货,2为待收货,3为未收到,4为已收货
		$condition_sql = "";
		if ($member_id > 0) {
			$condition_sql = " buyer_id = '".$member_id."'  and ";
		}
		if ($store_id > 0) {
			$condition_sql = " store_id = '".$store_id."' and ";
		}
		$condition_sql = $condition_sql.$condition;
		$refund_array = array();
		$refund_array['refund_state'] = '2';//状态:1为处理中,2为待管理员处理,3为已完成
		$refund_array['return_type'] = '1';//退货类型:1为不用退货,2为需要退货
		$refund_array['seller_message'] = Language::get('order_max_day').$return_confirm.'天未处理收货，按弃货处理';
		$this->table('refund_return')->where($condition_sql)->update($refund_array);
	}
	/**
	 * 自动收货完成订单
	 * @param int $order_id 订单编号
	 * @param	array	$log_array	订单记录信息
	 */
	public function editOrderFinnsh($order_id, $log_array = array()) {
		$field = 'order_id,buyer_id,buyer_name,store_id,order_sn,order_amount,payment_code,order_state';
		$order = $this->table('order')->field($field)->where(array('order_id'=> $order_id))->find();
		$order_shipped = $this->getOrderState('order_shipped');//订单状态30:已发货
		$order_completed = $this->getOrderState('order_completed');//订单状态40:已收货
		if ($order['order_state'] == $order_shipped) {//确认已经完成发货
			if (empty($log_array)) {
				$log_array['order_id'] = $order_id;
				$log_array['log_role'] = 'system';
				$log_array['log_msg'] = Language::get('order_completed');
				$log_array['log_time'] = time();
			}
			$state = true;
			$order_array = array();
			$order_array['finnshed_time'] = time();
			$order_array['order_state'] = $order_completed;
			$model_order = Model('order');
			$state = $model_order->editOrder($order_array, array('order_id'=> $order_id));//更新订单状态为已收货
			$log_array['log_orderstate'] = $order_array['order_state'];
			if ($state) $state = $model_order->addOrderLog($log_array);//订单处理记录信息
			return $state;
		} else {
			return false;
		}
	}

}
?>