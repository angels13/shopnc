<?php
/**
 * APP会员
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class memberControl{

	public function __construct(){
		require_once(BASE_PATH.'/framework/function/client.php');
	}

	public function infoOp(){
		if (!empty($_GET['uid'])){
			$member_info = nc_member_info($_GET['uid'],'uid');
		}elseif(!empty($_GET['user_name'])){
			$member_info = nc_member_info($_GET['user_name'],'user_name');
		}
		return $member_info;
	}
}