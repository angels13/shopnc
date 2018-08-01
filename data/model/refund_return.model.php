<?php
/**
 * 退款退货
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
class refund_returnModel extends Model{

    /**
     * 取得退单数量
     * @param unknown $condition
     */
    public function getRefundReturn($condition) {
        return $this->table('refund_return')->where($condition)->count();
    }

	/**
	 * 增加退款退货
	 *
	 * @param
	 * @return int
	 */
	public function addRefundReturn($refund_array, $order = array(), $goods = array()) {
	    if (!empty($order) && is_array($order)) {
			$refund_array['order_id'] = $order['order_id'];
			$refund_array['order_sn'] = $order['order_sn'];
			$refund_array['store_id'] = $order['store_id'];
			$refund_array['store_name'] = $order['store_name'];
			$refund_array['buyer_id'] = $order['buyer_id'];
			$refund_array['buyer_name'] = $order['buyer_name'];
	    }
	    if (!empty($goods) && is_array($goods)) {
			$refund_array['goods_id'] = $goods['goods_id'];
			$refund_array['order_goods_id'] = $goods['rec_id'];
			$refund_array['order_goods_type'] = $goods['goods_type'];
			$refund_array['goods_name'] = $goods['goods_name'];
			$refund_array['commis_rate'] = $goods['commis_rate'];
			$refund_array['goods_image'] = $goods['goods_image'];
	    }
	    $refund_array['refund_sn'] = $this->getRefundsn($refund_array['store_id']);
		$refund_id = $this->table('refund_return')->insert($refund_array);
		return $refund_id;
	}

	/**
	 * 订单锁定
	 *
	 * @param
	 * @return bool
	 */
	public function editOrderLock($order_id) {
	    $order_id = intval($order_id);
		if ($order_id > 0) {
    	    $condition = array();
    	    $condition['order_id'] = $order_id;
    		$data = array();
    		$data['lock_state'] = array('exp','lock_state+1');
    		$result = $this->table('order')->where($condition)->update($data);
    		return $result;
		}
		return false;
	}

	/**
	 * 订单解锁
	 *
	 * @param
	 * @return bool
	 */
	public function editOrderUnlock($order_id) {
	    $order_id = intval($order_id);
		if ($order_id > 0) {
    	    $condition = array();
    	    $condition['order_id'] = $order_id;
    	    $condition['lock_state'] = array('egt','1');
    		$data = array();
    		$data['lock_state'] = array('exp','lock_state-1');
    		$data['delay_time'] = time();
    		$result = $this->table('order')->where($condition)->update($data);
    		return $result;
		}
		return false;
	}

	/**
	 * 修改记录
	 *
	 * @param
	 * @return bool
	 */
	public function editRefundReturn($condition, $data) {
		if (empty($condition)) {
			return false;
		}
		if (is_array($data)) {
			$result = $this->table('refund_return')->where($condition)->update($data);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * 平台确认退款处理
	 *
	 * @param
	 * @return bool
	 */
	public function editOrderRefund($refund) {
	    $refund_id = intval($refund['refund_id']);
		if ($refund_id > 0) {
		    Language::read('model_lang_index');
			$order_id = $refund['order_id'];//订单编号
			$field = 'order_id,buyer_id,buyer_name,store_id,order_sn,order_amount,payment_code,order_state,refund_amount';
			$order = $this->table('order')->field($field)->where(array('order_id'=> $order_id))->find();

			$predeposit_model = Model('predeposit');
            $log_array = array();
            $log_array['member_id'] = $order['buyer_id'];
            $log_array['member_name'] = $order['buyer_name'];
            $log_array['amount'] = $refund['refund_amount'];
            $log_array['order_sn'] = $order['order_sn'];
            $state = $predeposit_model->changePd('refund', $log_array);//增加买家可用金额

			$order_state = $order['order_state'];
			$model_trade = Model('trade');
			$order_paid = $model_trade->getOrderState('order_paid');//订单状态20:已付款
			if ($state && $order_state == $order_paid) {
				$log_array = array();
				$log_array['order_id'] = $order_id;
				$log_array['log_role'] = 'system';
				$log_array['log_time'] = time();
        	    $log_array['log_msg'] = '商品全部退款完成取消订单。';
        	    $state = $model_trade->editOrderCancel($order_id, $log_array);//已付款未发货时取消订单
        	}
			if ($state) {
			    $order_array = array();
			    $order_amount = $order['order_amount'];//订单金额
			    $refund_amount = $order['refund_amount']+$refund['refund_amount'];//退款金额
			    $order_array['refund_state'] = ($order_amount-$refund_amount) > 0 ? 1:2;
			    $order_array['refund_amount'] = ncPriceFormat($refund_amount);
			    $order_array['delay_time'] = time();
			    $state = $this->table('order')->where(array('order_id'=> $order_id))->update($order_array);//更新订单退款
        	}
			if ($state && $refund['order_lock'] == '2') {
			    $state = $this->editOrderUnlock($order_id);//订单解锁
			}
			return $state;
		}
		return false;
	}

	/**
	 * 取退款退货记录
	 *
	 * @param
	 * @return array
	 */
	public function getRefundReturnList($condition = array(), $page = '', $fields = '*', $limit = '') {
		$result = $this->table('refund_return')->field($fields)->where($condition)->page($page)->limit($limit)->order('refund_id desc')->select();
		return $result;
	}

	/**
	 * 取退款记录
	 *
	 * @param
	 * @return array
	 */
	public function getRefundList($condition = array(), $page = '') {
	    $condition['refund_type'] = '1';//类型:1为退款,2为退货
		$result = $this->getRefundReturnList($condition, $page);
		return $result;
	}

	/**
	 * 取退货记录
	 *
	 * @param
	 * @return array
	 */
	public function getReturnList($condition = array(), $page = '') {
	    $condition['refund_type'] = '2';//类型:1为退款,2为退货
		$result = $this->getRefundReturnList($condition, $page);
		return $result;
	}

	/**
	 * 退款退货申请编号
	 *
	 * @param
	 * @return array
	 */
	public function getRefundsn($store_id) {
		$result = mt_rand(100,999).substr(100+$store_id,-3).date('ymdHis');
		return $result;
	}

	/**
	 * 取一条记录
	 *
	 * @param
	 * @return array
	 */
	public function getRefundReturnInfo($condition = array(), $fields = '*') {
        return $this->table('refund_return')->where($condition)->field($fields)->find();
	}

	/**
	 * 根据订单取商品的退款退货状态
	 *
	 * @param
	 * @return array
	 */
	public function getGoodsRefundList($order_list = array()) {
	    $order_ids = array();//订单编号数组
	    $order_ids = array_keys($order_list);
	    $model_trade = Model('trade');
	    $condition = array();
	    $condition['order_id'] = array('in', $order_ids);
	    $refund_list = $this->table('refund_return')->where($condition)->order('refund_id desc')->select();
	    $refund_goods = array();//已经提交的退款退货商品
	    if (!empty($refund_list) && is_array($refund_list)) {
    	    foreach ($refund_list as $key => $value) {
    	        $order_id = $value['order_id'];//订单编号
    	        $goods_id = $value['order_goods_id'];//订单商品表编号
    	        if (empty($refund_goods[$order_id][$goods_id])) {
    	            $refund_goods[$order_id][$goods_id] = $value;
    	        }
    	    }
	    }
	    if (!empty($order_list) && is_array($order_list)) {
    	    foreach ($order_list as $key => $value) {
    	        $order_id = $key;
    	        $goods_list = $value['extend_order_goods'];//订单商品
    	        $order_state = $value['order_state'];//订单状态
        	    $order_paid = $model_trade->getOrderState('order_paid');//订单状态20:已付款
        	    $payment_code = $value['payment_code'];//支付方式
        	    if ($order_state == $order_paid && $payment_code != 'offline') {//已付款未发货的非货到付款订单可以申请取消
        	        $order_list[$order_id]['refund'] = '1';
        	    } elseif ($order_state > $order_paid && !empty($goods_list) && is_array($goods_list)) {//已发货后对商品操作
        	        $refund = $this->getRefundState($value);//根据订单状态判断是否可以退款退货
            	    foreach ($goods_list as $k => $v) {
            	        $goods_id = $v['rec_id'];//订单商品表编号
            	        if ($v['goods_pay_price'] > 0) {//实际支付额大于0的可以退款
            	            $v['refund'] = $refund;
            	        }
            	        if (!empty($refund_goods[$order_id][$goods_id])) {
            	            $seller_state = $refund_goods[$order_id][$goods_id]['seller_state'];//卖家处理状态:1为待审核,2为同意,3为不同意
            	            if ($seller_state == 3) {
            	                $order_list[$order_id]['complain'] = '1';//不同意可以发起投诉
            	            } else {
            	                $v['refund'] = '0';//已经存在处理中或同意的商品不能再操作
            	            }
            	            $v['extend_refund'] = $refund_goods[$order_id][$goods_id];
            	        }
            	        $goods_list[$k] = $v;
            	    }
        	    }
    	        $order_list[$order_id]['extend_order_goods'] = $goods_list;
    	    }
	    }
		return $order_list;
	}

	/**
	 * 根据订单判断投诉订单商品是否可退款
	 *
	 * @param
	 * @return array
	 */
	public function getComplainRefundList($order) {
	    $list = array();
	    $refund_list = array();//已退或处理中商品
	    $refund_goods = array();//可退商品
	    if (!empty($order) && is_array($order)) {
            $order_id = $order['order_id'];
            $order_list[$order_id] = $order;
            $order_list = $this->getGoodsRefundList($order_list);
            $order = $order_list[$order_id];
            $goods_list = $order['extend_order_goods'];
            $order_amount = $order['order_amount'];//订单金额
		    $order_refund_amount = $order['refund_amount'];//订单退款金额
            foreach ($goods_list as $k => $v) {
                $goods_id = $v['rec_id'];//订单商品表编号
        		$v['refund_state'] = 3;
                if (!empty($v['extend_refund'])) {
                    $v['refund_state'] = $v['extend_refund']['seller_state'];//卖家处理状态为3,不同意时能退款
                }
                if ($v['refund_state'] > 2) {//可退商品
                    $goods_pay_price = $v['goods_pay_price'];//商品实际成交价
            		if ($order_amount < ($goods_pay_price + $order_refund_amount)) {
            		    $goods_pay_price = $order_amount - $order_refund_amount;
            		    $v['goods_pay_price'] = $goods_pay_price;
            		}
            		$v['goods_refund'] = $v['goods_pay_price'];
                    $refund_goods[$goods_id] = $v;
                } else {//已经存在处理中或同意的商品不能再退款
                    $refund_list[$goods_id] = $v;
                }
            }
		}
		$list = array(
			'refund' => $refund_list,
			'goods' => $refund_goods
			);
		return $list;
	}

	/**
	 * 根据订单状态判断是否可以退款退货
	 *
	 * @param
	 * @return array
	 */
	public function getRefundState($order) {
	    $refund = '0';//默认不允许退款退货
	    $order_state = $order['order_state'];//订单状态
	    $model_trade = Model('trade');
	    $order_shipped = $model_trade->getOrderState('order_shipped');//30:已发货
	    $order_completed = $model_trade->getOrderState('order_completed');//40:已收货
	    switch ($order_state) {
            case $order_shipped:
                $payment_code = $order['payment_code'];//支付方式
                if ($payment_code != 'offline') {//货到付款订单在没确认收货前不能退款退货
                    $refund = '1';
                }
                break;
            case $order_completed:
        	    $order_refund = $model_trade->getMaxDay('order_refund');//15:收货完成后可以申请退款退货
        	    $delay_time = $order['delay_time']+60*60*24*$order_refund;
                if ($delay_time > time()) {
                    $refund = '1';
                }
                break;
            default:
                $refund = '0';
                break;
	    }

	    return $refund;
	}

	/**
	 * 向模板页面输出退款退货状态
	 *
	 * @param
	 * @return array
	 */
	public function getRefundStateArray($type = 'all') {
		Language::read('refund');
		$state_array = array(
			'1' => Language::get('refund_state_confirm'),
			'2' => Language::get('refund_state_yes'),
			'3' => Language::get('refund_state_no')
			);//卖家处理状态:1为待审核,2为同意,3为不同意
		Tpl::output('state_array', $state_array);

		$admin_array = array(
			'1' => '处理中',
			'2' => '待处理',
			'3' => '已完成'
			);//确认状态:1为买家或卖家处理中,2为待平台管理员处理,3为退款退货已完成
		Tpl::output('admin_array', $admin_array);

		$state_data = array(
			'seller' => $state_array,
			'admin' => $admin_array
			);
		if ($type == 'all') return $state_data;//返回所有
		return $state_data[$type];
	}

    /**
     * 退货退款数量
     *
     * @param array $condition
     * @return int
     */
    public function getRefundReturnCount($condition) {
        return $this->table('refund_return')->where($condition)->count();
    }

}