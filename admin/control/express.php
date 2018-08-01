<?php
/**
 * 快递公司
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
class expressControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('express');
	}

	public function indexOp(){
		$lang	= Language::getLangContent();
		$model = Model('express');
		if (preg_match('/^[A-Z]$/',$_GET['letter'])){
			$model->where(array('e_letter'=>$_GET['letter']));
		}
		$list = $model->page(10)->order('e_order,e_state desc,id')->select();
		Tpl::output('page',$model->showpage());
		Tpl::output('list',$list);
		Tpl::showpage('express.index');
	}

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			case 'state':
				$model_brand = Model('express');
				$update_array = array();
				$update_array['id'] = intval($_GET['id']);
				$update_array[$_GET['column']] = trim($_GET['value']);
				$model_brand->update($update_array);
				H('express',true);
				$this->log(L('nc_edit,express_name,express_state').'[ID:'.intval($_GET['id']).']',1);
				echo 'true';exit;
				break;				
			case 'order':
				$_GET['value'] = $_GET['value'] == 0? 2:1;
				$model_brand = Model('express');
				$update_array = array();
				$update_array['id'] = intval($_GET['id']);
				$update_array[$_GET['column']] = trim($_GET['value']);
				$model_brand->update($update_array);
				H('express',true);
				$this->log(L('nc_edit,express_name,express_state').'[ID:'.intval($_GET['id']).']',1);
				echo 'true';exit;
				break;
		}
		H('express',true);
	}
	
}