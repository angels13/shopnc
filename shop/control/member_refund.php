<?php
/**
 * 买家退款
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

class member_refundControl extends BaseMemberControl {
	public function __construct(){
		parent::__construct();
		Language::read('member_member_index');
		$model_refund = Model('refund_return');
		$model_refund->getRefundStateArray();
	}
	/**
	 * 添加订单商品部分退款
	 *
	 */
	public function add_refundOp(){
		$model_order = Model('order');
		$model_refund = Model('refund_return');
		$order_id = intval($_GET['order_id']);
		$goods_id = intval($_GET['goods_id']);
		$condition = array();
		$condition['buyer_id'] = $_SESSION['member_id'];
		$condition['order_id'] = $order_id;
		$order_list = $model_order->getOrderList($condition);
		$order = $order_list[0];
		Tpl::output('order',$order);
		$order_id = $order['order_id'];

		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['rec_id'] = $goods_id;//订单商品表编号
		$goods_list = $model_order->getOrderGoodsList($condition);
		$goods = $goods_list[0];
		$goods_pay_price = $goods['goods_pay_price'];//商品实际成交价
		$order_amount = $order['order_amount'];//订单金额
		$order_refund_amount = $order['refund_amount'];//订单退款金额
		if ($order_amount < ($goods_pay_price + $order_refund_amount)) {
		    $goods_pay_price = $order_amount - $order_refund_amount;
		    $goods['goods_pay_price'] = $goods_pay_price;
		}
		Tpl::output('goods',$goods);

		$goods_id = $goods['rec_id'];
		$condition = array();
		$condition['buyer_id'] = $order['buyer_id'];
		$condition['order_id'] = $order['order_id'];
		$condition['order_goods_id'] = $goods_id;
		$condition['seller_state'] = array('lt','3');
		$refund_list = $model_refund->getRefundReturnList($condition);
		$refund = array();
		if (!empty($refund_list) && is_array($refund_list)) {
			$refund = $refund_list[0];
		}
		if (chksubmit() && $goods_id > 0){
		    $refund_state = $model_refund->getRefundState($order);//根据订单状态判断是否可以退款退货
			if ($refund['refund_id'] > 0 || $refund_state != 1) {//检查订单状态,防止页面刷新不及时造成数据错误
				showDialog(Language::get('wrong_argument'),'reload','error','CUR_DIALOG.close();');
			}
			$refund_array = array();
			$refund_amount = floatval($_POST['refund_amount']);//退款金额
			if (($refund_amount < 0) || ($refund_amount > $goods_pay_price)) {
			    $refund_amount = $goods_pay_price;
			}
			$goods_num = intval($_POST['goods_num']);//退货数量
			if (($goods_num < 0) || ($goods_num > $goods['goods_num'])) {
			    $goods_num = 1;
			}
			$model_trade = Model('trade');
			$order_shipped = $model_trade->getOrderState('order_shipped');//订单状态30:已发货
			if ($order['order_state'] == $order_shipped) {
			    $refund_array['order_lock'] = '2';//锁定类型:1为不用锁定,2为需要锁定
			}
			$refund_array['refund_type'] = $_POST['refund_type'];//类型:1为退款,2为退货
			$refund_array['return_type'] = '2';//退货类型:1为不用退货,2为需要退货
			if ($refund_array['refund_type'] != '2') {
			    $refund_array['refund_type'] = '1';
			    $refund_array['return_type'] = '1';
			}
			$refund_array['seller_state'] = '1';//状态:1为待审核,2为同意,3为不同意
			$refund_array['refund_amount'] = ncPriceFormat($refund_amount);
			$refund_array['goods_num'] = $goods_num;
			$refund_array['buyer_message'] = $_POST['buyer_message'];
			$refund_array['add_time'] = time();
			$state = $model_refund->addRefundReturn($refund_array,$order,$goods);

			if ($state) {
    			if ($order['order_state'] == $order_shipped) {
    			    $model_refund->editOrderLock($order_id);
    			}
				showDialog(Language::get('nc_common_save_succ'),'reload','succ','CUR_DIALOG.close();');
			} else {
				showDialog(Language::get('nc_common_save_fail'),'reload','error','CUR_DIALOG.close();');
			}
		}
		Tpl::showpage('member_refund_add','null_layout');
	}
	/**
	 * 添加全部退款即取消订单
	 *
	 */
	public function add_refund_allOp(){
		$model_order = Model('order');
		$model_trade = Model('trade');
		$model_refund = Model('refund_return');
		$order_id = intval($_GET['order_id']);
		$condition = array();
		$condition['buyer_id'] = $_SESSION['member_id'];
		$condition['order_id'] = $order_id;
		$order_list = $model_order->getOrderList($condition);
		$order = $order_list[0];
		Tpl::output('order',$order);
		$order_amount = $order['order_amount'];//订单金额
		$condition = array();
		$condition['buyer_id'] = $order['buyer_id'];
		$condition['order_id'] = $order['order_id'];
		$condition['goods_id'] = '0';
		$condition['seller_state'] = array('lt','3');
		$refund_list = $model_refund->getRefundReturnList($condition);
		$refund = array();
		if (!empty($refund_list) && is_array($refund_list)) {
			$refund = $refund_list[0];
		}
		if (chksubmit()) {
		    $order_paid = $model_trade->getOrderState('order_paid');//订单状态20:已付款
		    $payment_code = $order['payment_code'];//支付方式
			if ($refund['refund_id'] > 0 || $order['order_state'] != $order_paid || $payment_code == 'offline') {//检查订单状态,防止页面刷新不及时造成数据错误
				showDialog(Language::get('wrong_argument'),'reload','error','CUR_DIALOG.close();');
			}
			$refund_array = array();
			$refund_array['refund_type'] = '1';//类型:1为退款,2为退货
			$refund_array['seller_state'] = '1';//状态:1为待审核,2为同意,3为不同意
			$refund_array['order_lock'] = '2';//锁定类型:1为不用锁定,2为需要锁定
			$refund_array['goods_id'] = '0';
			$refund_array['order_goods_id'] = '0';
			$refund_array['goods_name'] = '订单商品全部退款';
			$refund_array['refund_amount'] = ncPriceFormat($order_amount);
			$refund_array['buyer_message'] = $_POST['buyer_message'];
			$refund_array['add_time'] = time();
			$state = $model_refund->addRefundReturn($refund_array,$order);

			if ($state) {
			    $model_refund->editOrderLock($order_id);
				showDialog(Language::get('nc_common_save_succ'),'reload','succ','CUR_DIALOG.close();');
			} else {
				showDialog(Language::get('nc_common_save_fail'),'reload','error','CUR_DIALOG.close();');
			}
		}
	    Tpl::showpage('member_refund_all','null_layout');
	}
	/**
	 * 退款记录列表页
	 *
	 */
	public function indexOp(){
		$model_refund = Model('refund_return');
		$condition = array();
		$condition['buyer_id'] = $_SESSION['member_id'];

		$keyword_type = array('order_sn','refund_sn','goods_name');
		if (trim($_GET['key']) != '' && in_array($_GET['type'],$keyword_type)){
			$type = $_GET['type'];
			$condition[$type] = array('like','%'.$_GET['key'].'%');
		}
		if (trim($_GET['add_time_from']) != '' || trim($_GET['add_time_to']) != ''){
			$add_time_from = strtotime(trim($_GET['add_time_from']));
			$add_time_to = strtotime(trim($_GET['add_time_to']));
			if ($add_time_from !== false || $add_time_to !== false){
				$condition['add_time'] = array('time',array($add_time_from,$add_time_to));
			}
		}
		$refund_list = $model_refund->getRefundList($condition,10);
		//查询会员信息
		$this->get_member_info();
		Tpl::output('refund_list',$refund_list);
		Tpl::output('show_page',$model_refund->showpage());
		self::profile_menu('member_order','buyer_refund');
		Tpl::output('menu_sign','myorder');
		Tpl::output('menu_sign_url','index.php?act=member_order');
		Tpl::output('menu_sign1','buyer_refund');
		Tpl::showpage('member_refund');
	}
	/**
	 * 退款记录查看
	 *
	 */
	public function viewOp(){
		$model_refund = Model('refund_return');
		$condition = array();
		$condition['buyer_id'] = $_SESSION['member_id'];
		$condition['refund_id'] = intval($_GET['refund_id']);
		$refund_list = $model_refund->getRefundList($condition);
		$refund = $refund_list[0];
		Tpl::output('refund',$refund);
		Tpl::showpage('member_refund_view','null_layout');
	}
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='') {
		$menu_array = array();
		switch ($menu_type) {
			case 'member_order':
				$menu_array = array(
				array('menu_key'=>'member_order','menu_name'=>Language::get('nc_member_path_order_list'),	'menu_url'=>'index.php?act=member_order'),
				array('menu_key'=>'buyer_refund','menu_name'=>Language::get('nc_member_path_buyer_refund'),	'menu_url'=>'index.php?act=member_refund'),
				array('menu_key'=>'buyer_return','menu_name'=>Language::get('nc_member_path_buyer_return'),	'menu_url'=>'index.php?act=member_return'));
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
