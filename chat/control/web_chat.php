<?php
/**
 * web_chat
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

class web_chatControl extends BaseControl {
	public function __construct(){
		parent::__construct();
		Language::read('member_chat');
		if (strtoupper(CHARSET) == 'GBK'){
			$_GET = Language::getGBK($_GET);
			$_POST = Language::getGBK($_POST);
		}
	}
	/**
	 * add msg
	 *
	 */
	public function send_msgOp(){
		$member = array();
		$model_chat	= Model('web_chat');
		if(empty($_POST)) $_POST = $_GET;
		$member_id = $_SESSION['member_id'];
		$member_name = $_SESSION['member_name'];
		$f_id = intval($_POST['f_id']);
		$t_id = intval($_POST['t_id']);
		$t_name = trim($_POST['t_name']);
		if (($member_id < 1) || ($member_id != $f_id)) $this->error(Language::get('nc_member_chat_login'));
		$member = $model_chat->getMember($t_id);
		if ($t_name != $member['member_name']) $this->error(Language::get('nc_member_chat_name_error'));
		
		$msg = array();
		$msg['f_id'] = $f_id;	
		$msg['f_name'] = $member_name;	
		$msg['f_ip'] = getIp();	
		$msg['t_id'] = $t_id;	
		$msg['t_name'] = $t_name;	
		$msg['t_msg'] = trim($_POST['t_msg']);
		$msg['r_state'] = '2';//state:1--read ,2--unread
		$msg['add_time'] = time();
		if ($msg['t_msg'] != '') $state = $model_chat->add($msg);
		if($state) {
			$msg['m_id'] = $state;	
			$msg['add_time'] =  date('Y-m-d H:i:s',$msg['add_time']);
			$this->json($msg);
		} else {
			$this->error(Language::get('nc_member_chat_add_error'));
		}
	}
	/**
	 * friends info
	 *
	 */
	public function get_user_listOp(){
		$member_list = array();
		$model_chat	= Model('web_chat');
		
		$member_id = $_SESSION['member_id'];
		$member_name = $_SESSION['member_name'];
		$f_id = intval($_GET['f_id']);
		if (($member_id < 1) || ($member_id != $f_id)) $this->error(Language::get('nc_member_chat_login'));
		$n = intval($_GET['n']);
		if ($n < 1) $n = 50;
		$list = $model_chat->getFriendList(array('friend_frommid'=>$f_id),$n);
		if(!empty($list) && is_array($list)) {
			foreach($list as $k => $v){
				$member = array();
				$u_id = $v['friend_tomid'];
				$member['u_id'] = $u_id;
				$member['u_name'] = $v['friend_tomname'];
				$member['avatar'] = getMemberAvatarForID($u_id);
				$member['friend'] = 1;
				$member_list[$u_id] = $member;
			}
		}
		$add_time = date("Y-m-d");
		$add_time30 = strtotime($add_time)-60*60*24*30;
		$list = $model_chat->getRecentList(array('f_id'=>$f_id,'add_time'=>array('egt',$add_time30)),10);
		if(!empty($list) && is_array($list)) {
			foreach($list as $k => $v){
				$member = array();
				$u_id = $v['t_id'];
				$member['u_id'] = $u_id;
				$member['u_name'] = $v['t_name'];
				$member['avatar'] = getMemberAvatarForID($u_id);
				$member['recent'] = 1;
				$member['time'] = date("Y-m-d H:i:s",$v['addtime']);
				if(empty($member_list[$u_id])) {
					$member_list[$u_id] = $member;
				} else {
					$member_list[$u_id]['recent'] = 1;
					$member_list[$u_id]['time'] = date("Y-m-d H:i:s",$v['addtime']);
				}
			}
		}
		$list = $model_chat->getRecentFromList(array('t_id'=>$f_id,'add_time'=>array('egt',$add_time30)),10);
		if(!empty($list) && is_array($list)) {
			foreach($list as $k => $v){
				$member = array();
				$u_id = $v['f_id'];
				$member['u_id'] = $u_id;
				$member['u_name'] = $v['f_name'];
				$member['avatar'] = getMemberAvatarForID($u_id);
				$member['recent'] = 1;
				$member['time'] = date("Y-m-d H:i:s",$v['addtime']);
				if(empty($member_list[$u_id])) {
					$member_list[$u_id] = $member;
				} else {
					$member_list[$u_id]['recent'] = 1;
					$member_list[$u_id]['time'] = date("Y-m-d H:i:s",$v['addtime']);
				}
			}
		}
		$this->json($member_list);
	}
	/**
	 * member info
	 *
	 */
	public function get_infoOp(){
		if ($_SESSION['member_id'] < 1) $this->error(Language::get('nc_member_chat_login'));
		$val = '';
		$member = array();
		$model_chat	= Model('web_chat');
		$types = array('member_id','member_name','store_id','member');
		$key = $_GET['t'];
		$member_id = intval($_GET['u_id']);
		if(trim($key) != '' && in_array($key,$types)){
			$member = $model_chat->getMember($member_id);
			$member['member_avatar'] = getMemberAvatar($member['member_avatar']);
			$this->json($member);
		}
	}
	/**
	 * chat log
	 *
	 */
	public function get_chat_logOp(){
		$member_id = $_SESSION['member_id'];
		$f_id = intval($_GET['f_id']);
		$t_id = intval($_GET['t_id']);
		$page = intval($_GET['page']);
		if (($member_id < 1) || ($member_id != $f_id)) $this->error(Language::get('nc_member_chat_login'));
		if ($page < 1) $page = 20;
		$add_time_to = date("Y-m-d");
		$time_from = array();
		$time_from['7'] = strtotime($add_time_to)-60*60*24*7;
		$time_from['15'] = strtotime($add_time_to)-60*60*24*15;
		$time_from['30'] = strtotime($add_time_to)-60*60*24*30;
		
		$key = $_GET['t'];
		if(trim($key) != '' && array_key_exists($key,$time_from)){
			$model_chat	= Model('web_chat');
			$chat_log = array();
			$list = array();
			$condition_sql = " add_time >= '".$time_from[$key]."' ";
			$condition_sql .= " and ((f_id = '".$f_id."' and t_id = '".$t_id."') or (f_id = '".$t_id."' and t_id = '".$f_id."'))";
			$list = $model_chat->getLogList($condition_sql,$page);
			
			if(!empty($list) && is_array($list)) {
				foreach($list as $k => $v){
					$v['time'] = date("Y-m-d H:i:s",$v['add_time']);
					$list[$k] = $v;
				}
			}
			$chat_log['list'] = $list;
			$chat_log['total_page'] = $model_chat->gettotalpage();
			$this->json($chat_log);
		}
	}
	/**
	 * get session
	 *
	 */
	public function get_sessionOp(){
		$key = $_GET['key'];
		$val = '';
		if (!empty($_SESSION[$key])) $val = $_SESSION[$key];
		echo $val;
		exit;
	}
	/**
	 * json
	 *
	 */
	public function json($json){
		if (strtoupper(CHARSET) == 'GBK'){
			$json = Language::getUTF8($json);//GBKtoUTF-8
		}
		echo $_GET['callback'].'('.json_encode($json).')';
		exit;
	}
	/**
	 * error
	 *
	 */
	public function error($msg = ''){
		$this->json(array('error'=> $msg));
	}
}
