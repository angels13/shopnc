<?php
/**
 * 用户中心
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class homeControl extends BaseMemberControl {
	/**
	 * 用户中心
	 *
	 * @param 
	 * @return 
	 */
	public function indexOp() {
		$this->memberOp();	
	}
	/**
	 * 我的资料【用户中心】
	 *
	 * @param 
	 * @return 
	 */
	public function memberOp() {

		Language::read('member_home_member');
		$lang	= Language::getLangContent();

		$model_member	= Model('member');

		if (chksubmit()){

			$member_array	= array();
			$member_array['member_truename']	= $_POST['member_truename'];
			$member_array['member_sex']			= $_POST['member_sex'];
			$member_array['member_qq']			= $_POST['member_qq'];
			$member_array['member_ww']			= $_POST['member_ww'];
			$member_array['member_areaid']		= $_POST['area_id'];
			$member_array['member_cityid']		= $_POST['city_id'];
			$member_array['member_provinceid']	= $_POST['province_id'];
			$member_array['member_areainfo']	= $_POST['area_info'];
			$member_array['member_id']			= $_SESSION['member_id'];
			if (strlen($_POST['birthday']) == 10){
				$member_array['member_birthday']	= $_POST['birthday'];
			}
			$member_array['member_privacy']		= serialize($_POST['privacy']);
			$update = $model_member->update($member_array);

			$message = $update? $lang['nc_common_save_succ'] : $lang['nc_common_save_fail']; 
			showDialog($message,'reload',$update ? 'succ' : 'error');
		}

		$member_info = $model_member->find($_SESSION['member_id']);
		if($member_info['member_privacy'] != ''){
			$member_info['member_privacy'] = unserialize($member_info['member_privacy']);
		}
		Tpl::output('member_info',$member_info);

		self::profile_menu('member','member');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','baseinfo');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_profile');
	}
	/**
	 * 我的资料【更多个人资料】
	 *
	 * @param 
	 * @return 
	 */
	public function moreOp(){
		/**
		 * 读取语言包
		 */
		Language::read('member_home_member');
		
		// 实例化模型
		$model = Model();
		
		if(chksubmit()){
			$model->table('sns_mtagmember')->where(array('member_id'=>$_SESSION['member_id']))->delete();
			if(!empty($_POST['mid'])){
				$insert_array = array();
				foreach ($_POST['mid'] as $val){
					$insert_array[] = array('mtag_id'=>$val,'member_id'=>$_SESSION['member_id']);
				}
				$model->table('sns_mtagmember')->insertAll($insert_array,'',true);
			}
			showDialog(Language::get('nc_common_op_succ'),'','succ');
		}
		
		// 用户标签列表
		$mtag_array = $model->table('sns_membertag')->order('mtag_sort asc')->limit(1000)->select();
		
		// 用户已添加标签列表。
		$mtm_array = $model->table('sns_mtagmember')->where(array('member_id'=>$_SESSION['member_id']))->select();
		$mtag_list	= array();
		$mtm_list	= array();
		if(!empty($mtm_array) && is_array($mtm_array)){
			// 整理
			$elect_array = array();
			foreach($mtm_array as $val){
				$elect_array[]	= $val['mtag_id'];
			}
			foreach ((array)$mtag_array as $val){
				if(in_array($val['mtag_id'], $elect_array)){
					$mtm_list[] = $val;
				}else{
					$mtag_list[] = $val;
				}
			}
		}else{
			$mtag_list = $mtag_array;
		}
		Tpl::output('mtag_list', $mtag_list);
		Tpl::output('mtm_list', $mtm_list);
		
		self::profile_menu('member','more');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','baseinfo');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_profile.more');
	}
	/**
	 * 我的资料【密码修改】
	 *
	 * @param 
	 * @return 
	 */
	public function passwdOp() {
		/**
		 * 读取语言包
		 */
		Language::read('member_home_member');
		$lang	= Language::getLangContent();

		/**
		 * 实例化模型
		 */
		$model_member	= Model('member');
		/**
		 * 修改密码
		 */				
		if(chksubmit()) {
			/**
			 * 填写密码信息验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["orig_password"],		"require"=>"true",		"message"=>$lang['home_member_old_password_null']),
			array("input"=>$_POST["new_password"],		"require"=>"true",		"message"=>$lang['home_member_new_password_null']),
			array("input"=>$_POST["confirm_password"],	"require"=>"true",		"validator"=>"Compare","operator"=>"==","to"=>$_POST["new_password"],"message"=>$lang['home_member_input_two_password_again']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}

			$member_info	= $model_member->where(array('member_name'=>trim($_SESSION['member_name']),'member_passwd'=>md5(trim($_POST['orig_password']))))->find();
			if(empty($member_info)) {
				showDialog($lang['home_member_old_password_wrong']);
			}
			$update	= $model_member->update(array('member_passwd'=>md5(trim($_POST['new_password'])),'member_id'=>$_SESSION['member_id']));
			if($update) {
				$message = $lang['nc_common_save_succ'];
			} else {
				$message = $lang['nc_common_save_fail'];
			}
			showDialog($message,'index.php?act=login&op=logout',$update ? 'succ' : 'error');
		}

		self::profile_menu('member','passwd');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','passwd');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_passwd');
	}
	/**
	 * 我的资料【email修改】
	 *
	 * @param 
	 * @return 
	 */
	public function emailOp() {
		/**
		 * 读取语言包
		 */
		Language::read('member_home_member');
		$lang	= Language::getLangContent();

		/**
		 * 实例化模型
		 */
		$model_member	= Model('member');
		if(chksubmit()) {
			/**
			 * 填写密码信息验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["orig_password"],		"require"=>"true",		"message"=>$lang['home_member_password_null']),
			array("input"=>$_POST["email"],				"require"=>"true",		"validator"=>"email",	"message"=>$lang['home_member_input_email_again_format']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
			}
			/**
			 * 邮箱是否存在检查
			 */			
			$email_array	= $model_member->getby_member_email(trim($_POST['email']));
			if(!empty($email_array) && is_array($email_array)) {
				showDialog($lang['home_member_input_email_again_exists']);
			}
			/**
			 * 密码是否正确检查
			 */
			$member_info	= $model_member->where(array('member_name'=>trim($_SESSION['member_name']),'member_passwd'=>md5(trim($_POST['orig_password']))))->find();
			if(empty($member_info)) {
				showDialog($lang['home_member_input_password_again_wrong']);
			}
			/**
			 * 修改电子邮件地址
			 */
			$update	= $model_member->update(array('member_email'=>trim($_POST['email']),'member_id'=>$_SESSION['member_id']));
			if($update) {
				$_SESSION['member_email'] = trim($_POST['email']);
				$message = $lang['nc_common_save_succ'];
			} else {
				$message = $lang['nc_common_save_fail'];
			}
			showDialog($message,'reload',$update ? 'succ' : 'error');
		}

		self::profile_menu('member','email');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','email');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_email');
	}
	/**
	 * 收到(普通)站内信列表
	 *
	 * @param 
	 * @return 
	 */
	public function messageOp() {
		//读取语言包
		Language::read('member_home_message');
		$model_message	= Model('message');
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$message_array	= $model_message->listMessage(array('message_type'=>'2','to_member_id_common'=>$_SESSION['member_id'],'no_message_state'=>'2'),$page);
		Tpl::output('show_page',$page->show());
		Tpl::output('message_array',$message_array);
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		Tpl::output('isallowsend',$isallowsend);
		Tpl::output('drop_type','msg_list');
		self::profile_menu('message','message');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','receivemsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_box');
	}
	/**
	 * 收到(私信)站内信列表
	 *
	 * @param 
	 * @return 
	 */
	public function personalmsgOp() {
		//读取语言包
		Language::read('member_home_message');
		$model_message	= Model('message');
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$message_array	= $model_message->listMessage(array('message_type'=>'0','to_member_id_common'=>$_SESSION['member_id'],'no_message_state'=>'2'),$page);
		Tpl::output('show_page',$page->show());
		Tpl::output('message_array',$message_array);
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		Tpl::output('isallowsend',$isallowsend);
		Tpl::output('drop_type','msg_list');
		self::profile_menu('message','close');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','close');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_box');
	}
	/**
	 * 统计收到站内信未读条数
	 *
	 * @return int
	 */
	private function receivedCommonNewNum(){
		$model_message	= Model('message');
		$countnum = $model_message->countMessage(array('message_type'=>'2','to_member_id_common'=>$_SESSION['member_id'],'no_message_state'=>'2','message_open_common'=>'0'));
		return $countnum;
	}
	/**
	 * 统计系统站内信未读条数
	 *
	 * @return int
	 */
	private function receivedSystemNewNum(){
		$message_model = Model('message');
		$condition_arr = array();
		$condition_arr['message_type'] = '1';//系统消息
		$condition_arr['to_member_id'] = $_SESSION['member_id'];
		$condition_arr['no_del_member_id'] = $_SESSION['member_id'];
		$condition_arr['no_read_member_id'] = $_SESSION['member_id'];
		$countnum = $message_model->countMessage($condition_arr);
		return $countnum; 
	}
	/**
	 * 统计私信未读条数
	 *
	 * @return int
	 */
	private function receivedPersonalNewNum(){
		$model_message = Model('message');
		$countnum = $model_message->countMessage(array('message_type'=>'0','to_member_id_common'=>$_SESSION['member_id'],'no_message_state'=>'2','message_open_common'=>'0'));
		return $countnum; 
	}
	/**
	 * 查询会员是否允许发送站内信
	 *
	 * @return bool
	 */
	private function allowSendMessage($member_id){
        $member_model = Model('member');
        $member_id = intval($member_id);
        $member_info = $member_model->infoMember(array('member_id'=>"{$member_id}"));
        if ($member_info['is_allowtalk'] == '1'){
        	return true;
        }else{
        	return false;
        }
	}
	/**
	 * 私人站内信列表
	 *
	 * @param 
	 * @return 
	 */
	public function privatemsgOp(){
		Language::read('member_home_message');
		$model_message	= Model('message');
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$message_array	= $model_message->listMessage(array('message_type_in'=>'0,2','from_member_id'=>"{$_SESSION['member_id']}",'no_message_state'=>'1'),$page);
		Tpl::output('show_page',$page->show());
		Tpl::output('message_array',$message_array);
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		Tpl::output('isallowsend',$isallowsend);
		Tpl::output('drop_type','msg_private');
		Tpl::output('menu_sign','message');
		self::profile_menu('message','private');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','privatemsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_sendlist');
	}
	/**
	 * 系统站内信列表
	 *
	 * @param 
	 * @return 
	 */
	public function systemmsgOp(){
		Language::read('member_home_message');
		$model_message	= Model('message');
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$message_array	= $model_message->listMessage(array('from_member_id'=>'0','message_type'=>'1','to_member_id'=>$_SESSION['member_id'],'no_del_member_id'=>$_SESSION['member_id']),$page);
		if (!empty($message_array) && is_array($message_array)){
			foreach ($message_array as $k=>$v){
				$v['message_open'] = '0';
				if (!empty($v['read_member_id'])){
					$tmp_readid_arr = explode(',',$v['read_member_id']);
					if (in_array($_SESSION['member_id'],$tmp_readid_arr)){
						$v['message_open'] = '1';
					}
				}
				$v['from_member_name'] = Language::get('home_message_system_message');
				$message_array[$k]	= $v;
			}
		}
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		Tpl::output('show_page',$page->show());
		Tpl::output('message_array',$message_array);
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		Tpl::output('isallowsend',$isallowsend);
		Tpl::output('drop_type','msg_system');
		self::profile_menu('message','system');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','systemmsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_box');
	}
	/**
	 * 发送站内信页面
	 *
	 * @param 
	 * @return 
	 */
	public function sendmsgOp(){
		Language::read('member_home_message');
		$referer_url = getReferer();
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		if (!$isallowsend){
			showMessage(Language::get('home_message_noallowsend'),$referer_url,'html','error');
		}
		$model_member	= Model('member');
		$member_name_string	= '';
		$member_id = intval($_GET['member_id']);
		if($member_id > 0) {
			//连接发放站内信页面
			$member_info	= $model_member->infoMember(array('member_id'=>$member_id));
			if (empty($member_info)){
				showMessage(Language::get('wrong_argument'),$referer_url,'html','error');
			}
			$member_name_string	= $member_info['member_name'];
			Tpl::output('member_name',$member_name_string);
		}else {
			//批量给好友发放站内信页面
			$friend_model = Model('sns_friend');
			$friend_list = $friend_model->listFriend(array('friend_frommid'=>"{$_SESSION['member_id']}"));
			Tpl::output('friend_list',$friend_list);
		}
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		self::profile_menu('message','sendmsg');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','sendmsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_send');
	}
	/**
	 * 站内信保存操作
	 *
	 * @param 
	 * @return 
	 */	
	public function savemsgOp() {
		Language::read('member_home_message');
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		if (!$isallowsend){
			showDialog(Language::get('home_message_noallowsend'));
		}
		if (chksubmit()) {
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["to_member_name"],"require"=>"true","message"=>Language::get('home_message_receiver_null')),
				array("input"=>$_POST["msg_content"],"require"=>"true","message"=>Language::get('home_message_content_null')),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showDialog($error);
			}
			$msg_content = trim($_POST['msg_content']);
			$membername_arr = explode(',',$_POST['to_member_name']);
			if (empty($membername_arr)){
				showDialog(Language::get('home_message_receiver_null'));
			}
			if (in_array("{$_SESSION['member_name']}",$membername_arr)){
				unset($membername_arr[array_search("{$_SESSION['member_name']}",$membername_arr)]);
			}
			//查询有效会员
			$member_model = Model('member');
			$member_list = $member_model->getMemberList(array('member_name'=>array('in', $membername_arr)));
			if (!empty($member_list)){
				$model_message	= Model('message');
				foreach ($member_list as $k=>$v){
					$insert_arr = array();
					$insert_arr['from_member_id'] = $_SESSION['member_id'];
					$insert_arr['from_member_name'] = $_SESSION['member_name'];
					$insert_arr['member_id'] = $v['member_id'];
					$insert_arr['to_member_name'] = $v['member_name'];
					$insert_arr['msg_content'] = $msg_content;
					$insert_arr['message_type'] = intval($_POST['msg_type']);
					$return = $model_message->saveMessage($insert_arr);
				}
			}else {
				showDialog(Language::get('home_message_receiver_null'));
			}
			if($_GET['type'] == 'sns_board'){
				$insert_arr['msg_id']		= $return;
				$insert_arr['msg_content']	= parsesmiles($insert_arr['msg_content']);
				if (strtoupper(CHARSET) == 'GBK') {
					$insert_arr['msg_content'] = Language::getUTF8($insert_arr['msg_content']);
				}
				$data = json_encode($insert_arr);
				$js = "leaveMsgSuccess(".$data.")";
				showDialog(Language::get('home_message_send_success'),'','succ',$js);
			}else{
				showDialog(Language::get('home_message_send_success'),'index.php?act=home&op=privatemsg','succ');
			}
		}
	}
	/**
	 * 普通站内信查看操作
	 *
	 * @param 
	 * @return 
	 */
	public function showmsgcommonOp() {
		Language::read('member_home_message');
		$model_message	= Model('message');
		$message_id =  intval($_GET['message_id']);
		$drop_type = trim($_GET['drop_type']);
		$referer_url = getReferer();
		if(!in_array($drop_type,array('msg_list')) || $message_id<=0){
			showMessage(Language::get('wrong_argument'),$referer_url,'html','error');
		}
		//查询站内信
		$param = array();
		$param['message_id'] = "$message_id";
		$param['to_member_id_common'] = "{$_SESSION['member_id']}";
		$param['no_message_state'] = "2";
		$message_info = $model_message->getRowMessage($param);
		if (empty($message_info)){
			showMessage(Language::get('home_message_no_record'),$referer_url,'html','error');
		}
		unset($param);
		if ($message_info['message_parent_id'] >0){
			//查询该站内信的父站内信
			$parent_array	= $model_message->getRowMessage(array('message_id'=>"{$message_info['message_parent_id']}",'message_type'=>'0','no_message_state'=>'2'));
			//查询该站内信的回复站内信
			$reply_array	= $model_message->listMessage(array('message_parent_id'=>"{$message_info['message_parent_id']}",'message_type'=>'0','no_message_state'=>'2'));
		}else {//此信息为父站内信
			$parent_array = $message_info;
			//查询回复站内信
			$reply_array	= $model_message->listMessage(array('message_parent_id'=>"$message_id",'message_type'=>'0','no_message_state'=>'2'));
		}
		//处理获取站内信数组
		$message_list = array();
		if (!empty($reply_array)){
			foreach ($reply_array as $k=>$v){
				$message_list[$v['message_id']] = $v;
			}
		}
		if (!empty($parent_array)){
			$message_list[$parent_array['message_id']] = $parent_array;
		}
		unset($parent_array);
		unset($reply_array);
		//更新已读状态
		$messageid_arr = array_keys($message_list);
		if (!empty($messageid_arr)){
			$messageid_str = "'".implode("','",$messageid_arr)."'";
			$model_message->updateCommonMessage(array('message_open'=>'1'),array('message_id_in'=>"$messageid_str"));
		}
		//更新未读站内信数量cookie值
		$cookie_name = 'msgnewnum'.$_SESSION['member_id'];
		$countnum = $model_message->countNewMessage($_SESSION['member_id']);
		setNcCookie($cookie_name,$countnum,2*3600);//保存2小时
		Tpl::output('message_num',$countnum);
		
    	//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);
		Tpl::output('message_id',$message_id);//点击的该条站内信编号
		Tpl::output('message_list',$message_list);//站内信列表
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		Tpl::output('isallowsend',$isallowsend);
    	self::profile_menu('message','showmsg');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','showmsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_view');
	}
	/**
	 * 系统站内信查看操作
	 *
	 * @param 
	 * @return 
	 */
	public function showmsgbatchOp() {
		Language::read('member_home_message');
		$model_message	= Model('message');
		$message_id =  intval($_GET['message_id']);
		$drop_type = trim($_GET['drop_type']);
		$referer_url = getReferer();
		if(!in_array($drop_type,array('msg_system','msg_seller')) || $message_id<=0){
			showMessage(Language::get('wrong_argument'),$referer_url,'html','error');
		}
		//查询站内信
		$param = array();
		$param['message_id'] = "$message_id";
		$param['to_member_id'] = "{$_SESSION['member_id']}";
		$param['no_del_member_id'] = "{$_SESSION['member_id']}";
		$message_info = $model_message->getRowMessage($param);
		if (empty($message_info)){
			showMessage(Language::get('home_message_no_record'),$referer_url,'html','error');
		}
		if ($drop_type == 'msg_system'){
			$message_info['from_member_name'] =  Language::get('home_message_system_message');
		}
		if ($drop_type == 'msg_seller'){
			//查询店铺信息
			$model_store = Model('store');
			$store_info = $model_store->getStoreInfo(array('member_id'=>"{$message_info['from_member_id']}"));
			$message_info['from_member_name'] =  $store_info['store_name'];
			$message_info['store_id'] =  $store_info['store_id'];
		}
		$message_list[0] = $message_info;
		Tpl::output('message_list',$message_list);//站内信列表
		//更新为已读信息
		$tmp_readid_str = '';
		if (!empty($message_info['read_member_id'])){
			$tmp_readid_arr = explode(',',$message_info['read_member_id']);
			if (!in_array($_SESSION['member_id'],$tmp_readid_arr)){
				$tmp_readid_arr[] = $_SESSION['member_id'];
			}
			foreach ($tmp_readid_arr as $readid_k=>$readid_v){
				if ($readid_v == ''){
					unset($tmp_readid_arr[$readid_k]);
				}
			}
			$tmp_readid_arr = array_unique ($tmp_readid_arr);//去除相同
			sort($tmp_readid_arr);//排序
			$tmp_readid_str = ",".implode(',',$tmp_readid_arr).",";
		}else {
			$tmp_readid_str = ",{$_SESSION['member_id']},";
		}
		$model_message->updateCommonMessage(array('read_member_id'=>$tmp_readid_str),array('message_id'=>"{$message_id}"));
		//更新未读站内信数量cookie值
		$cookie_name = 'msgnewnum'.$_SESSION['member_id'];
		$countnum = $model_message->countNewMessage($_SESSION['member_id']);
		setNcCookie($cookie_name,$countnum,2*3600);//保存2小时
		Tpl::output('message_num',$countnum);
		//查询新接收到普通的消息
		$newcommon = $this->receivedCommonNewNum();
		Tpl::output('newcommon',$newcommon);
		//查询新接收到系统的消息
		$newsystem = $this->receivedSystemNewNum();
		Tpl::output('newsystem',$newsystem);
		//查询新接收到卖家的消息
		$newpersonal = $this->receivedPersonalNewNum();
		Tpl::output('newpersonal',$newpersonal);	
		Tpl::output('drop_type',$drop_type);	
    	self::profile_menu('message','showmsg');
		Tpl::output('menu_sign','message');
		Tpl::output('menu_sign_url','index.php?act=home&op=message');
		Tpl::output('menu_sign1','showmsg');
		//查询会员信息
		$this->get_member_info();
		Tpl::output('header_menu_sign','message');//默认选中顶部“站内信”菜单
		Tpl::showpage('message_view');
	}
	/**
	 * 短消息回复保存
	 *
	 * @param 
	 * @return 
	 */
	public function savereplyOp() {
		Language::read('member_home_message');
		//查询会员是否允许发送站内信
		$isallowsend = $this->allowSendMessage($_SESSION['member_id']);
		if (!$isallowsend){
			if($_GET['inajax'] == 1){
				showDialog(Language::get('home_message_noallowsend'));
			}else{
				showMessage(Language::get('home_message_noallowsend'),'index.php?act=home&op=message','html','error');
			}
		}
		if ($_POST['form_submit'] == 'ok') {
			$message_id = intval($_POST["message_id"]);
			if ($message_id <=0){
				showMessage(Language::get('wrong_argument'),'index.php?act=home&op=message','html','error');
			}
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["msg_content"],"require"=>"true","message"=>Language::get('home_message_reply_content_null'))
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				if($_GET['inajax'] == 1){
					showDialog(Language::get('error'));
				}else{
					showMessage(Language::get('error').$error,'','html','error');
				}
			}
			$model_message	= Model('message');
			//查询站内信
			$param = array();
			$param['message_id'] = "$message_id";
			$param['no_message_state'] = "2";//未删除
			$message_info = $model_message->getRowMessage($param);
			if (empty($message_info)){
				if($_GET['inajax'] == 1){
					showDialog(Language::get('home_message_no_record'));
				}else{
					showMessage(Language::get('home_message_no_record').$error,'','html','error');
				}
			}
			//不能回复自己的站内信
			if ($message_info['from_member_id'] == $_SESSION['member_id']){
				showMessage(Language::get('home_message_no_record'),'','html','error');
			}
			$insert_arr = array();
			if ($message_info['message_parent_id'] > 0){
				$insert_arr['message_parent_id'] = $message_info['message_parent_id'];
			}else {
				$insert_arr['message_parent_id'] = $message_info['message_id'];
			}
			$insert_arr['from_member_id'] = $_SESSION['member_id'];
			$insert_arr['from_member_name'] = $_SESSION['member_name'];
			$insert_arr['member_id'] = $message_info['from_member_id'];
			$insert_arr['to_member_name'] = $message_info['from_member_name'];
			$insert_arr['msg_content'] = $_POST['msg_content'];
			$insert_state = $model_message->saveMessage($insert_arr);
			if ($insert_state){
				//更新父类站内信更新时间
				$update_arr = array();
				$update_arr['message_update_time'] = time();
				$update_arr['message_open'] = 1;
				$model_message->updateCommonMessage($update_arr,array('message_id'=>"{$insert_arr['message_parent_id']}"));
			}
			if($_GET['inajax'] == 1){
				$insert_arr['msg_id']		= $insert_state;
				if(strtoupper(CHARSET) == 'GBK'){
					$insert_arr['msg_content'] = Language::getUTF8($insert_arr['msg_content']);
				}
				$insert_arr['msg_content']	= parsesmiles($insert_arr['msg_content']);
				$data = json_encode($insert_arr);
				$js = "replyMsgSuccess(".$data.")";
				showDialog(Language::get('home_message_send_success'),'','succ',$js);
			}else{
				showMessage(Language::get('home_message_send_success'),'index.php?act=home&op=privatemsg');
			}
		}else {
			if($_GET['inajax'] == 1){
				showDialog(Language::get('home_message_reply_command_wrong'));
			}else{
				showMessage(Language::get('home_message_reply_command_wrong'),'','html','error');
			}
		}
	}
	/**
	 * 删除普通信
	 */
	public function dropcommonmsgOp() {
		Language::read('member_home_message');
		$message_id = trim($_GET['message_id']);
		$drop_type = trim($_GET['drop_type']);
		if(!in_array($drop_type,array('msg_private','msg_list','sns_msg')) || empty($message_id)) {
			showMessage(Language::get('wrong_argument'),'','html','error');
		}
		$messageid_arr = explode(',',$message_id);
		$messageid_str = '';
		if (!empty($messageid_arr)){
			$messageid_str = "'".implode("','",$messageid_arr)."'";
		}
		$model_message	= Model('message');
		$param	= array('message_id_in'=>$messageid_str);
		if($drop_type == 'msg_private'){
			$param['from_member_id'] = "{$_SESSION['member_id']}";
		}elseif($drop_type == 'msg_list'){
			$param['to_member_id_common']	= "{$_SESSION['member_id']}";
		}elseif($drop_type == 'sns_msg'){
			$param['from_to_member_id']	= $_SESSION['member_id'];
		}
		$drop_state	= $model_message->dropCommonMessage($param,$drop_type);
		if ($drop_state){
			//更新未读站内信数量cookie值
			$cookie_name = 'msgnewnum'.$_SESSION['member_id'];
			$countnum = $model_message->countNewMessage($_SESSION['member_id']);
			setNcCookie($cookie_name,$countnum,2*3600);//保存2小时
			showDialog(Language::get('home_message_delete_success'),'reload','succ');
		}else {
			showDialog(Language::get('home_message_delete_fail'),'','error');
		}
	}
	/**
	 * 删除批量站内信
	 */
	public function dropbatchmsgOp() {
		Language::read('member_home_message');
		$message_id = trim($_GET['message_id']);
		$drop_type = trim($_GET['drop_type']);
		if(!in_array($drop_type,array('msg_system','msg_seller')) || empty($message_id)){
			showDialog(Language::get('home_message_delete_request_wrong'));
		}
		$messageid_arr = explode(',',$message_id);
		$messageid_str = '';
		if (!empty($messageid_arr)){
			$messageid_str = "'".implode("','",$messageid_arr)."'";
		}
		$model_message	= Model('message');
		$param	= array('message_id_in'=>$messageid_str);
		if($drop_type == 'msg_system'){
			$param['message_type'] = '1';
			$param['from_member_id'] = '0';
		}
		if($drop_type == 'msg_seller'){
			$param['message_type'] = '2';
		}
		$drop_state	= $model_message->dropBatchMessage($param,$_SESSION['member_id']);
		if ($drop_state){
			//更新未读站内信数量cookie值
			$cookie_name = 'msgnewnum'.$_SESSION['member_id'];
			$countnum = $model_message->countNewMessage($_SESSION['member_id']);
			setNcCookie($cookie_name,$countnum,2*3600);//保存2小时
			showDialog(Language::get('home_message_delete_success'),'reload','succ');
		}else {
			showDialog(Language::get('home_message_delete_fail'),'','error');
		}
	}

	public function uploadOp() {
		if (!chksubmit()){
			redirect('index.php?act=home&op=avatar');
		}
		import('function.thumb');
		Language::read('member_home_member,cut');
		$lang	= Language::getLangContent();
		$member_id = $_SESSION['member_id'];

		//上传图片
		$upload = new UploadFile();
		$upload->set('thumb_width',	500);
		$upload->set('thumb_height',499);
		$ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
		$upload->set('file_name',"avatar_$member_id.$ext");
		$upload->set('thumb_ext','_new');
		$upload->set('ifremove',true);
		$upload->set('default_dir',ATTACH_AVATAR);
		if (!empty($_FILES['pic']['tmp_name'])){
			$result = $upload->upfile('pic');
			if (!$result){
				showMessage($upload->error,'','html','error');
			}
		}else{
			showMessage('upload failed','','html','error');
		}
		self::profile_menu('member','avatar');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','avatar');
		Tpl::output('newfile',$upload->thumb_image);
		Tpl::output('height',get_height(BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR.'/'.$upload->thumb_image));
		Tpl::output('width',get_width(BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR.'/'.$upload->thumb_image));
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_profile.avatar');
	}

	/**
	 * 裁剪
	 *
	 */
	public function cutOp(){
		if (chksubmit()){
			$thumb_width = 120;
			$x1 = $_POST["x1"];
			$y1 = $_POST["y1"];
			$x2 = $_POST["x2"];
			$y2 = $_POST["y2"];
			$w = $_POST["w"];
			$h = $_POST["h"];
			$scale = $thumb_width/$w;
			$_POST['newfile'] = str_replace('..', '', $_POST['newfile']);
			if (strpos($_POST['newfile'],"avatar_{$_SESSION['member_id']}_new.") !== 0) {
			    redirect('index.php?act=home&op=avatar');
			}
			$src = BASE_UPLOAD_PATH.DS.ATTACH_AVATAR.DS.$_POST['newfile'];
			$avatarfile = BASE_UPLOAD_PATH.DS.ATTACH_AVATAR.DS."avatar_{$_SESSION['member_id']}.jpg";
			import('function.thumb');
			$cropped = resize_thumb($avatarfile, $src,$w,$h,$x1,$y1,$scale);
			@unlink($src);
			Model()->table('member')->where(array('member_id'=>$_SESSION['member_id']))->update(array('member_avatar'=>'avatar_'.$_SESSION['member_id'].'.jpg'));
			$_SESSION['avatar'] = 'avatar_'.$_SESSION['member_id'].'.jpg';
			redirect('index.php?act=home&op=avatar');
		}
	}

	/**
	 * 更换头像
	 *
	 * @param 
	 * @return 
	 */
	public function avatarOp() {
		Language::read('member_home_member,cut');
		$model_member	= Model('member');
		$member_id = $_SESSION['member_id'];
		$member_avatar	= $model_member->getfby_member_id($member_id,'member_avatar');
		Tpl::output('member_avatar',$member_avatar);
		self::profile_menu('member','avatar');
		Tpl::output('menu_sign','profile');
		Tpl::output('menu_sign_url','index.php?act=home&op=member');
		Tpl::output('menu_sign1','avatar');
		Tpl::setLayout('member_pub_layout');
		Tpl::showpage('member_profile.avatar');
	}
	/**
	 * 重新申请开店
	 */
	public function store_reapplyOp(){
		Language::read('member_home_member');
		Model()->table('store')->update(array('store_id'=>$_SESSION['store_id'], 'store_state'=>2));
		showDialog(L('home_member_apply_succ'), 'reload', 'succ');
	}
	/**
	 * 
	 */
	/**
	 * 用户中心右边，小导航
	 * 
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return 
	 */
	private function profile_menu($menu_type,$menu_key='') {
		$menu_array		= array();
		switch ($menu_type) {
			case 'member':
				$menu_array	= array(
				1=>array('menu_key'=>'member',	'menu_name'=>Language::get('home_member_base_infomation'),'menu_url'=>'index.php?act=home&op=member'),
				2=>array('menu_key'=>'more',	'menu_name'=>Language::get('home_member_more'),'menu_url'=>'index.php?act=home&op=more'),
				3=>array('menu_key'=>'passwd',	'menu_name'=>Language::get('home_member_modify_password'),'menu_url'=>'index.php?act=home&op=passwd'),
				4=>array('menu_key'=>'email',	'menu_name'=>Language::get('home_member_modify_email'),'menu_url'=>'index.php?act=home&op=email'),
				5=>array('menu_key'=>'avatar',	'menu_name'=>Language::get('home_member_modify_avatar'),'menu_url'=>'index.php?act=home&op=avatar'));
				break;
			case 'message':
				$menu_array	= array(
				1=>array('menu_key'=>'message',	'menu_name'=>Language::get('home_message_received_message'),'menu_url'=>'index.php?act=home&op=message'),
				2=>array('menu_key'=>'private',	'menu_name'=>Language::get('home_message_private_message'),	'menu_url'=>'index.php?act=home&op=privatemsg'),
				3=>array('menu_key'=>'system',	'menu_name'=>Language::get('home_message_system_message'),	'menu_url'=>'index.php?act=home&op=systemmsg'),
				4=>array('menu_key'=>'close',	'menu_name'=>Language::get('home_message_close'),	'menu_url'=>'index.php?act=home&op=personalmsg'),
				);
				if($menu_key == 'sendmsg') {
					$menu_array[5] = array('menu_key'=>'sendmsg','menu_name'=>Language::get('home_message_send_message'),'menu_url'=>'index.php?act=home&op=sendmsg');
				}elseif($menu_key == 'showmsg') {
					$menu_array[5] = array('menu_key'=>'showmsg','menu_name'=>Language::get('home_message_view_message'),'menu_url'=>'#');
				}
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
