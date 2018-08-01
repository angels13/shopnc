<?php
/**
 * 品牌管理
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
class store_brandControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_brand');
		$lang	= Language::getLangContent();
		//卖家店铺是否审核的判断
		if($this->store_info['store_state'] == 2) {
			showMessage($lang['store_auditing_tip'],'index.php?act=store','html','error');
		}
	}

	public function indexOp(){
		$this->brand_listOp();
	}

	/**
	 * 品牌列表
	 */	
	public function brand_listOp() {
		$model_brand	= Model('brand');
		$condition['like_brand_name']	= $_GET['brand_name'];
		$condition['storeid_equal']			= "{$_SESSION['store_id']}";

		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');

		$brand_list		= $model_brand->getBrandList($condition,$page);
		Tpl::output('brand_list',$brand_list);
		Tpl::output('show_page',$page->show());

		self::profile_menu('brand_list','brand_list');
		Tpl::output('menu_sign','brand_list');
		Tpl::showpage('store_brand.list');
	}

	/**
	 * 品牌添加页面
	 */	
	public function brand_addOp() {
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if($_GET['brand_id'] != '') {
			$brand_array = $model_brand->getOneBrand($_GET['brand_id']);
			if (empty($brand_array) || $brand_array['store_id'] != $_SESSION['store_id']){
				showMessage($lang['wrong_argument'],'','html','error');
			}
			Tpl::output('brand_array',$brand_array);
		}
		
		// 一级商品分类
		$gc_list = Model()->table('goods_class')->where(array('gc_parent_id'=>0))->select();
		Tpl::output('gc_list', $gc_list);
		
		Tpl::showpage('store_brand.add','null_layout');
	}

	/**
	 * 品牌保存
	 */	
	public function brand_saveOp() {
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if (chksubmit()) {
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["brand_name"], "require"=>"true", "message"=>$lang['store_goods_brand_name_null'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}else {
				/**
				 * 上传图片
				 */
				if (!empty($_FILES['brand_pic']['name'])){
					$upload = new UploadFile();
					$upload->set('default_dir',ATTACH_BRAND);
					$upload->set('thumb_width',	150);
					$upload->set('thumb_height',50);
					$upload->set('thumb_ext',	'_small');
					$upload->set('ifremove',	true);
					$result = $upload->upfile('brand_pic');				
					if ($result){
						$_POST['brand_pic'] = $upload->thumb_image;
					}else {
						showDialog($upload->error);
					}
				}
				$insert_array = array();
				$insert_array['brand_name']		= $_POST['brand_name'];
				$insert_array['class_id']	= $_POST['class_id'];
				$insert_array['brand_class']	= $_POST['brand_class'];
				$insert_array['brand_pic']		= $_POST['brand_pic'];
				$insert_array['brand_apply']	= 0;
				$insert_array['store_id']		= $_SESSION['store_id'];

				$result = $model_brand->add($insert_array);
				if ($result){
					showDialog($lang['store_goods_brand_apply_success'],'index.php?act=store_brand&op=brand_list','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
				}else {
					showDialog($lang['nc_common_save_fail']);
				}
			}
		}
	}

	/**
	 * 品牌修改
	 */	
	public function brand_editOp() {
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if ($_POST['form_submit'] == 'ok' and intval($_POST['brand_id']) != 0) {
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["brand_name"], "require"=>"true", "message"=>$lang['store_goods_brand_name_null'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}else {
				/**
				 * 上传图片
				 */
				if (!empty($_FILES['brand_pic']['name'])){
					$upload = new UploadFile();
					$upload->set('default_dir',ATTACH_BRAND);
					$upload->set('thumb_width',	150);
					$upload->set('thumb_height',50);
					$upload->set('thumb_ext',	'_small');
					$upload->set('ifremove',	true);
					$result = $upload->upfile('brand_pic');

					if ($result){
						$_POST['brand_pic'] = $upload->thumb_image;
					}else {
						showDialog($upload->error);
					}
				}

				$update_array = array();
				$update_array['brand_id'] = intval($_POST['brand_id']);
				$update_array['brand_name'] = $_POST['brand_name'];
				$update_array['class_id']	= $_POST['class_id'];
				$update_array['brand_class'] = $_POST['brand_class'];
				if (!empty($_POST['brand_pic'])){
					$update_array['brand_pic'] = $_POST['brand_pic'];
				}

				//查出原图片路径，后面会删除图片
				$brand_info = $model_brand->getOneBrand(intval($_POST['brand_id']));
				$result = $model_brand->edit($update_array);
				if ($result){
					//删除老图片
					if (!empty($brand_info['brand_pic']) && $_POST['brand_pic']){
						@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_info['brand_pic']);
					}
					showDialog($lang['nc_common_save_succ'],'index.php?act=store_brand&op=brand_list','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
				}else {
					showDialog($lang['nc_common_save_fail']);
				}
			}
		} else {
			showDialog($lang['nc_common_save_fail']);
		}
	}

	/**
	 * 品牌删除
	 */	
	public function drop_brandOp() {
		$model_brand	= Model('brand');
		$brand_id		= intval($_GET['brand_id']);
		if ($brand_id > 0){
			$brand_list = $model_brand->getBrandList(array('brand_id'=>$brand_id,'brand_apply'=>'0'));
			$brand_array = $brand_list[0];
			if (empty($brand_array) || $brand_array['store_id'] != $_SESSION['store_id']){
				showDialog(Language::get('nc_common_del_fail'));
			}
			if (!empty($brand_array['brand_pic'])){
				@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
			}
			/**
			 * 删除分类
			 */
			$model_brand->del($brand_id);
			unset($brand_array);
			showDialog(Language::get('nc_common_del_succ'),'index.php?act=store_brand&op=brand_list','succ');
		}else {
			showDialog(Language::get('nc_common_del_fail'));
		}
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @param array 	$array		附加菜单
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='',$array=array()) {
		Language::read('member_layout');
		$lang	= Language::getLangContent();
		$menu_array		= array();
		switch ($menu_type) {
			case 'brand_list':
				$menu_array = array(
				1=>array('menu_key'=>'brand_list',	'menu_name'=>$lang['nc_member_path_brand_list'],			'menu_url'=>'index.php?act=store_brand&op=brand_list'));
				break;
		}
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}		
}	