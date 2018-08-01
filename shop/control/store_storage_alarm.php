<?php
/**
 * 会员中心——我是卖家
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

class store_storage_alarmControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
	}
	/**
	 * 卖家商品分类
	 *
	 * @param 
	 * @return 
	 */
	public function indexOp() {
        $model_store = Model('store');
        if (chksubmit()) {
            $store_storage_alarm = intval(abs($_POST['store_storage_alarm']));
            $model_store->editStore(array('store_storage_alarm'=>$store_storage_alarm),array('store_id'=>$_SESSION['store_id']));
            showDialog(L('nc_common_save_succ'),'reload','succ');
        }
        Tpl::output('store_storage_alarm',$this->store_info['store_storage_alarm']);
        $this->profile_menu('store_storage_alarm', 'store_storage_alarm');
		Tpl::output('menu_sign','store_storage_alarm');
		Tpl::showpage('store_storage_alarm.index');
	}


	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='') {
		$menu_array		= array();
		switch ($menu_type) {
			case 'store_storage_alarm':
				$menu_array = array(
				1=>array('menu_key'=>'store_storage_alarm','menu_name'=>'库存警报',	'menu_url'=>urlShop('store_storage_alarm', 'index')));
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
