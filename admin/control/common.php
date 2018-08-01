<?php
/**
 * 通用页面
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class commonControl extends SystemControl{
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 图片上传
	 *
	 */
	public function pic_uploadOp(){
		if (chksubmit()){
			//上传图片
			$upload = new UploadFile();
			$upload->set('thumb_width',	500);
			$upload->set('thumb_height',499);
			$upload->set('thumb_ext','_small');
			$upload->set('max_size',C('image_max_filesize')?C('image_max_filesize'):1024);
			$upload->set('ifremove',true);
			$upload->set('default_dir',$_GET['uploadpath']);

			if (!empty($_FILES['_pic']['tmp_name'])){
				$result = $upload->upfile('_pic');
				if ($result){
					exit(json_encode(array('status'=>1,'url'=>UPLOAD_SITE_URL.'/'.$_GET['uploadpath'].'/'.$upload->thumb_image)));
				}else {
					exit(json_encode(array('status'=>0,'msg'=>$upload->error)));
				}
			}
		}
	}

	/**
	 * 图片裁剪
	 *
	 */
	public function pic_cutOp(){
		Language::read('admin_common');
		$lang = Language::getLangContent();
		import('function.thumb');
		if (chksubmit()){
			$thumb_width = $_POST['x'];
			$x1 = $_POST["x1"];
			$y1 = $_POST["y1"];
			$x2 = $_POST["x2"];
			$y2 = $_POST["y2"];
			$w = $_POST["w"];
			$h = $_POST["h"];
			$scale = $thumb_width/$w;
			$src = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_POST['url']);
			if (!empty($_POST['filename'])){
// 				$save_file2 = BASE_UPLOAD_PATH.'/'.$_POST['filename'];
				$save_file2 = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_POST['filename']);
			}else{
				$save_file2 = str_replace('_small.','_sm.',$src);
			}
			$cropped = resize_thumb($save_file2, $src,$w,$h,$x1,$y1,$scale);
			@unlink($src);
			$pathinfo = pathinfo($save_file2);
			exit($pathinfo['basename']);		
		}
		$save_file = str_ireplace(UPLOAD_SITE_URL,BASE_UPLOAD_PATH,$_GET['url']);
		$_GET['resize'] = $_GET['resize'] == '0' ? '0' : '1';
		Tpl::output('height',get_height($save_file));
		Tpl::output('width',get_width($save_file));
		Tpl::showpage('common.pic_cut','null_layout');
	}
	
	/**
	 * 查询每月的周数组
	 */
	public function getweekofmonthOp(){
	    import('function.datehelper');
	    $year = $_GET['y'];
	    $month = $_GET['m'];
	    $week_arr = getMonthWeekArr($year, $month);
	    echo json_encode($week_arr);
	    die;
	}
}