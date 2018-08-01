<?php
/**
 * 会员中心——账户概览
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

class memberControl extends BaseMemberControl{
	/**
	 * 会员地址
	 *
	 * @param
	 * @return
	 */
	public function addressOp() {
		/**
		 * 读取语言包
		 */
		Language::read('member_member_index');
		$lang	= Language::getLangContent();
		/**
		 * 实例化模型
		 */
		$address_class = Model('address');
		/**
		 * 判断页面类型
		 */
		if (!empty($_GET['type'])){
			/**
			 * 新增/编辑地址页面
			 */
			if (intval($_GET['id']) > 0){
				/**
				 * 得到地址信息
				 */
				$address_info = $address_class->getOneAddress(intval($_GET['id']));
				if ($address_info['member_id'] != $_SESSION['member_id']){
					showMessage($lang['member_address_wrong_argument'],'index.php?act=member&op=address','html','error');
				}
				/**
				 * 输出地址信息
				 */
				Tpl::output('address_info',$address_info);
			}
			/**
			 * 增加/修改页面输出
			 */
			Tpl::output('type',$_GET['type']);
			Tpl::showpage('address_form','null_layout');
			exit();
		}
		/**
		 * 判断操作类型
		 */
		if (chksubmit()){
			/**
			 * 验证表单信息
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["true_name"],"require"=>"true","message"=>$lang['member_address_receiver_null']),
				array("input"=>$_POST["area_id"],"require"=>"true","validator"=>"Number","message"=>$lang['member_address_wrong_area']),
				array("input"=>$_POST["city_id"],"require"=>"true","validator"=>"Number","message"=>$lang['member_address_wrong_area']),
				array("input"=>$_POST["area_info"],"require"=>"true","message"=>$lang['member_address_area_null']),
				array("input"=>$_POST["address"],"require"=>"true","message"=>$lang['member_address_address_null']),
				array("input"=>$_POST['tel_phone'].$_POST['mob_phone'],'require'=>'true','message'=>$lang['member_address_phone_and_mobile'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}
            $data = array();
            $data['member_id'] = $_SESSION['member_id'];
            $data['true_name'] = $_POST['true_name'];
            $data['area_id'] = intval($_POST['area_id']);
            $data['city_id'] = intval($_POST['city_id']);
            $data['area_info'] = $_POST['area_info'];
            $data['address'] = $_POST['address'];
            $data['tel_phone'] = $_POST['tel_phone'];
            $data['mob_phone'] = $_POST['mob_phone'];

			if (intval($_POST['id']) > 0){
                $rs = $address_class->editAddress($data, array('address_id' => $_POST['id']));
				if (!$rs){
					showDialog($lang['member_address_modify_fail'],'','error');
				}
			}else {
				$rs = $address_class->addAddress($data);
				if (!$rs){
					showDialog($lang['member_address_add_fail'],'','error');
				}
			}
			showDialog($lang['nc_common_op_succ'],'reload','succ','CUR_DIALOG.close()');
		}
		$del_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0 ;
		if ($del_id > 0){
			$rs = $address_class->delAddress(array('address_id'=>$del_id,'member_id'=>$_SESSION['member_id']));
			if ($rs){
				showDialog(Language::get('member_address_del_succ'),'index.php?act=member&op=address','succ');
			}else {
				showDialog(Language::get('member_address_del_fail'),'','error');
			}
		}
		$address_list = $address_class->getAddressList(array('member_id'=>$_SESSION['member_id']));
		/**
		 * 页面输出
		 */
		self::profile_menu('address','address');
		Tpl::output('menu_sign','address');
		Tpl::output('address_list',$address_list);
		Tpl::output('menu_sign_url','index.php?act=member&op=address');
		Tpl::output('menu_sign1','address_list');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('address_index');
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='') {
		/**
		 * 读取语言包
		 */
		Language::read('member_layout');
		$menu_array	= array();
		switch ($menu_type) {
			case 'address':
				$menu_array = array(
				1=>array('menu_key'=>'address','menu_name'=>Language::get('nc_member_path_address_list'),	'menu_url'=>'index.php?act=member&op=address'));
				break;
			case 'member_order':
				$menu_array = array(
				1=>array('menu_key'=>'member_order','menu_name'=>Language::get('nc_member_path_order_list'),	'menu_url'=>'index.php?act=member_order'),
				2=>array('menu_key'=>'buyer_refund','menu_name'=>Language::get('nc_member_path_buyer_refund'),	'menu_url'=>'index.php?act=member_refund'),
				3=>array('menu_key'=>'buyer_return','menu_name'=>Language::get('nc_member_path_buyer_return'),	'menu_url'=>'index.php?act=member_return'));
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
