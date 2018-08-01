<?php
/**
 * 直通车管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class member_qqconnectControl extends BaseMemberControl {
	public function __construct() {		
		parent::__construct();
		/**
		 * 读取语言包
		 */
		Language::read('member_member_qqconnect');
		if ($GLOBALS['setting_config']['qq_isuse'] != 1){
			showMessage(Language::get('member_qqconnect_unavailable'),'index.php?act=member_snsindex','html','error');
		}
		Tpl::setLayout('member_pub_layout');
	}
	public function qqbindOp(){
		//获得用户信息		
		$model_member	= Model('member');
		$member_info	= $model_member->infoMember(array('member_id'=>$_SESSION['member_id']),'member_qqopenid,member_qqinfo');
		if (trim($member_info['member_qqinfo'])){
			$member_info['member_qqinfoarr'] = unserialize($member_info['member_qqinfo']);
		}
		Tpl::output('member_info',$member_info);
		//信息输出
		self::profile_menu('qq_bind');
		Tpl::output('menu_sign','qq_bind');
		Tpl::output('menu_sign_url','index.php?act=member_qqconnect&op=qqbind');
		Tpl::output('menu_sign1','qq_bind');
		Tpl::showpage('member_qqbind');
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
				array("input"=>$_POST["new_password"],		"require"=>"true","validator"=>"Length","min"=>6,"max"=>20,"message"=>Language::get('member_qqconnect_password_null')),
				array("input"=>$_POST["confirm_password"],	"require"=>"true","validator"=>"Compare","operator"=>"==","to"=>$_POST["new_password"],"message"=>Language::get('member_qqconnect_input_two_password_again')),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error,'','html','error');
			}
			$update_arr['member_passwd'] = md5(trim($_POST['new_password']));
		}
		$update_arr['member_qqopenid'] = '';
		$update_arr['member_qqinfo'] = '';
		$edit_state		= $model_member->updateMember($update_arr,$_SESSION['member_id']);
		
		if(!$edit_state) {
			showMessage(Language::get('member_qqconnect_password_modify_fail'),'html','error');
		}
		
		session_unset();
		session_destroy();
		showMessage(Language::get('member_qqconnect_unbind_success'),'index.php?act=login&ref_url='.urlencode('index.php?act=member_qqconnect&op=qqbind'));
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
			1=>array('menu_key'=>'qq_bind',	'menu_name'=>$lang['nc_member_path_qq_bind'],	'menu_url'=>'index.php?act=member_qqconnect&op=qqbind'),
		);
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}