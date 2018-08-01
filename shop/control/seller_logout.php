<?php
/**
 * 店铺卖家注销
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

class seller_logoutControl extends BaseSellerControl {

	public function __construct() {
		parent::__construct();
	}

    public function indexOp() {
        $this->logoutOp();
    }

    public function logoutOp() {
        $this->recordSellerLog('注销成功');
        session_destroy();
        showMessage('注销成功', 'index.php?act=seller_login');
    }

}
