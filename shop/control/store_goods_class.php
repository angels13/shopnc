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

class store_goods_classControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index');
	}
	/**
	 * 卖家商品分类
	 *
	 * @param 
	 * @return 
	 */
	public function indexOp() {
		$model_class	= Model('my_goods_class');

		if($_GET['type'] == 'ok') {
			if(intval($_GET['class_id']) != 0) {
				$class_info	= $model_class->getClassInfo(array('stc_id'=>intval($_GET['class_id'])));
				Tpl::output('class_info',$class_info);
			}
			if(intval($_GET['top_class_id']) != 0) {
				Tpl::output('class_info',array('stc_parent_id'=>intval($_GET['top_class_id'])));
			}
			$goods_class		= $model_class->getClassList(array('store_id'=>$_SESSION['store_id'],'stc_top'=>1));
			Tpl::output('goods_class',$goods_class);
			Tpl::showpage('store_goods_class.add','null_layout');
		} else {
			$goods_class		= $model_class->getTreeClassList(array('store_id'=>$_SESSION['store_id']),2);
			$str	= '';
			if(is_array($goods_class) and count($goods_class)>0) {
				foreach ($goods_class as $key => $val) {
					$row[$val['stc_id']]	= $key + 1;
					$str .= intval($row[$val['stc_parent_id']]).",";
				}
				$str = substr($str,0,-1);
			} else {
				$str = '0';
			}
			Tpl::output('map',$str);
			Tpl::output('class_num',count($goods_class)-1);
			Tpl::output('goods_class',$goods_class);

			self::profile_menu('store_goods_class','store_goods_class');
			Tpl::output('menu_sign','store_goods_class');
			Tpl::output('menu_sign_url','index.php?act=store_goods_class&op=store_goods_class');
			Tpl::output('menu_sign1','goods_class');
			Tpl::showpage('store_goods_class.list');
		}
	}
	/**
	 * 卖家商品分类保存
	 *
	 * @param 
	 * @return 
	 */
	public function goods_class_saveOp() {
		$model_class	= Model('my_goods_class');
		if($_POST['stc_id'] != '') {
			$choeck_class	= $model_class->getClassInfo(array('stc_id'=>intval($_POST['stc_id']),'store_id'=>$_SESSION['store_id']));
			if(empty($choeck_class)) {
				showDialog(Language::get('store_goods_class_wrong'));
			}
			$state = $model_class->editGoodsClass($_POST,intval($_POST['stc_id']));
			if($state) {
				showDialog(Language::get('nc_common_save_succ'),'index.php?act=store_goods_class&op=index','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
			} else {
				showDialog(Language::get('nc_common_save_fail'));
			}
		} else {
			$state = $model_class->addGoodsClass($_POST);
			if($state) {
				showDialog(Language::get('nc_common_save_succ'),'index.php?act=store_goods_class&op=index','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
			} else {
				showDialog(Language::get('nc_common_save_fail'));
			}
		}
	}
	/**
	 * 卖家商品分类删除
	 *
	 * @param 
	 * @return 
	 */
	public function drop_goods_classOp() {
		$model_class	= Model('my_goods_class');
		$drop_state	= $model_class->dropGoodsClass(trim($_GET['class_id']));
		if ($drop_state){
			showDialog(Language::get('nc_common_del_succ'),'index.php?act=store_goods_class&op=store_goods_class','succ');
		}else{
			showDialog(Language::get('nc_common_del_fail'));
		}
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='') {
		Language::read('member_layout');
		$menu_array		= array();
		switch ($menu_type) {
			case 'store_goods_class':
				$menu_array = array(
				1=>array('menu_key'=>'store_goods_class','menu_name'=>Language::get('nc_member_path_goods_class'),	'menu_url'=>'index.php?act=store_goods_class&op=store_goods_class'));
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
