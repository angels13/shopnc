<?php
/**
 * 清理缓存
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
class cacheControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('cache');
	}

	/**
	 * 清理缓存
	 */
	public function clearOp(){
		$lang	= Language::getLangContent();

		if (chksubmit()){

			//清理所有缓存
			if($_POST['cls_full']==1){
				H('setting',true);H('goods_class',true);H('seo',true);H('goods_class_seo',true);
				H('class_tag',true);H('groupbuy',true);H('nav',true);H('express',true);
				H('store_class',true);
				H('store_grade',true);
				H('circle_level',true);
				Model('adv')->makeApAllCache();
				Model('web_config')->getWebHtml('index',1);
				delCacheFile('fields');
				delCacheFile('index');
		        showMessage($lang['cache_cls_ok']);exit;
			}

			//清理基本缓存
			if (@in_array('setting',$_POST['cache'])){
				H('setting',true);
			}

			//清理商品分类，商品发布第一步中的搜索 缓存
			if (@in_array('goodsclass',$_POST['cache'])){
				H('goods_class',true);H('class_tag',true);
			}

			//清理广告 缓存
			if (@in_array('adv',$_POST['cache'])){
				Model('adv')->makeApAllCache();
			}

			//清理团购地区、分类、价格区间 缓存
			if (@in_array('groupbuy',$_POST['cache'])){
				H('groupbuy',true);
			}

			//清理底部导航 缓存
			if (@in_array('nav',$_POST['cache'])){
				H('nav',true);
			}

			//清理首页缓存
			if (@in_array('index',$_POST['cache'])){
				Model('web_config')->getWebHtml('index',1);
		        delCacheFile('index');
			}

			//清理TABLE缓存
			if (@in_array('table',$_POST['cache'])){
		        delCacheFile('fields');
			}

			//清理SEO缓存
			if (@in_array('seo',$_POST['cache'])){
				H('seo',true);H('goods_class_seo',true);
			}

			//清理快递公司
			if (@in_array('express',$_POST['cache'])){
				H('express',true);
			}

			//清理店铺分类
			if (@in_array('store_class',$_POST['cache'])){
				H('store_class',true);
			}

			//清理店铺等级
			if (@in_array('store_grade',$_POST['cache'])){
				H('store_grade',true);
			}

			// Circle Member Level Cache Clearner
			if (@in_array('circle_level', $_POST['cache'])){
				H('circle_level',true);
			}
			
			$this->log(L('cache_cls_operate'));
			showMessage($lang['cache_cls_ok']);
		}

		Tpl::showpage('cache.clear');
	}

}
