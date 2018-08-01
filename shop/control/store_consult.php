<?php
/**
 * 卖家商品咨询管理
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
class store_consultControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_consult_index');
	}

	/**
	 * 商品咨询首页
	 */
	public function indexOp(){
		$this->consult_listOp();
	}

	/**
	 * 商品咨询列表页
	 */
	public function consult_listOp(){
		$consult	= Model('consult');
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$list_consult	= array();
		$search_array	= array();
		$search_array['type']		= $_GET['type'];
		$search_array['store_id']	= "{$_SESSION['store_id']}";
		$list_consult	= $consult->getConsultList($search_array,$page);
		Tpl::output('show_page',$page->show());
		Tpl::output('list_consult',$list_consult);
		$_GET['type']	= empty($_GET['type'])?'consult_list':$_GET['type'];
		self::profile_menu('consult',$_GET['type']);
		Tpl::output('menu_sign','consult_manage');
		Tpl::output('menu_sign_url','index.php?act=store_consult&op=consult_list');
		Tpl::output('menu_sign1',$_GET['type']);
		Tpl::showpage('store_consult_manage');
	}

	/**
	 * 商品咨询删除处理
	 */
	public function drop_consultOp(){
		$consult	= Model('consult');
		$id_array = explode(',',trim($_GET['id']));
		$consult_id = "'".implode("','",$id_array)."'";
		$state	= $consult->dropConsult($consult_id,$_SESSION['store_id']);
		if($state) {
			showDialog(Language::get('store_consult_drop_success'),'reload','succ');
		} else {
			showDialog(Language::get('store_consult_drop_fail'));
		}
	}

	/**
	 * 回复商品咨询表单页
	 */
	public function reply_consultOp(){
		$consult	= Model('consult');
		$list_consult	= array();
		$search_array	= array();
		$search_array['consult_id']	= intval($_GET['id']);
		$search_array['store_id']	= "{$_SESSION['store_id']}";
		$list_consult	= $consult->getConsultList($search_array);
		$consult_info	= $list_consult[0];
		Tpl::output('consult',$consult_info);
		Tpl::showpage('store_consult_form','null_layout');
	}

	/**
	 * 商品咨询回复内容的保存处理
	 */
	public function reply_saveOp(){
		$consult	= Model('consult');
		$consult_id = intval($_POST['consult_id']);
		$input	= array();
		$input['consult_reply']	= $_POST['content'];
		$condtion_arr = array();
		$condtion_arr['store_id'] = "{$_SESSION['store_id']}";
		$condtion_arr['consult_id'] = "{$consult_id}";
		$state	= $consult->replyConsult($input,$condtion_arr);		
		if($state){
			showDialog(Language::get('nc_common_op_succ'),'reload','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
		} else {
			showDialog(Language::get('nc_common_op_fail'));
		}
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @param array 	$array		附加菜单
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='',$array=array()) {		
		Language::read('member_layout');
		$menu_array		= array();
		switch ($menu_type) {
			case 'consult':
				$menu_array	= array(
				1=>array('menu_key'=>'consult_list',	'menu_name'=>Language::get('nc_member_path_all_consult'),			'menu_url'=>'index.php?act=store_consult&op=consult_list'),
				2=>array('menu_key'=>'to_reply',	'menu_name'=>Language::get('nc_member_path_unreplied_consult'),			'menu_url'=>'index.php?act=store_consult&op=consult_list&type=to_reply'),
				3=>array('menu_key'=>'replied',	'menu_name'=>Language::get('nc_member_path_replied_consult'),			'menu_url'=>'index.php?act=store_consult&op=consult_list&type=replied'));
				break;
		}
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
