<?php
/**
 * 会员中心——积分兑换信息
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class member_pointorderControl extends BaseMemberControl{
	public function __construct() {
		parent::__construct();
		/**
		 * 读取语言包
		 */
		Language::read('member_pointorder');
		/**
		 * 判断系统是否开启积分和积分兑换功能
		 */
		if ($GLOBALS['setting_config']['points_isuse'] != 1 || $GLOBALS['setting_config']['pointprod_isuse'] != 1){
			showMessage(Language::get('member_pointorder_unavailable'),'index.php?act=member_snsindex','html','error');
		}
	}
	public function indexOp() {
		$this->orderlistOp();
	}
	/**
	 * 兑换信息列表
	 */
	public function orderlistOp() {
		//条件
		$condition_arr = array();
		$condition_arr['point_buyerid'] = $_SESSION['member_id'];
		//分页
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		//兑换信息列表
		$pointorder_model = Model('pointorder');
		$order_list = $pointorder_model->getPointOrderList($condition_arr,$page,'simple');
		$order_idarr = array();
		$order_listnew = array();
		if (is_array($order_list) && count($order_list)>0){
			foreach ($order_list as $k => $v){
				$v['point_orderstatetext'] = $this->pointorder_state($v['point_orderstate']);
				$order_idarr[] = $v['point_orderid'];
				$order_listnew[$v['point_orderid']] = $v;
			}
		}
		//查询兑换商品
		if (is_array($order_idarr) && count($order_idarr)>0){
			$order_idstr = implode(',',$order_idarr);
			$prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid_in'=>$order_idstr),'');
			if (is_array($prod_list) && count($prod_list)>0){
				foreach ($prod_list as $v){
					if (isset($order_listnew[$v['point_orderid']])){
						$v['point_goodsimage'] = ATTACH_POINTPROD.DS.str_ireplace('.', '_small.', $v['point_goodsimage']);
						$order_listnew[$v['point_orderid']]['prodlist'][] = $v;
					}
				}
			}
		}
		//信息输出
		Tpl::output('payment_list',$payment_list);
		Tpl::output('order_list',$order_listnew);
		Tpl::output('page',$page->show());
		//查询会员信息
		$this->get_member_info();
		self::profile_menu('pointorder','orderlist');
		Tpl::output('menu_sign','pointorder');
		Tpl::output('menu_sign_url','index.php?act=member_pointorder&op=orderlist');
		Tpl::output('menu_sign1','pointorder_list');
		Tpl::showpage('member_pointorder');
	}
	/**
	 * 	取消兑换
	 */
	public function cancel_orderOp(){
		$order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('member_pointorder_parameter_error'),'index.php?act=member_pointorder','html','error');
		}
		$pointorder_model = Model('pointorder');
		$condition_arr = array();		
		$condition_arr['point_orderid'] = "$order_id";
		$condition_arr['point_buyerid'] = $_SESSION['member_id'];
		$condition_arr['point_order_enablecancel'] = '1';//可取消
		//查询兑换信息
		$order_info = $pointorder_model->getPointOrderInfo($condition_arr,'simple','point_ordersn,point_buyerid,point_buyername,point_allpoint');
		if (!is_array($order_info) || count($order_info)<=0){
			showMessage(Language::get('member_pointorder_record_error'),'index.php?act=member_pointorder','html','error');
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
			$insert_arr['pl_desc'] 			= Language::get('member_pointorder_cancel_tip1').$order_info['point_ordersn'].Language::get('member_pointorder_cancel_tip2');
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
			showMessage(Language::get('member_pointorder_cancel_success'),'index.php?act=member_pointorder');
		}else {
			showMessage(Language::get('member_pointorder_cancel_fail'),'index.php?act=member_pointorder','html','error');
		}
	}
	/**
	 * 确认收货
	 */
	public function receiving_orderOp(){
		$order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('member_pointorder_parameter_error'),'index.php?act=member_pointorder','html','error');
		}
		$pointorder_model = Model('pointorder');
		$condition_arr = array();		
		$condition_arr['point_orderid'] = "$order_id";
		$condition_arr['point_buyerid'] = $_SESSION['member_id'];
		$condition_arr['point_orderstate'] = '30';//待收货
		//更新运费
		$state = $pointorder_model->updatePointOrder($condition_arr,array('point_orderstate'=>'40','point_finnshedtime'=>time()));
		if ($state){
			showMessage(Language::get('member_pointorder_confirmreceiving_success'),'index.php?act=member_pointorder');
		}else {
			showMessage(Language::get('member_pointorder_confirmreceiving_fail'),'index.php?act=member_pointorder','html','error');
		}
	}
	/**
	 * 兑换信息详细
	 */
	public function order_infoOp(){
		$order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('member_pointorder_parameter_error'),'index.php?act=member_pointorder','html','error');
		}
		//查询订单信息
		$pointorder_model = Model('pointorder');
		$condition_arr['point_orderid'] = $order_id;
		$condition_arr['point_buyerid'] = $_SESSION['member_id'];
		$order_info = $pointorder_model->getPointOrderInfo($condition_arr,'all','*');
		if (!is_array($order_info) || count($order_info) <= 0){
			showMessage(Language::get('member_pointorder_record_error'),'index.php?act=member_pointorder','html','error');
		}
		$order_info['point_orderstatetext'] = $this->pointorder_state($order_info['point_orderstate']);
		//兑换商品信息
		$prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid'=>"{$order_id}"),$page);
		Tpl::output('prod_list',$prod_list);
		Tpl::output('order_info',$order_info);
		//查询会员信息
		$this->get_member_info();
		//信息输出
//		self::profile_menu('pointorderinfo','orderinfo');
//		Tpl::output('menu_sign','pointorder');
//		Tpl::output('menu_sign_url','index.php?act=member_pointorder&op=orderlist');
//		Tpl::output('menu_sign1','pointorder_info');
		Tpl::output('left_show','order_view');		
		Tpl::showpage('member_pointorder_info');
	}
	/**
	 * 获得订单状态描述
	 *
	 */
	public function pointorder_state($order_step){
		$log_array	= array();
		switch ($order_step) {
			case 2:
				$log_array['order_state']	= Language::get('member_pointorder_state_canceled');
				$log_array['change_state'] = '';
				break;
			case 20:
				$log_array['order_state']	= Language::get('member_pointorder_state_waitship');
				$log_array['change_state']	= '';
				break;
			case 30:
				$log_array['order_state']	= Language::get('member_pointorder_state_shipped');
				$log_array['change_state']	= Language::get('member_pointorder_state_waitreceiving');
				break;
			case 40:
				$log_array['order_state']	= Language::get('member_pointorder_state_finished');
				$log_array['change_state']	= '';
				break;
			default:
				$log_array['order_state']	= Language::get('member_pointorder_state_unknown');
				$log_array['change_state']	= Language::get('member_pointorder_state_unknown');
		}
		return $log_array;
	}
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='') {
		$menu_array	= array();
		switch ($menu_type) {
			case 'pointorder':
				$menu_array	= array(
					1=>array('menu_key'=>'orderlist','menu_name'=>Language::get('member_pointorder_list_title'),	'menu_url'=>'index.php?act=member_pointorder&op=orderlist')
				);
				break;
			case 'pointorderinfo':
				$menu_array	= array(
					1=>array('menu_key'=>'orderlist','menu_name'=>Language::get('nc_member_path_pointorder_list'),	'menu_url'=>'index.php?act=member_pointorder&op=orderlist'),
					2=>array('menu_key'=>'orderinfo','menu_name'=>Language::get('nc_member_path_pointorder_info'),	'')
				);
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}