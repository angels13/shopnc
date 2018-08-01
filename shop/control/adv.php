<?php
/**
 * 广告展示
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
class advControl {
    /**
	 * 
	 * 广告展示
	 */
	public function advshowOp(){
		import('function.adv');
		$ap_id = intval($_GET['ap_id']);
		echo advshow($ap_id,'js');
	}
}