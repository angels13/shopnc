<?php
/**
 * 积分兑换订单管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class pointorderControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('pointorder');
		/**
		 * 判断系统是否开启积分功能和积分兑换功能
		 */
		if ($GLOBALS['setting_config']['points_isuse'] != 1 || $GLOBALS['setting_config']['pointprod_isuse'] != 1){
			showMessage(Language::get('admin_pointorder_unavailable'),'index.php?act=dashboard&op=welcome','','error');
		}
	}
	/**
	 * 积分兑换列表
	 */
	public function pointorder_listOp(){
		//条件
		$condition_arr = array();
		//兑换单号
		if (trim($_GET['pordersn'])){
			$condition_arr['point_ordersn_like'] = trim($_GET['pordersn']);
		}
		//兑换会员名称
		if (trim($_GET['pbuyname'])){
			$condition_arr['point_buyername_like'] = trim($_GET['pbuyname']);
		}
		if (trim($_GET['porderstate'])){
			$condition_arr['point_orderstatetxt'] = trim($_GET['porderstate']);
		}
		//分页
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		//查询直通车列表
		$pointprod_model = Model('pointorder');
		$order_list = $pointprod_model->getPointOrderList($condition_arr,$page,'simple');
		if (is_array($order_list) && count($order_list)>0){
			foreach ($order_list as $k => $v){
				$order_list[$k]['point_orderstatetext'] = $this->pointorder_state($v['point_orderstate']);
			}
		}

		//信息输出
		Tpl::output('order_list',$order_list);
		Tpl::output('show_page',$page->show());
		Tpl::showpage('pointorder.list');
	}
	/**
	 * 删除积分礼品兑换信息
	 */
	public function order_dropOp(){
		$order_id = intval($_GET['order_id']);
		if (!$order_id){
			showMessage(Language::get('admin_pointorder_parameter_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		$pointorder_model = Model('pointorder');
		//删除操作
		$condition_arr = array();
		$condition_arr['point_orderid_del'] = $order_id;
		$condition_arr['point_orderstate_in'] = '2';//只有取消的订单才能删除
		$result = $pointorder_model->dropPointOrder($condition_arr);
		if($result) {
			//删除兑换礼品信息
			$pointorder_model->dropPointOrderProd(array('prod_orderid_del'=>$order_id));
			//删除兑换地址信息
			$pointorder_model->dropPointOrderAddress(array('address_orderid_del'=>$order_id));
			showMessage(Language::get('admin_pointorder_del_success'),'index.php?act=pointorder&op=pointorder_list');
		} else {
			showMessage(Language::get('admin_pointorder_del_fail'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
	}
	/**
	 * 取消兑换
	 */
	public function order_cancelOp(){
		$order_id = intval($_GET['id']);
		if ($order_id <= 0){
			showMessage(Language::get('admin_pointorder_parameter_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		$pointorder_model = Model('pointorder');
		$condition_arr = array();
		$condition_arr['point_orderid'] = "$order_id";
		$condition_arr['point_order_enablecancel'] = '1';//可取消
		//查询兑换信息
		$order_info = $pointorder_model->getPointOrderInfo($condition_arr,'simple','point_ordersn,point_buyerid,point_buyername,point_allpoint');
		if (!is_array($order_info) || count($order_info)<=0){
			showMessage(Language::get('admin_pointorderd_record_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		//更新运费
		$state = $pointorder_model->updatePointOrder($condition_arr,array('point_orderstate'=>'2'));
		if ($state){
			//退还会员积分
			$points_model =Model('points');
			$insert_arr['pl_memberid'] 		= $order_info['point_buyerid'];
			$insert_arr['pl_membername'] 	= $order_info['point_buyername'];
			$insert_arr['pl_points'] 		= $order_info['point_allpoint'];
			$insert_arr['point_ordersn'] 	= $order_info['point_ordersn'];
			$insert_arr['pl_desc'] 			= Language::get('admin_pointorder_cancel_tip1').$order_info['point_ordersn'].Language::get('admin_pointorder_cancel_tip2');
			$points_model->savePointsLog('pointorder',$insert_arr,true);
			//更改兑换礼品库存
			$prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid'=>$order_id),'','point_goodsid,point_goodsnum');
			if (is_array($prod_list) && count($prod_list)>0){
				$pointprod_model = Model('pointprod');
				foreach ($prod_list as $v){
					$update_arr = array();
					$update_arr['pgoods_storage'] = array('sign'=>'increase','value'=>$v['point_goodsnum']);
					$update_arr['pgoods_salenum'] = array('sign'=>'decrease','value'=>$v['point_goodsnum']);
					$pointprod_model->updatePointProd($update_arr,array('pgoods_id'=>$v['point_goodsid']));
					unset($update_arr);
				}
			}
			showMessage(Language::get('admin_pointorder_cancel_success'),'index.php?act=pointorder&op=pointorder_list');
		}else {
			showMessage(Language::get('admin_pointorder_cancel_fail'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
	}
	/**
	 * 发货
	 */
	public function order_shipOp(){
		$order_id = intval($_GET['id']);
		if ($order_id <= 0){
			showMessage(Language::get('admin_pointorder_parameter_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		$pointorder_model = Model('pointorder');
		$condition_arr = array();
		$condition_arr['point_orderid'] = "$order_id";
		$condition_arr['point_orderstate_in'] = '20,30';//确认付款状态和已经发货状态
		if (chksubmit()){
			$obj_validate = new Validate();
			$validate_arr[] = array("input"=>$_POST["shippingcode"],"require"=>"true","message"=>Language::get('admin_pointorder_ship_code_nullerror'));
			$obj_validate->validateparam = $validate_arr;
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage(Language::get('error').$error,'index.php?act=pointorder&op=pointorder_list','','error');
			}
			//更新发货信息
			$update_arr = array();
			$shippingtime = strtotime(trim($_POST['shippingtime']));
			if ($shippingtime > 0){
				$update_arr['point_shippingtime'] = $shippingtime;
			}else {
				$update_arr['point_shippingtime'] = time();
			}
			$update_arr['point_shippingcode'] = trim($_POST['shippingcode']);
			$update_arr['point_shippingdesc'] = trim($_POST['shippingdesc']);
			$update_arr['point_orderstate']   = '30'; //已经发货
			$state = $pointorder_model->updatePointOrder($condition_arr,$update_arr);
			if ($state){
				showMessage(Language::get('admin_pointorder_ship_success'),'index.php?act=pointorder&op=pointorder_list');
			}else {
				showMessage(Language::get('admin_pointorder_ship_fail'),'index.php?act=pointorder&op=pointorder_list','','error');
			}
		}else {
			//查询订单信息
			$order_info = $pointorder_model->getPointOrderInfo($condition_arr,'simple','point_ordersn,point_buyername,point_shippingtime,point_shippingcode,point_shippingdesc,point_orderstate');
			if (is_array($order_info) && count($order_info)>0){
				Tpl::output('order_info',$order_info);
				Tpl::showpage('pointorder.ship');
			}else {
				Tpl::output('errormsg',Language::get('admin_pointorderd_record_error'));
				Tpl::showpage('pointorder.ship');
			}
		}
	}
	/**
	 * 兑换信息详细
	 */
	public function order_infoOp(){
		$order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('admin_pointorder_parameter_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		//查询订单信息
		$pointorder_model = Model('pointorder');
		$condition_arr['point_orderid'] = $order_id;
		$order_info = $pointorder_model->getPointOrderInfo($condition_arr,'all','*');
		if (!is_array($order_info) || count($order_info) <= 0){
			showMessage(Language::get('admin_pointorderd_record_error'),'index.php?act=pointorder&op=pointorder_list','','error');
		}
		$order_info['point_orderstatetext'] = $this->pointorder_state($order_info['point_orderstate']);
		//兑换商品信息
		$prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid'=>"{$order_id}"),$page);
		Tpl::output('prod_list',$prod_list);
		Tpl::output('order_info',$order_info);
		Tpl::showpage('pointorder.info');
	}
	/**
	 * 获得订单状态描述
	 *
	 */
	public function pointorder_state($order_step){
		$log_array	= array();
		switch ($order_step) {
			case 2:
				$log_array['order_state']	= Language::get('admin_pointorder_state_canceled');
				$log_array['change_state'] = '';
				break;
			case 20:
				$log_array['order_state']	= Language::get('admin_pointorder_state_waitship');
				$log_array['change_state']	= '';
				break;
			case 30:
				$log_array['order_state']	= Language::get('admin_pointorder_state_shipped');
				$log_array['change_state']	= Language::get('admin_pointorder_state_waitreceiving');
				break;
			case 40:
				$log_array['order_state']	= Language::get('admin_pointorder_state_finished');
				$log_array['change_state']	= '';
				break;
			default:
				$log_array['order_state']	= Language::get('admin_pointorder_state_unknown');
				$log_array['change_state']	= Language::get('admin_pointorder_state_unknown');
		}
		return $log_array;
	}
}