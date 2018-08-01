<?php
/**
 * 会员管理
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

class memberControl extends SystemControl{
	const EXPORT_SIZE = 5000;
	public function __construct(){
		parent::__construct();
		Language::read('member');
	}

	/**
	 * 会员管理
	 */
	public function memberOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 检索条件
		 */
		if ($_GET['search_field_value'] != '') {
    		switch ($_GET['search_field_name']){
    			case 'member_name':
    				$condition['member_name'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_email':
    				$condition['member_email'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_truename':
    				$condition['member_truename'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    		}
		}
		switch ($_GET['search_state']){
			case 'no_informallow':
				$condition['inform_allow'] = '2';
				break;
			case 'no_isbuy':
				$condition['is_buy'] = '0';
				break;
			case 'no_isallowtalk':
				$condition['is_allowtalk'] = '0';
				break;
			case 'no_memberstate':
				$condition['member_state'] = '0';
				break;
		}
		/**
		 * 排序
		 */
		$order = trim($_GET['search_sort']);
		if (empty($order)) {
		    $order = 'member_id desc';
		}
		$member_list = $model_member->getMemberList($condition, '*', 10, $order);
		/**
		 * 整理会员信息
		 */
		if (is_array($member_list)){
			foreach ($member_list as $k=> $v){
				$member_list[$k]['member_time'] = $v['member_time']?date('Y-m-d H:i:s',$v['member_time']):'';
				$member_list[$k]['member_login_time'] = $v['member_login_time']?date('Y-m-d H:i:s',$v['member_login_time']):'';
			}
		}

		Tpl::output('search_sort',trim($_GET['search_sort']));
		Tpl::output('search_field_name',trim($_GET['search_field_name']));
		Tpl::output('search_field_value',trim($_GET['search_field_value']));
		Tpl::output('member_list',$member_list);
		Tpl::output('page',$model_member->showpage());
		Tpl::showpage('member.index');
	}

	/**
	 * 会员修改
	 */
	public function member_editOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 保存
		 */
		if (chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$update_array = array();
				$update_array['member_id']			= intval($_POST['member_id']);
				if (!empty($_POST['member_passwd'])){
					$update_array['member_passwd'] = md5($_POST['member_passwd']);
				}
				$update_array['member_email']		= trim($_POST['member_email']);
				$update_array['member_truename']	= trim($_POST['member_truename']);
				$update_array['member_sex'] 		= trim($_POST['member_sex']);
				$update_array['member_qq'] 			= trim($_POST['member_qq']);
				$update_array['member_ww']			= trim($_POST['member_ww']);
				$update_array['inform_allow'] 		= trim($_POST['inform_allow']);
				$update_array['is_buy'] 			= trim($_POST['isbuy']);
				$update_array['is_allowtalk'] 		= trim($_POST['allowtalk']);
				$update_array['member_state'] 		= trim($_POST['memberstate']);
				if (!empty($_POST['member_avatar'])){
					$update_array['member_avatar'] = $_POST['member_avatar'];
				}
				$result = $model_member->updateMember($update_array,intval($_POST['member_id']));
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=member&op=member',
					'msg'=>$lang['member_edit_back_to_list'],
					),
					array(
					'url'=>'index.php?act=member&op=member_edit&member_id='.intval($_POST['member_id']),
					'msg'=>$lang['member_edit_again'],
					),
					);
					$this->log(L('nc_edit,member_index_name').'[ID:'.$_POST['member_id'].']',1);
					showMessage($lang['member_edit_succ'],$url);
				}else {
					showMessage($lang['member_edit_fail']);
				}
			}
		}
		$condition['member_id'] = intval($_GET['member_id']);
		$member_array = $model_member->infoMember($condition);

		Tpl::output('member_array',$member_array);
		Tpl::showpage('member.edit');
	}

	/**
	 * 新增会员
	 */
	public function member_addOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 保存
		 */
		if (chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$insert_array = array();
				$insert_array['member_name']	= trim($_POST['member_name']);
				$insert_array['member_passwd']	= trim($_POST['member_passwd']);
				$insert_array['member_email']	= trim($_POST['member_email']);
				$insert_array['member_truename']= trim($_POST['member_truename']);
				$insert_array['member_sex'] 	= trim($_POST['member_sex']);
				$insert_array['member_qq'] 		= trim($_POST['member_qq']);
				$insert_array['member_ww']		= trim($_POST['member_ww']);
                //默认允许举报商品
                $insert_array['inform_allow'] 	= '1';
				if (!empty($_POST['member_avatar'])){
					$insert_array['member_avatar'] = trim($_POST['member_avatar']);
				}

				$result = $model_member->addMember($insert_array);
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=member&op=member',
					'msg'=>$lang['member_add_back_to_list'],
					),
					array(
					'url'=>'index.php?act=member&op=member_add',
					'msg'=>$lang['member_add_again'],
					),
					);
					$this->log(L('nc_add,member_index_name').'[	'.$_POST['member_name'].']',1);
					showMessage($lang['member_add_succ'],$url);
				}else {
					showMessage($lang['member_add_fail']);
				}
			}
		}
		Tpl::showpage('member.add');
	}

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 验证会员是否重复
			 */
			case 'check_user_name':
				$model_member = Model('member');
				$condition['member_name']	= trim($_GET['member_name']);
				$condition['no_member_id']	= intval($_GET['member_id']);
				$list = $model_member->infoMember($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
				/**
			 * 验证邮件是否重复
			 */
			case 'check_email':
				$model_member = Model('member');
				$condition['member_email'] = trim($_GET['member_email']);
				$condition['no_member_id'] = intval($_GET['member_id']);
				$list = $model_member->infoMember($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
		}
	}

}
