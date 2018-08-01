<?php
/**
 * 新浪微博绑定管理页面
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class member_sconnectControl extends BaseMemberControl {
	/**
	 * 卖家店铺是否审核的判断
	 *
	 * @param 
	 * @return 
	 */	
	public function __construct() {		
		parent::__construct();
		/**
		 * 读取语言包
		 */
		Language::read('member_member_sconnect');
		if ($GLOBALS['setting_config']['sina_isuse'] != 1){
			showMessage(Language::get('member_sconnect_unavailable'),'index.php?act=member_snsindex','html','error');
		}
		Tpl::setLayout('member_pub_layout');
	}
	public function sinabindOp(){
		//获得用户信息		
		$model_member	= Model('member');
		$member_info	= $model_member->infoMember(array('member_id'=>$_SESSION['member_id']),'member_sinaopenid,member_sinainfo');
		if (trim($member_info['member_sinainfo'])){
			$member_info['member_sinainfoarr'] = unserialize($member_info['member_sinainfo']);
		}
		Tpl::output('member_info',$member_info);
		//信息输出
		self::profile_menu('sina_bind');
		Tpl::output('menu_sign','sina_bind');
		Tpl::output('menu_sign_url','index.php?act=member_sconnect&op=sinabind');
		Tpl::output('menu_sign1','sina_bind');
		Tpl::showpage('member_sinabind');
	}
	public function unbindOp(){
		//修改密码
		$model_member	= Model('member');
		$update_arr = array();
		if ($_POST['is_editpw'] == 'yes'){
			/**
			 * 填写密码信息验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["new_password"],		"require"=>"true","validator"=>"Length","min"=>6,"max"=>20,"message"=>Language::get('member_sconnect_password_null')),
				array("input"=>$_POST["confirm_password"],	"require"=>"true","validator"=>"Compare","operator"=>"==","to"=>$_POST["new_password"],"message"=>Language::get('member_sconnect_input_two_password_again')),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error,'','html','error');
			}
			$update_arr['member_passwd'] = md5(trim($_POST['new_password']));
		}
		$update_arr['member_sinaopenid'] = '';
		$update_arr['member_sinainfo'] = '';
		$edit_state		= $model_member->updateMember($update_arr,$_SESSION['member_id']);
		
		if(!$edit_state) {
			showMessage(Language::get('member_sconnect_password_modify_fail'),'','html','error');
		}
		session_unset();
		session_destroy();
		showMessage(Language::get('member_sconnect_unbind_success'),'index.php?act=login&ref_url='.urlencode('index.php?act=member_sconnect&op=sinabind'));
	}
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @param array 	$array		附加菜单
	 * @return 
	 */
	private function profile_menu($menu_key='',$array=array()) {
		Language::read('member_layout');
		$lang	= Language::getLangContent();
		$menu_array		= array();
		$menu_array = array(
			1=>array('menu_key'=>'sina_bind',	'menu_name'=>$lang['nc_member_path_sina_bind'],	'menu_url'=>'index.php?act=member_sconnect&op=sina_bind'),
		);
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}