<?php
/**
 * cms首页
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class indexControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

	public function indexOp(){
        $model_mb_ad = Model('mb_ad');
        $model_mb_home = Model('mb_home');

        $datas = array();

        //广告
        $adv_list = array();
        $mb_ad_list = $model_mb_ad->getMbAdList(array(), null, 'link_sort asc');
        foreach ($mb_ad_list as $value) {
            $adv = array();
            $adv['image'] = $value['link_pic_url'];
            $adv['keyword'] = $value['link_keyword'];
            $adv_list[] = $adv;
        }
        $datas['adv_list'] = $adv_list;

        //首页
        $home_type1_list = array();
        $home_type2_list = array();
        $mb_home_list = $model_mb_home->getMbHomeList(array(), null, 'h_sort asc');
        foreach ($mb_home_list as $value) {
            $home = array();
            $home['image'] = $value['h_img_url'];
            $home['title'] = $value['h_title'];
            $home['desc'] = $value['h_desc'];
            $home['keyword'] = $value['h_keyword'];
            if($value['h_type'] == 'type1') {
                $home['keyword1'] = $value['h_multi_keyword'];
                $home_type1_list[] = $home;
            } else {
                $home_type2_list[] = $home;
            }
        }
        $datas['home1'] = $home_type1_list;
        $datas['home2'] = $home_type2_list;

        output_data($datas);
	}
}
