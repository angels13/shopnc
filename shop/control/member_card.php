<?php
/**
 * The AJAX call member information
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */

class member_cardControl extends BaseHomeControl{
	/**
	 * AJAX for membership information
	 */
	public function indexOp(){
		$ownid	= $_SESSION['member_id'];
		$uid	= intval($_GET['uid']);
		
		$model = Model();
		$member_info = $model->table('member')->field('member_id, member_name, member_truename, member_sex, member_email, member_qq, member_ww, member_areainfo, member_birthday, member_privacy')->find($uid);
		if(empty($member_info)){
			echo 'false';exit;
		}
		if($member_info['member_privacy'] != ''){
			$member_info['member_privacy'] = unserialize($member_info['member_privacy']);
		}

		if($ownid == $uid){
			$followed = 2;
		}else{
			// Whether to pay attention to yourself(own)
			$followed = 0;	// 0 stranger, 1 friend, 2 own
			$where = array();
			$where['friend_frommid']	= $uid;
			$where['friend_tomid']		= $ownid;
			$friend_info = $model->table('sns_friend')->where($where)->find();
			if(!empty($friend_info)){
				$followed = 1;
			}
		}
		
		$data = array();
		$data['id']			= $member_info['member_id'];
		$data['name']		= $member_info['member_name'];
		$data['avatar']		= getMemberAvatarForID($member_info['member_id']);
		$data['truename']	= ($followed >= intval($member_info['member_privacy']['truename'])) ? $member_info['member_truename'] : '';
		$data['sex']		= ($followed >= intval($member_info['member_privacy']['sex'])) ? $member_info['member_sex'] : 3;
		$data['email']		= ($followed >= intval($member_info['member_privacy']['email'])) ? $member_info['member_email'] : L('home_member_privary');
		$data['qq']			= ($followed >= intval($member_info['member_privacy']['qq'])) ? $member_info['member_qq'] : '';
		$data['ww']			= ($followed >= intval($member_info['member_privacy']['ww'])) ? $member_info['member_ww'] : '';
		$data['areainfo']	= ($followed >= intval($member_info['member_privacy']['area'])) ? $member_info['member_areainfo'] : L('home_member_privary');
		$data['birthday']	= ($followed >= intval($member_info['member_privacy']['birthday'])) ? $member_info['member_birthday'] : L('home_member_privary');
		
		switch ($_GET['from']){
			case 'shop':
				$data['url']= SHOP_SITE_URL;
				break;
			case 'cms':
				$data['url']= CMS_SITE_URL;
				break;
			case 'circle':
				$data['url']= CIRCLE_SITE_URL;
				break;
			case 'microshop':
				$data['url']= MICROSHOP_SITE_URL;
				break;
			default:
				$data['url']= '';
				break;
		}
		if ($ownid == $uid){
			$data['follow'] = 2;	// 0 stranger, 1 friend, 2 own
		}else{
			// Whether to pay attention to me
			$where = array();
			$where['friend_frommid']	= $ownid;
			$where['friend_tomid']		= $uid;
			$friend_info = $model->table('sns_friend')->where($where)->find();
			$data['follow']	= (!empty($friend_info)) ? 1 : 0;
		}
		// Pay attention to the number of
		$data['attention_count'] = $model->table('sns_friend')->where(array('friend_frommid'=>$uid))->count();

		// Number of fans
		$data['fans_count']	= $model->table('sns_friend')->where(array('friend_tomid'=>$uid))->count();
		
		echo $_GET['callback'].'('.json_encode($data).')';
		//Tpl::output('data', $data);
		//Tpl::showpage('member_card','null_layout');
	}
	
	public function mcard_infoOp(){
		$uid	= intval($_GET['uid']);
		$member_info = Model()->table('member')->field('member_credit')->find($uid);
		if(empty($member_info)){
			echo 'false';exit;
		}
		echo json_encode($member_info);
	}
}
