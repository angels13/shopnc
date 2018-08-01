<?php
/**
 * 我的订单
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

class member_orderControl extends mobileMemberControl {

	public function __construct(){
		parent::__construct();
	}

    /**
     * 订单列表
     */
    public function order_listOp() {
		$model_order = Model('order');

        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];

        $order_list_array = $model_order->getOrderList($condition, $this->page, '*', 'order_id desc','', array('order_goods'));

        $order_group_list = array();
        $order_pay_sn_array = array();
        foreach ($order_list_array as $value) {
            //显示取消订单
            $value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$value);
            //显示收货
            $value['if_receive'] = $model_order->getOrderOperateState('receive',$value);
            //显示锁定中
            $value['if_lock'] = $model_order->getOrderOperateState('lock',$value);
            //显示物流跟踪
            $value['if_deliver'] = $model_order->getOrderOperateState('deliver',$value);

            $order_group_list[$value['pay_sn']]['order_list'][] = $value;

            //如果有在线支付且未付款的订单则显示合并付款链接
            if ($value['order_state'] == ORDER_STATE_NEW) {
                $order_group_list[$value['pay_sn']]['pay_amount'] += $value['order_amount'];
            }
            $order_group_list[$value['pay_sn']]['add_time'] = $value['add_time'];

            //记录一下pay_sn，后面需要查询支付单表
            $order_pay_sn_array[] = $value['pay_sn'];
        }

        $new_order_group_list = array();
        foreach ($order_group_list as $key => $value) {
            $value['pay_sn'] = strval($key);
            $new_order_group_list[] = $value;
        }

        $page_count = $model_order->gettotalpage();

        output_data(array('order_group_list' => $new_order_group_list), mobile_page($page_count));
    }

    /**
     * 取消订单
     */
    public function order_cancelOp() {
        $extend_msg = '其它原因';
        $this->change_order_state('order_cancel', $extend_msg);
    }

    /**
     * 订单确认收货
     */
    public function order_receiveOp() {
        $this->change_order_state('order_receive');
    }

    /**
     * 修改订单状态
     */
	private function change_order_state($state_type, $extend_msg = '') {
        $order_id = intval($_POST['order_id']);

        $model_order = Model('order');

		$condition = array();
		$condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
		$order_info	= $model_order->getOrderInfo($condition);

        $result = $model_order->memberChangeState($state_type, $order_info, $this->member_info['member_id'], $this->member_info['member_name'], $extend_msg);

        if(empty($result['error'])) {
            output_data('1');
        } else {
            output_error($result['error']);
        }
    }


}
