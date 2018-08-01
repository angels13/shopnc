<?php
/**
 * 客服中心
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

class store_callcenterControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index');
	}
	public function indexOp(){
		$store_info = Model('store')->field('store_presales,store_aftersales,store_workingtime')->find($_SESSION['store_id']);
		if(!empty($store_info['store_presales'])){
			$store_info['store_presales']	= unserialize($store_info['store_presales']);
		}
		if(!empty($store_info['store_aftersales'])){
			$store_info['store_aftersales']	= unserialize($store_info['store_aftersales']);
		}
		Tpl::output('storeinfo', $store_info);
		
		Tpl::output('menu_sign','store_setting');
		$this->profile_menu('store_callcenter');
		Tpl::showpage('store_callcenter');
	}
	/**
	 * 保存
	 */
	public function saveOp(){
		if(chksubmit()){
			$update = array();
			$i=0;
			if(is_array($_POST['pre']) && !empty($_POST['pre'])){
				foreach($_POST['pre'] as $val){
					if(empty($val['name']) || empty($val['type']) || empty($val['num'])) continue;
					$update['store_presales'][$i]['name']	= $val['name'];
					$update['store_presales'][$i]['type']	= intval($val['type']);
					$update['store_presales'][$i]['num']	= $val['num'];
					$i++;
				}
				$update['store_presales'] = serialize($update['store_presales']);
			}else{
				$update['store_presales'] = serialize(null);
			}
			
			$i=0;
			if(is_array($_POST['after']) && !empty($_POST['after'])){
				foreach($_POST['after'] as $val){
					if(empty($val['name']) || empty($val['type']) || empty($val['num'])) continue;
					$update['store_aftersales'][$i]['name']	= $val['name'];
					$update['store_aftersales'][$i]['type']	= intval($val['type']);
					$update['store_aftersales'][$i]['num']	= $val['num'];
					$i++;
				}
				$update['store_aftersales'] = serialize($update['store_aftersales']);
			}else{
				$update['store_aftersales'] = serialize(null);
			}
			
			$update['store_workingtime'] = $_POST['working_time'];
			$update['store_id']	= $_SESSION['store_id'];
			Model()->table('store')->update($update);
			showDialog(Language::get('nc_common_save_succ'), 'index.php?act=store_callcenter', 'succ');
		}
	}
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_key) {
		$menu_array	= array(
			1=>array('menu_key'=>'store_callcenter','menu_name'=>Language::get('nc_member_path_store_callcenter'),'menu_url'=>'index.php?act=store_callcenter'),
		);
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
