<?php
/**
 * 咨询管理
 * 
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class consultingControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('consulting');
	}

	/**
	 * 咨询管理	 
	 */
	public function consultingOp(){
		$condition	= array();
		if(chksubmit()){
			if(trim($_GET['member_name'] != '')){
				$condition['member_name']	= trim($_GET['member_name']);
			}
			if(trim($_GET['consult_content'] != '')){
				$condition['consult_content']	= trim($_GET['consult_content']);
			}
			Tpl::output('form_submit',trim($_GET['form_submit']));
		}
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$consult	= Model('consult');
		$consult_list	= $consult->getConsultList($condition,$page,'admin');
		Tpl::output('show_page',$page->show());
		Tpl::output('consult_list',$consult_list);
		Tpl::showpage('consulting.index');
	}

	public function deleteOp(){		
		if(empty($_REQUEST['consult_id'])){
			showMessage(Language::get('nc_common_del_fail'));
		}
		$array_id	= array();
		if(!empty($_GET['consult_id'])){
			$array_id[]	= intval($_GET['consult_id']);
		}
		if(!empty($_POST['consult_id'])){
			$array_id	= $_POST['consult_id'];
		}
		$id	= array();
		$id	= "'".implode("','",$array_id)."'";
		$consult	= Model('consult');
		if($consult->dropConsult($id)){
			$this->log(L('nc_delete,consulting').'[ID:'.$id.']',null);
			showMessage(Language::get('nc_common_del_succ'));
		}else{
			showMessage(Language::get('nc_common_del_fail'));
		}
	}
}