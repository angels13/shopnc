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

class store_infoControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index');
	}
	public function indexOp(){
        $model_store = Model('store');
        $model_store_bind_class = Model('store_bind_class');
        $model_store_class = Model('store_class');
        $model_store_grade = Model('store_grade');

        // 店铺信息
        $store_info = $model_store->getStoreInfoByID($_SESSION['store_id']);
        Tpl::output('store_info', $store_info);

        // 店铺分类信息
        $store_class_info = $model_store_class->getOneClass($store_info['sc_id']);
        Tpl::output('store_class_name', $store_class_info['sc_name']);

        // 店铺等级信息
        $store_grade_info = $model_store_grade->getOneGrade($store_info['grade_id']);
        Tpl::output('store_grade_name', $store_grade_info['sg_name']);

        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id'=>$store_info['member_id']));
        Tpl::output('joinin_detail', $joinin_detail);

        $store_bind_class_list = $model_store_bind_class->getStoreBindClassList(array('store_id'=>$_SESSION['store_id']), null);
        $goods_class = H('goods_class') ? H('goods_class') : H('goods_class', true);
        for($i = 0, $j = count($store_bind_class_list); $i < $j; $i++) {
            $store_bind_class_list[$i]['class_1_name'] = $goods_class[$store_bind_class_list[$i]['class_1']]['gc_name'];
            $store_bind_class_list[$i]['class_2_name'] = $goods_class[$store_bind_class_list[$i]['class_2']]['gc_name'];
            $store_bind_class_list[$i]['class_3_name'] = $goods_class[$store_bind_class_list[$i]['class_3']]['gc_name'];
        }
        Tpl::output('store_bind_class_list', $store_bind_class_list);

        Tpl::showpage('store_info');
	}
}
