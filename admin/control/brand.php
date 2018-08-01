<?php
/**
 * 商品品牌管理
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
class brandControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('brand');
	}

	/**
	 * 品牌列表
	 */
	public function brandOp(){
		$lang	= Language::getLangContent();
		$model_brand = Model();
		if (chksubmit()){
			if (!empty($_POST['del_brand_id'])){
				//删除图片
				if (is_array($_POST['del_brand_id'])){
					foreach ($_POST['del_brand_id'] as $k => $v){
						$v = intval($v);
						$brand_array = $model_brand->table('brand')->where(array('brand_id'=>$v))->find();
						if (!empty($brand_array['brand_pic'])){
							@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
						}
						//删除分类
						$model_brand->table('brand')->where(array('brand_id'=>$v))->delete();
						unset($brand_array);
					}
				}
				$this->log(L('nc_delete,brand_index_brand').'[ID:'.implode(',',$_POST['del_brand_id']).']',1);
				showMessage($lang['nc_common_del_succ']);
			}else {
				$this->log(L('nc_delete,brand_index_brand').'[ID:'.implode(',',$_POST['del_brand_id']).']',0);
				showMessage($lang['nc_common_del_fail']);
			}
		}
		/**
		 * 检索条件
		 */
		if (!empty($_GET['search_brand_name'])){
			$condition['brand_name'] = array('like',"%{$_GET['search_brand_name']}%");
		}
		if (!empty($_GET['search_brand_class'])){
			$condition['brand_class'] = array('like',"%{$_GET['search_brand_class']}%");
		}
		$condition['brand_apply']	= '1';

		$brand_list = $model_brand->table('brand')->where($condition)->order('brand_id desc')->page(20)->select();
		Tpl::output('page',$model_brand->showpage());
		Tpl::output('brand_list',$brand_list);
		Tpl::output('search_brand_name',trim($_GET['search_brand_name']));
		Tpl::output('search_brand_class',trim($_GET['search_brand_class']));
		Tpl::showpage('brand.index');
	}

	/**
	 * 增加品牌
	 */
	public function brand_addOp(){
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["brand_name"], "require"=>"true", "message"=>$lang['brand_add_name_null']),
				array("input"=>$_POST["brand_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['brand_add_sort_int']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$insert_array = array();
				$insert_array['brand_name'] = trim($_POST['brand_name']);
				$insert_array['class_id']	= $_POST['class_id'];
				$insert_array['brand_class'] = trim($_POST['brand_class']);
				$insert_array['brand_pic'] = trim($_POST['brand_pic']);
				$insert_array['brand_recommend'] = trim($_POST['brand_recommend']);
				$insert_array['brand_sort'] = intval($_POST['brand_sort']);

				$result = $model_brand->add($insert_array);
				if ($result){
					$url = array(
						array(
							'url'=>'index.php?act=brand&op=brand_add',
							'msg'=>$lang['brand_add_again'],
						),
						array(
							'url'=>'index.php?act=brand&op=brand',
							'msg'=>$lang['brand_add_back_to_list'],
						)
					);
					$this->log(L('nc_add,brand_index_brand').'['.$_POST['brand_name'].']',1);
					showMessage($lang['nc_common_save_succ'],$url);
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}

		// 一级商品分类
		$gc_list = H('goods_class') ? H('goods_class') : H('goods_class', true);
		Tpl::output('gc_list', $gc_list);

		Tpl::showpage('brand.add');
	}

	/**
	 * 品牌编辑
	 */
	public function brand_editOp(){
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');

		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["brand_name"], "require"=>"true", "message"=>$lang['brand_add_name_null']),
				array("input"=>$_POST["brand_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['brand_add_sort_int']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
			    $brand_array = $model_brand->getOneBrand(intval($_POST['brand_id']));
				$update_array = array();
				$update_array['brand_id'] = intval($_POST['brand_id']);
				$update_array['brand_name'] = trim($_POST['brand_name']);
				$update_array['class_id']	= $_POST['class_id'];
				$update_array['brand_class'] = trim($_POST['brand_class']);
				if (!empty($_POST['brand_pic'])){
					$update_array['brand_pic'] = $_POST['brand_pic'];
				}
				$update_array['brand_recommend'] = intval($_POST['brand_recommend']);
				$update_array['brand_sort'] = intval($_POST['brand_sort']);

				$result = $model_brand->edit($update_array);
				if ($result){
					if (!empty($_POST['brand_pic']) && !empty($brand_array['brand_pic'])){
						@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
					}
					$url = array(
						array(
							'url'=>'index.php?act=brand&op=brand_edit&brand_id='.intval($_POST['brand_id']),
							'msg'=>$lang['brand_edit_again'],
						),
						array(
							'url'=>'index.php?act=brand&op=brand',
							'msg'=>$lang['brand_add_back_to_list'],
						)
					);
					$this->log(L('nc_edit,brand_index_brand').'['.$_POST['brand_name'].']',1);
					showMessage($lang['nc_common_save_succ'],$url);
				}else {
					$this->log(L('nc_edit,brand_index_brand').'['.$_POST['brand_name'].']',0);
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}

		$brand_array = $model_brand->getOneBrand(intval($_GET['brand_id']));
		if (empty($brand_array)){
			showMessage($lang['param_error']);
		}
		Tpl::output('brand_array',$brand_array);

		// 一级商品分类
		$gc_list = H('goods_class') ? H('goods_class') : H('goods_class', true);
		Tpl::output('gc_list', $gc_list);

		Tpl::showpage('brand.edit');
	}

	/**
	 * 删除品牌
	 */
	public function brand_delOp(){
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if (intval($_GET['del_brand_id']) > 0){
			$brand_array = $model_brand->getOneBrand(intval($_GET['del_brand_id']));
			if (!empty($brand_array['brand_pic'])){
				@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
			}

			$model_brand->del(intval($_GET['del_brand_id']));
			unset($brand_array);
			$this->log(L('nc_delete,brand_index_brand').'[ID:'.intval($_GET['del_brand_id']).']',1);
			showMessage($lang['nc_common_del_succ'],'index.php?act=brand&op=brand');
		}else {
			$this->log(L('nc_delete,brand_index_brand').'[ID:'.intval($_GET['del_brand_id']).']',0);
			showMessage($lang['nc_common_del_fail'],'index.php?act=brand&op=brand');
		}
	}

	/**
	 * 品牌申请
	 */
	public function brand_applyOp(){
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		/**
		 * 对申请品牌进行操作 通过，拒绝
		 */
		if (chksubmit()){
			if (!empty($_POST['del_id'])){
				switch ($_POST['type']){
					case 'pass':
						//更新品牌 申请状态
						foreach ($_POST['del_id'] as $k => $v){
							$update_array = array();
							$update_array['brand_id'] = intval($v);
							$update_array['brand_apply'] = 1;
							$model_brand->edit($update_array);
						}
						$this->log(L('brand_apply_pass').'[ID:'.implode(',',$_POST['del_id']).']',null);
						showMessage($lang['brand_apply_passed']);
						break;
					case 'refuse':
						//删除该品牌
						foreach ($_POST['del_id'] as $k => $v){
							$v = intval($v);
							$brand_array = $model_brand->getOneBrand($v);
							if (!empty($brand_array['brand_pic'])){
								@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
							}
							//删除分类
							$result = $model_brand->del($v);
							unset($brand_array);
						}
						$this->log(L('nc_delete,brand_index_brand').'[ID:'.implode(',',$_POST['del_id']).']',1);
						showMessage($lang['nc_common_del_succ']);
						break;
						default:
							showMessage($lang['brand_apply_invalid_argument']);
				}
			}else {
				$this->log(L('nc_delete,brand_index_brand'),0);
				showMessage($lang['nc_common_del_fail']);
			}
		}
		/**
		 * 检索条件
		 */
		$condition['like_brand_name'] = trim($_POST['search_brand_name']);
		$condition['like_brand_class'] = trim($_POST['search_brand_class']);
		$condition['brand_apply'] = '0';
		$brand_list = $model_brand->getBrandList($condition);

		Tpl::output('brand_list',$brand_list);
		Tpl::output('search_brand_name',trim($_POST['search_brand_name']));
		Tpl::output('search_brand_class',trim($_POST['search_brand_class']));
		Tpl::showpage('brand.apply');
	}

	/**
	 * 审核 申请品牌操作
	 */
	public function brand_apply_setOp(){
		$model_brand = Model('brand');

		if (intval($_GET['brand_id']) > 0){
			switch ($_GET['state']){
				case 'pass':
					/**
					 * 更新品牌 申请状态
					 */
					$update_array = array();
					$update_array['brand_id'] = intval($_GET['brand_id']);
					$update_array['brand_apply'] = 1;

					$result = $model_brand->edit($update_array);
					if ($result === true){
						//得到将要删除的品牌
						$brand_info = $model_brand->getOneBrand(intval($_GET['brand_id']));
						$this->log(L('brand_apply_pass').'['.$brand_info['brand_name'].']',null);
						showMessage(Language::get('brand_apply_pass'));
					}else {
						$this->log(L('brand_apply_pass').'['.$brand_info['brand_name'].']',0);
						showMessage(Language::get('brand_apply_fail'));
					}
					break;
				case 'refuse':
					//删除该品牌
					$brand_array = $model_brand->getOneBrand(intval($_GET['brand_id']));
					if (!empty($brand_array['brand_pic'])){
						@unlink(BASE_UPLOAD_PATH.DS.ATTACH_BRAND.DS.$brand_array['brand_pic']);
					}
					//删除分类
					$model_brand->del(intval($_GET['brand_id']));
					unset($brand_array);
					$this->log(L('nc_delete,brand_index_brand').'[ID:'.intval($_GET['brand_id']).']',1);
					showMessage(Language::get('nc_common_del_succ'));
					break;
					default:
						showMessage(Language::get('brand_apply_paramerror'));
			}
		}else {
			$this->log(L('nc_delete,brand_index_brand').'[ID:'.intval($_GET['brand_id']).']',0);
			showMessage(Language::get('brand_apply_brandparamerror'));
		}
	}

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 品牌名称
			 */
			case 'brand_name':
				$model_brand = Model('brand');
				/**
				 * 判断是否有重复
				 */
				$condition['brand_name'] = trim($_GET['value']);
				$condition['no_brand_id'] = intval($_GET['id']);
				$result = $model_brand->getBrandList($condition);
				if (empty($result)){
					$update_array = array();
					$update_array['brand_id'] = intval($_GET['id']);
					$update_array['brand_name'] = trim($_GET['value']);
					$model_brand->edit($update_array);
					$this->log(L('nc_edit,brand_index_name').'['.$_GET['value'].']',1);
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
			/**
			 * 品牌类别，品牌排序，推荐
			 */
			case 'brand_class':
			case 'brand_sort':
			case 'brand_recommend':
				$model_brand = Model('brand');
				$update_array = array();
				$update_array['brand_id'] = intval($_GET['id']);
				$update_array[$_GET['column']] = trim($_GET['value']);
				$model_brand->edit($update_array);
				$detail_log = str_replace(array('brand_class','brand_sort','brand_recommend'),array(L('brand_index_class'),L('nc_sort'),L('nc_recommend')),$_GET['branch']);
				$this->log(L('nc_edit,brand_index_brand').$detail_log.'[ID:'.intval($_GET['id']).']',1);
				echo 'true';exit;
				break;
			/**
			 * 验证品牌名称是否有重复
			 */
			case 'check_brand_name':
				$model_brand = Model('brand');
				$condition['brand_name'] = trim($_GET['brand_name']);
				$condition['no_brand_id'] = intval($_GET['id']);
				$result = $model_brand->getBrandList($condition);
				if (empty($result)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
		}
	}

	/**
	 * 品牌导出第一步
	 */
	public function export_step1Op(){
		$model = Model();
		$condition = array();
		if (!empty($_GET['search_brand_name'])){
			$condition['brand_name'] = array('like',"%{$_GET['search_brand_name']}%");
		}
		if (!empty($_GET['search_brand_class'])){
			$condition['brand_class'] = array('like',"%{$_GET['search_brand_class']}%");
		}
		$condition['brand_apply']	= '1';

		if (!is_numeric($_GET['curpage'])){
			$count = $model->table('brand')->where($condition)->count();
			$array = array();
			if ($count > self::EXPORT_SIZE ){	//显示下载链接
				$page = ceil($count/self::EXPORT_SIZE);
				for ($i=1;$i<=$page;$i++){
					$limit1 = ($i-1)*self::EXPORT_SIZE + 1;
					$limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
					$array[$i] = $limit1.' ~ '.$limit2 ;
				}
				Tpl::output('list',$array);
				Tpl::output('murl','index.php?act=brand&op=brand');
				Tpl::showpage('export.excel');
			}else{	//如果数量小，直接下载
				$data = $model->table('brand')->where($condition)->order('brand_id desc')->limit(self::EXPORT_SIZE)->select();
				$this->createExcel($data);
			}
		}else{	//下载
			$limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
			$limit2 = self::EXPORT_SIZE;
			$data = $model->table('brand')->where($condition)->order('brand_id desc')->limit("{$limit1},{$limit2}")->select();
			$this->createExcel($data);
		}
	}

	/**
	 * 生成excel
	 *
	 * @param array $data
	 */
	private function createExcel($data = array()){
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_brandid'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_brand'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_brand_cate'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_brand_img'));
		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['brand_id']);
			$tmp[] = array('data'=>$v['brand_name']);
			$tmp[] = array('data'=>$v['brand_class']);
			$tmp[] = array('data'=>$v['brand_pic']);
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_brand'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_brand'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}

}
