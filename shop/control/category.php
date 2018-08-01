<?php
/**
 * 前台分类
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

class categoryControl extends BaseHomeControl {
	/**
	 * 分类列表
	 */
	public function indexOp(){
		Language::read('home_category_index');
		$lang	= Language::getLangContent();
		//导航
		$nav_link = array(
			'0'=>array('title'=>$lang['homepage'],'link'=>SHOP_SITE_URL.'/index.php'),
			'1'=>array('title'=>$lang['category_index_goods_class'])
		);
		Tpl::output('nav_link_list',$nav_link);
		
		Tpl::output('html_title',C('site_name').' - '.Language::get('category_index_goods_class'));
		Tpl::showpage('category');
	}
}
