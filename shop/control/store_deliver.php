<?php
/**
 * 发货
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

class store_deliverControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index,deliver');
	}

	/**
	 * 发货列表
	 *
	 */
	public function indexOp() {
	    $model_order = Model('order');
		if (!in_array($_GET['state'],array('deliverno','delivering','delivered'))) $_GET['state'] = 'deliverno';
		$order_state = str_replace(array('deliverno','delivering','delivered'),
		        array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),$_GET['state']);
		$condition = array();
		$condition['store_id'] = $_SESSION['store_id'];
		$condition['order_state'] = $order_state;
		if ($_GET['buyer_name'] != '') {
		    $condition['buyer_name'] = $_GET['buyer_name'];
		}
		if ($_GET['order_sn'] != '') {
		    $condition['order_sn'] = $_GET['order_sn'];
		}
		$if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
		$if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
		$start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
		$end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
		if ($start_unixtime || $end_unixtime) {
		    $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
		}
		$order_list = $model_order->getOrderList($condition,5,'*','order_id desc','',array('order_goods','order_common','member'));
		Tpl::output('order_list',$order_list);
		Tpl::output('show_page',$model_order->showpage());
		self::profile_menu('deliver',$_GET['state']);
		Tpl::showpage('store_order.deliver');
	}

	/**
	 * 发货
	 */
	public function sendOp(){
        $order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('wrong_argument'),'','html','error');
		}

		$model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
		$if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'],array(ORDER_STATE_PAY,ORDER_STATE_SEND));
		if ($if_allow_send) {
		    showMessage(Language::get('wrong_argument'),'','html','error');
		}

		if (chksubmit()){
			try {
                $model_order->beginTransaction();
                $data = array();
                $data['reciver_name'] = $_POST['reciver_name'];
                $data['reciver_info'] = serialize(array('address' => $_POST['reciver_address'],'phone' => $_POST['reciver_phone']));
                $data['deliver_explain'] = $_POST['deliver_explain'];
                $data['daddress_id'] = intval($_POST['daddress_id']);
                $data['shipping_express_id'] = intval($_POST['shipping_express_id']);
                $data['shipping_time'] = TIMESTAMP;
                $condition = array();
                $condition['order_id'] = $order_id;
                $condition['store_id'] = $_SESSION['store_id'];
                $update = $model_order->editOrderCommon($data,$condition);
                if (!$update) {
                    throw new Exception('nc_common_save_fail');
                }
                $data = array();
                $data['shipping_code']  = $_POST['shipping_code'];
                $data['order_state'] = ORDER_STATE_SEND;
                $data['delay_time'] = TIMESTAMP;
                $update = $model_order->editOrder($data,$condition);
                if (!$update) {
                    throw new Exception(L('nc_common_save_fail'));
                }
                $model_order->commit();
			} catch (Exception $e) {
			    $model_order->rollback();
			    showMessage($e->getMessage(),'','html','error');
			}

			//添加订单日志
			$data = array();
			$data['order_id'] = intval($_GET['order_id']);
			$data['log_role'] = 'seller';
			$data['log_user'] = $_SESSION['member_name'];
			$data['log_msg'] = L('order_log_send');
			$model_order->addOrderLog($data);
			showDialog(Language::get('nc_common_save_succ'),$_POST['ref_url'],'succ');
			exit;
		}
        Tpl::output('order_info',$order_info);
		//取发货地址
		$model_daddress = Model('daddress');
		if ($order_info['extend_order_common']['daddress_id'] > 0 ){
			$daddess_info = $model_daddress->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
		}else{
		    //取默认地址
			$daddess_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
			$daddess_info = $daddess_info[0];
		}
		Tpl::output('daddress_info',$daddess_info);

		//快递公司
		$my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
		if (!empty($my_express_list)){
			$my_express_list = explode(',',$my_express_list);
		}
		$express_list  = ($h = H('express')) ? $h : H('express',true);
		Tpl::output('my_express_list',$my_express_list);
		Tpl::output('express_list',$express_list);
		Tpl::output('menu_sign','deliver');
		Tpl::showpage('store_deliver.send');
	}
    
	/**
	 * 编辑收货地址
	 * @return boolean
	 */
	public function buyer_address_editOp() {
	    $order_id = intval($_GET['order_id']);
	    if ($order_id <= 0) return false;
	    $model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $order_common_info['reciver_info'] = @unserialize($order_common_info['reciver_info']);
		Tpl::output('address_info',$order_common_info);

		Tpl::showpage('store_deliver.buyer_address.edit','null_layout');
	}

	/**
	 * 选择发货地址
	 * @return boolean
	 */
	public function send_address_selectOp() {
	    Language::read('deliver');
	    $address_list = Model('daddress')->getAddressList(array('store_id'=>$_SESSION['store_id']));
	    Tpl::output('address_list',$address_list);
	    Tpl::showpage('store_deliver.daddress.select','null_layout');
	}

	/**
	 * 物流跟踪
	 */
	public function search_deliverOp(){
		Language::read('member_member_index');
		$lang	= Language::getLangContent();

		$order_sn	= $_GET['order_sn'];
		if (!is_numeric($order_sn)) showMessage(Language::get('wrong_argument'),'','html','error');
		$model_order	= Model('order');
		$condition['order_sn'] = $order_sn;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
		if (empty($order_info) || $order_info['order_state'] != ORDER_STATE_SEND) {
		    showMessage('未找到信息','','html','error');
		}
		$order_info['state_info'] = orderState($order_info);
		Tpl::output('order_info',$order_info);
		//卖家发货信息
		$daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
		Tpl::output('daddress_info',$daddress_info);

		//取得配送公司代码
		$express = ($express = H('express'))? $express :H('express',true);
		Tpl::output('e_code',$express[$order_info['extend_order_common']['shipping_express_id']]['e_code']);
		Tpl::output('e_name',$express[$order_info['extend_order_common']['shipping_express_id']]['e_name']);
		Tpl::output('e_url',$express[$order_info['extend_order_common']['shipping_express_id']]['e_url']);
		Tpl::output('shipping_code',$order_info['shipping_code']);

		self::profile_menu('search','search');
		Tpl::showpage('store_deliver.detail');
	}

	/**
	 * 延迟收货
	 */
	public function delay_receiveOp(){
	    $order_id = intval($_GET['order_id']);
	    $model_trade = Model('trade');
	    $model_order = Model('order');
	    $condition = array();
	    $condition['order_id'] = $order_id;
	    $condition['store_id'] = $_SESSION['store_id'];
	    $condition['lock_state'] = 0;
	    $order_info = $model_order->getOrderInfo($condition);

	    //取目前系统最晚收货时间
	    $delay_time = $order_info['delay_time'] + intval($model_trade->getMaxDay('order_confirm'))*3600*24;
	    if (chksubmit()) {
	        $delay_date = intval($_POST['delay_date']);
	        if (!in_array($delay_date,array(5,10,15))) {
	            showDialog(Language::get('wrong_argument'),'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
	        }
	        $update = $model_order->editOrder(array('delay_time'=>array('exp','delay_time+'.$delay_date*3600*24)),$condition);
	        if ($update) {
	            //新的最晚收货时间
	            $dalay_date = date('Y-m-d H:i:s',$delay_time+$delay_date*3600*24);
	            showDialog("成功将最晚收货期限延迟到了".$dalay_date.'&emsp;','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();',4);
	        } else {
	            showDialog('延迟失败','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
	        }
        } else {
            $order_info['delay_time'] = $delay_time;
            Tpl::output('order_info',$order_info);
            Tpl::showpage('store_deliver.delay_receive','null_layout');
            exit();
        }
	}

	/**
	 * 从第三方取快递信息
	 *
	 */
	public function get_expressOp(){
        $url = 'http://www.kuaidi100.com/query?type='.$_GET['e_code'].'&postid='.$_GET['shipping_code'].'&id=1&valicode=&temp='.random(4).'&sessionid=&tmp='.random(4);
        import('function.ftp');
        $content = dfsockopen($url);
        $content = json_decode($content,true);
        if ($content['status'] != 200) exit(json_encode(false));
        $content['data'] = array_reverse($content['data']);
        $output = '';
        if (is_array($content['data'])){
            foreach ($content['data'] as $k=>$v) {
                if ($v['time'] == '') continue;
                $output .= '<li>'.$v['time'].'&nbsp;&nbsp;'.$v['context'].'</li>';
            }
        }
        if ($output == '') exit(json_encode(false));
        if (strtoupper(CHARSET) == 'GBK'){
            $output = Language::getUTF8($output);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }
        echo json_encode($output);
	}
	
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='') {
		Language::read('member_layout');
		$menu_array		= array();
		switch ($menu_type) {
			case 'deliver':
				$menu_array = array(
				array('menu_key'=>'deliverno',			'menu_name'=>Language::get('nc_member_path_deliverno'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=deliverno'),
				array('menu_key'=>'delivering',			'menu_name'=>Language::get('nc_member_path_delivering'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
				array('menu_key'=>'delivered',		'menu_name'=>Language::get('nc_member_path_delivered'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
				);
				break;
			case 'search':
				$menu_array = array(
				1=>array('menu_key'=>'nodeliver',			'menu_name'=>Language::get('nc_member_path_deliverno'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=nodeliver'),
				2=>array('menu_key'=>'delivering',			'menu_name'=>Language::get('nc_member_path_delivering'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
				3=>array('menu_key'=>'delivered',		'menu_name'=>Language::get('nc_member_path_delivered'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
				4=>array('menu_key'=>'search',		'menu_name'=>Language::get('nc_member_path_deliver_info'),	'menu_url'=>'###'),
				);
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
