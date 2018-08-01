<?php
/**
 * 发货设置
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

class store_deliver_setControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index,deliver');
	}
    
	/**
	 * 发货地址列表
	 */
	public function daddress_listOp() {
	   Language::read('member_member_index');
	   $lang	= Language::getLangContent();
	   $model_daddress = Model('daddress');
	   $condition = array();
	   $condition['store_id'] = $_SESSION['store_id'];
	   $address_list = $model_daddress->getAddressList($condition,'*','',20);
	   Tpl::output('address_list',$address_list);
	   self::profile_menu('daddress','daddress');
	   Tpl::showpage('store_deliver_set.daddress_list');		
	}

	/**
	 * 新增/编辑发货地址
	 */
    public function daddress_addOp() {
        Language::read('member_member_index');
        $lang	= Language::getLangContent();        
        $model_daddress = Model('daddress');
        if (chksubmit()) {
            //保存 新增/编辑 表单
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["seller_name"],"require"=>"true","message"=>$lang['store_daddress_receiver_null']),
				array("input"=>$_POST["area_id"],"require"=>"true","validator"=>"Number","message"=>$lang['store_daddress_wrong_area']),
				array("input"=>$_POST["city_id"],"require"=>"true","validator"=>"Number","message"=>$lang['store_daddress_wrong_area']),
				array("input"=>$_POST["area_info"],"require"=>"true","message"=>$lang['store_daddress_area_null']),
				array("input"=>$_POST["address"],"require"=>"true","message"=>$lang['store_daddress_address_null']),
				array("input"=>$_POST['telphone'],'require'=>'true','message'=>$lang['store_daddress_phone_and_mobile'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}
			$data = array(
				'store_id' => $_SESSION['store_id'],
				'seller_name' => $_POST['seller_name'],
				'area_id' => $_POST['area_id'],
				'city_id' => $_POST['city_id'],
				'area_info' => $_POST['area_info'],
				'address' => $_POST['address'],
				'telphone' => $_POST['telphone'],
				'company' => $_POST['company']
			);
			$address_id = intval($_POST['address_id']);
			if ($address_id > 0){
			    $condition = array();
			    $condition['address_id'] = $address_id;
			    $condition['store_id'] = $_SESSION['store_id'];
				$update = $model_daddress->editAddress($data,$condition);
				if (!$update){
					showDialog($lang['store_daddress_modify_fail'],'','error');
				}
			} else {
				$insert = $model_daddress->addAddress($data);
				if (!$insert){
					showDialog($lang['store_daddress_add_fail'],'','error');
				}
			}
			showDialog($lang['nc_common_op_succ'],'reload','succ','CUR_DIALOG.close()');
        } elseif (is_numeric($_GET['address_id']) > 0) {
            //编辑
            $condition = array();
            $condition['address_id'] = intval($_GET['address_id']);
            $condition['store_id'] = $_SESSION['store_id'];
            $address_info = $model_daddress->getAddressInfo($condition);
            if (empty($address_info) && !is_array($address_info)){
                showMessage($lang['store_daddress_wrong_argument'],'index.php?act=store_deliver_set&op=daddress_list','html','error');
            }
            Tpl::output('address_info',$address_info);
        }
        Tpl::showpage('store_deliver_set.daddress_add','null_layout');
    }
    
    /**
     * 删除发货地址
     */
    public function daddress_delOp() {
        $address_id = intval($_GET['address_id']);
        if ($address_id <=  0) {
            showDialog(Language::get('store_daddress_del_fail'),'','error');
        }
		$condition = array();
		$condition['address_id'] = $address_id;
		$condition['store_id'] = $_SESSION['store_id'];
        $delete = Model('daddress')->delAddress($condition);
        if ($delete){
            showDialog(Language::get('store_daddress_del_succ'),'index.php?act=store_deliver_set&op=daddress_list','succ');
        }else {
            showDialog(Language::get('store_daddress_del_fail'),'','error');
        }
    }

    /**
     * 设置默认发货地址
     */
   public function daddress_default_setOp() {
       $address_id = intval($_GET['address_id']);
       if ($address_id <=  0) return false;
       $condition = array();
       $condition['store_id'] = $_SESSION['store_id'];
       $update = Model('daddress')->editAddress(array('is_default'=>0),$condition);
       $condition['address_id'] = $address_id;
       $update = Model('daddress')->editAddress(array('is_default'=>1),$condition);
   }

	public function expressOp() {	
		$model = Model('store_extend');

		if (chksubmit()){
			$data['store_id'] = $_SESSION['store_id'];
			if(is_array($_POST['cexpress']) && !empty($_POST['cexpress'])){
				$data['express'] = implode(',',$_POST['cexpress']);
			}else{				
				$data['express'] = '';
			}
			if (!$model->getby_store_id($_SESSION['store_id'])){
				$result = $model->insert($data);
			}else{
				$result = $model->update($data);
			}
			if ($result){
				showDialog(Language::get('nc_common_save_succ'),'reload','succ');
			}else{
				showDialog(Language::get('nc_common_save_fail'),'reload','error');
			}
		}
		if (!$express_list = H('express')){
			$express_list = H('express',true);
		}
		
		//取得店铺启用的快递公司ID
		$express_select = $model->getfby_store_id($_SESSION['store_id'],'express');
		if (!is_null($express_select)){
			$express_select = explode(',',$express_select);
		}else{
			$express_select = array();
		}
		Tpl::output('express_select',$express_select);
		//页面输出
		self::profile_menu('daddress','express');
		Tpl::output('menu_sign','daddress');
		Tpl::output('express_list',$express_list);
		Tpl::output('menu_sign1','default_express');
		Tpl::showpage('store_deliver_express');
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
		switch ($menu_type) {
			case 'search':
				$menu_array = array(
				1=>array('menu_key'=>'nodeliver',			'menu_name'=>Language::get('nc_member_path_deliverno'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=nodeliver'),
				2=>array('menu_key'=>'delivering',			'menu_name'=>Language::get('nc_member_path_delivering'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
				3=>array('menu_key'=>'delivered',		     'menu_name'=>Language::get('nc_member_path_delivered'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
				4=>array('menu_key'=>'search',		'menu_name'=>Language::get('nc_member_path_deliver_info'),	'menu_url'=>'###'),
				);
				break;				
			case 'daddress':
				$menu_array = array(
				1=>array('menu_key'=>'daddress',			'menu_name'=>Language::get('store_deliver_daddress_list'),	'menu_url'=>'index.php?act=store_deliver_set&op=daddress_list'),
				2=>array('menu_key'=>'express',				'menu_name'=>Language::get('store_deliver_default_express'),	'menu_url'=>'index.php?act=store_deliver_set&op=express')
				);
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
