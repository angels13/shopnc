<?php
/**
 * chat
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
class web_chatModel extends Model{

	/**
	 * get chat msg
	 *
	 * @param
	 * @return array
	 */
	public function getMsgList($condition = array(),$page = 10){
		$result = $this->table('chat_msg')->where($condition)->page($page)->order('m_id')->select();
		return $result;
	}
	/**
	 * add chat msg
	 *
	 * @param
	 * @return int
	 */
	public function add($msg){
		$m_id = $this->table('chat_msg')->insert($msg);
		if ($m_id > 0){
			$msg['m_id'] = $m_id;
			unset($msg['r_state']);
			$this->table('chat_log')->insert($msg);
			return $m_id;
		}else {
			return 0;
		}
	}
	/**
	 * member info
	 *
	 * @param
	 * @return array
	 */
	public function getMember($member_id){
		if (intval($member_id) < 1){
			return false;
		}
		$member = $this->table('member')->field('member_id,member_name,member_avatar')->where(array('member_id'=> $member_id))->find();
		$member['store_name'] = '';
		$member['grade_id'] = '';
		$seller = $this->table('seller')->field('member_id,store_id,seller_name')->where(array('member_id'=> $member_id))->find();
		if (!empty($seller) && $seller['store_id'] > 0){
			$store_info = $this->table('store')->field('store_id,store_name,grade_id')->where(array('store_id'=> $seller['store_id']))->find();
			if (is_array($store_info) && !empty($store_info)){
				$member['store_id'] = $store_info['store_id'];
				$member['store_name'] = $store_info['store_name'];
				$member['seller_name'] = $seller['seller_name'];
				$member['grade_id'] = $store_info['grade_id'];
			}
		}
		return $member;
	}
	/**
	 * update chat msg
	 *
	 * @param
	 * @return bool
	 */
	public function updateMsg($condition,$data){
		$m_id = $condition['m_id'];
		if (intval($m_id) < 1){
			return false;
		}
		if (is_array($data)){
			$result = $this->table('chat_msg')->where($condition)->update($data);
			return $result;
		}else {
			return false;
		}
	}
	/**
	 * get chat log
	 *
	 * @param
	 * @return array
	 */
	public function getLogList($condition = array(),$page = 10){
		$result = $this->table('chat_log')->where($condition)->page($page)->order('m_id desc')->select();
		return $result;
	}
	/**
	 * get friends
	 *
	 * @param
	 * @return array
	 */
	public function getFriendList($condition = array(),$page = 50){
		$result = $this->table('sns_friend')->where($condition)->page($page)->order('friend_addtime desc')->select();
		return $result;
	}
	/**
	 * get recent msg
	 *
	 * @param
	 * @return array
	 */
	public function getRecentList($condition = array(),$limit = 5){
		$result = $this->table('chat_msg')->field('t_id,t_name,max(add_time) as addtime')->group('t_id')->where($condition)->order('addtime desc')->limit($limit)->select();
		return $result;
	}
	/**
	 * get recent from msg
	 *
	 * @param
	 * @return array
	 */
	public function getRecentFromList($condition = array(),$limit = 5){
		$result = $this->table('chat_msg')->field('f_id,f_name,max(add_time) as addtime')->group('f_id')->where($condition)->order('addtime desc')->limit($limit)->select();
		return $result;
	}

}