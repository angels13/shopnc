<?php
/**
 * 积分管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class member_pointsControl extends BaseMemberControl {
	public function indexOp(){
		$this->points_logOp();
		exit;
	}
	public function __construct() {
		parent::__construct();
		/**
		 * 读取语言包
		 */
		Language::read('member_member_points');
		/**
		 * 判断系统是否开启积分功能
		 */
		if ($GLOBALS['setting_config']['points_isuse'] != 1){
			showMessage(Language::get('points_unavailable'),'index.php?act=member_snsindex','html','error');
		}
	}
	/**
	 * 积分日志列表
	 */
	public function points_logOp(){
		$condition_arr = array();
		$condition_arr['pl_memberid'] = $_SESSION['member_id'];
		if ($_GET['stage']){
			$condition_arr['pl_stage'] = $_GET['stage'];
		}
		$condition_arr['saddtime'] = strtotime($_GET['stime']);
		$condition_arr['eaddtime'] = strtotime($_GET['etime']);
        if($condition_arr['eaddtime'] > 0) {
            $condition_arr['eaddtime'] += 86400;
        }
		$condition_arr['pl_desc_like'] = $_GET['description'];
		//分页
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		//查询积分日志列表
		$points_model = Model('points');
		$list_log = $points_model->getPointsLogList($condition_arr,$page,'*','');
		//查询会员信息
		$this->get_member_info();	
		//信息输出
		self::profile_menu('points');
		Tpl::output('show_page',$page->show());
		Tpl::output('list_log',$list_log);
		Tpl::output('menu_sign','points');
		Tpl::output('menu_sign_url','index.php?act=member_points');		
		Tpl::showpage('member_points');
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
			1=>array('menu_key'=>'points',	'menu_name'=>$lang['nc_member_path_points'],	'menu_url'=>'index.php?act=member_points'),
		);
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
