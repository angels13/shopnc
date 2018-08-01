<?php
/**
 * 代金券
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class pointvoucherControl extends BaseHomeControl {
	private $templatestate_arr;
	public function __construct() {
		parent::__construct();
		//读取语言包
		Language::read('home_pointprod,home_voucher');
		//判断系统是否开启积分和代金券功能
		if (C('points_isuse') != 1 || C('voucher_allow') !=1){
			showMessage(Language::get('voucher_pointunavailable'),'index.php','html','error');
		}
		//代金券模板状态
		$this->templatestate_arr = array('usable'=>array(1,Language::get('voucher_templatestate_usable')),'disabled'=>array(2,Language::get('voucher_templatestate_disabled')));
		//领取的代金券状态
		$this->voucherstate_arr = array('unused'=>array(1,Language::get('voucher_voucher_state_unused')),'used'=>array(2,Language::get('voucher_voucher_state_used')),'expire'=>array(3,Language::get('voucher_voucher_state_expire')));
		Tpl::output('index_sign','pointprod');
	}
	public function indexOp(){
		$this->pointvoucherOp();
	}
	/**
	 * 代金券列表
	 */
	public function pointvoucherOp(){
		$model = Model();
		//查询已兑换代金券券数量
		$vouchercount = 0;
		if($_SESSION['is_login'] == '1'){
			//查询会员信息
			$member_info = $model->table('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
			Tpl::output('member_info',$member_info);
			$vouchercount = $model->table('voucher')->where(array('voucher_owner_id'=>$_SESSION['member_id'],'voucher_state'=>$this->voucherstate_arr['unused'][0]))->count();
		}
		Tpl::output('vouchercount',$vouchercount);
		//查询代金券面额
		$pricelist =  $model->table('voucher_price')->order('voucher_price asc')->select();
		$voucherlist = array();
		if (!empty($pricelist)){
			foreach ($pricelist as $k=>$v){
				$voucherlist[$v['voucher_price']]['price'] = $v;
			}
		}
		//查询代金券列表
		$field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
		$on = 'voucher_template.voucher_t_store_id=store.store_id';
		$voucher = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_state'=>$this->templatestate_arr['usable'][0],'voucher_t_end_date'=>array('gt',time())))->select();
		if (!empty($voucher)){
			foreach ($voucher as $k=>$v){
				if (!empty($v['voucher_t_customimg'])){
					$v['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.ATTACH_VOUCHER.DS.$v['voucher_t_store_id'].DS.$v['voucher_t_customimg'];
				}else{
					$v['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(240);
				}
				$v['voucher_t_limit'] = intval($v['voucher_t_limit']);
				if (!empty($voucherlist[$v['voucher_t_price']])){
					$voucherlist[$v['voucher_t_price']]['voucher'][] = $v;
				}
			}
		}
		Tpl::output('voucherlist',$voucherlist);
		Tpl::showpage('pointvoucher');
	}
	/**
	 * 兑换代金券
	 */
	public function voucherexchangeOp(){
		$vid = intval($_GET['vid']);
		if($vid <= 0){
			$vid = intval($_POST['vid']);
		}
		if($_SESSION['is_login'] != '1'){
			$js = "login_dialog();";
			showDialog('','','js',$js);
		}elseif ($_GET['dialog']){
			$js = "CUR_DIALOG = ajax_form('vexchange', '".Language::get('home_voucher_exchangtitle')."', 'index.php?act=pointvoucher&op=voucherexchange&vid={$vid}', 480);";
			showDialog('','','js',$js);
			die;
		}
		$result = true;
		$message = "";
		if ($vid <= 0){
			$result = false;
			$message = Language::get('wrong_argument');
		}
		if ($result){
			//查询代金券信息
			$model = Model();
			$field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
			$on = 'voucher_template.voucher_t_store_id=store.store_id';
			$voucher_info = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_id'=>$vid,'voucher_t_state'=>$this->templatestate_arr['usable'][0],'voucher_t_end_date'=>array('gt',time())))->find();
			if (empty($voucher_info)){
				$result = false;
				$message = Language::get('home_voucher_recorderror');
			}else {
				//查询会员信息
				$member_info = $model->table('member')->field('member_points')->where(array('member_id'=>$_SESSION['member_id']))->find();
				Tpl::output('member_info',$member_info);
				Tpl::output('voucher_info',$voucher_info);
			}
		}
		Tpl::output('message',$message);
		Tpl::output('result',$result);
		Tpl::showpage('pointvoucher.exchange','null_layout');
	}
	/**
	 * 兑换代金券保存信息
	 *
	 */
	public function voucherexchange_saveOp(){
		if($_SESSION['is_login'] != '1'){
			$js = "login_dialog();";
			showDialog('','','js',$js);
		}
		$vid = intval($_POST['vid']);
		$js = "DialogManager.close('vexchange');";
		if ($vid <= 0){
			showDialog(Language::get('wrong_argument'),'','error',$js);
		}
		$model = Model('voucher');
		//查询代金券信息
		$voucher_info = $model->getUsableTemplateInfo($vid);
		if (empty($voucher_info)){
			showDialog(Language::get('home_voucher_recorderror'),'','error',$js);
		}
		//验证是否为店铺自己
		if ($_SESSION['store_id'] == $voucher_info['voucher_t_store_id']){
			showDialog(Language::get('home_voucher_unableexchangself'),'','error',$js);
		}
		$voucher_list= $model->table('voucher')->where(array('voucher_owner_id'=>$_SESSION['member_id'],'voucher_store_id'=>$voucher_info['voucher_t_store_id'],'voucher_end_date'=>array('gt',time())))->select();
		if (!empty($voucher_list)){
			$voucher_count = 0;//在该店铺兑换的代金券数量
			$voucherone_count = 0;//该张代金券兑换的次数
			foreach ($voucher_list as $k=>$v){
				if ($v['voucher_state'] == 1){
					$voucher_count += 1;
				}
				if ($v['voucher_t_id'] == $voucher_info['voucher_t_id']){
					$voucherone_count += 1;
				}
			}
			//买家最多只能拥有同一个店铺尚未消费抵用的店铺代金券最大数量的验证
			if ($voucher_count >= intval(C('promotion_voucher_buyertimes_limit'))){
				$message = sprintf(Language::get('home_voucher_alreadyfull'),C('promotion_voucher_buyertimes_limit'));
				showDialog($message,'','error',$js);
			}
			//同一张代金券最多能兑换的次数
			if (!empty($voucher_info['voucher_t_eachlimit']) && $voucherone_count >= $voucher_info['voucher_t_eachlimit']){
				$message = sprintf(Language::get('home_voucher_reachedeachlimit'),$voucher_info['voucher_t_eachlimit']);
				showDialog($message,'','error',$js);
			}
		}
		//查询会员信息
		$member_info = $model->table('member')->field('member_points')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if (empty($member_info)){
			showDialog(Language::get('wrong_argument'),'','error',$js);
		}
		if (intval($member_info['member_points']) < intval($voucher_info['voucher_t_points'])){
			showDialog(Language::get('home_voucher_pointsnotenough'),'','error',$js);
		}
		//添加代金券信息
		$insert_arr = array();
		$insert_arr['voucher_code'] = $model->get_voucher_code();
		$insert_arr['voucher_t_id'] = $voucher_info['voucher_t_id'];
		$insert_arr['voucher_title'] = $voucher_info['voucher_t_title'];
		$insert_arr['voucher_desc'] = $voucher_info['voucher_t_desc'];
		$insert_arr['voucher_start_date'] = time();
		$insert_arr['voucher_end_date'] = $voucher_info['voucher_t_end_date'];
		$insert_arr['voucher_price'] = $voucher_info['voucher_t_price'];
		$insert_arr['voucher_limit'] = $voucher_info['voucher_t_limit'];
		$insert_arr['voucher_store_id'] = $voucher_info['voucher_t_store_id'];
		$insert_arr['voucher_state'] = 1;
		$insert_arr['voucher_active_date'] = time();
		$insert_arr['voucher_owner_id'] = $_SESSION['member_id'];
		$insert_arr['voucher_owner_name'] = $_SESSION['member_name'];
		//扣除会员积分
		$points_model = Model('points');
		$points_arr['pl_memberid'] = $_SESSION['member_id'];
		$points_arr['pl_membername'] = $_SESSION['member_name'];
		$points_arr['pl_points'] = -$voucher_info['voucher_t_points'];
		$points_arr['point_ordersn'] = $insert_arr['voucher_code'];
		$points_arr['pl_desc'] = Language::get('home_voucher').$insert_arr['voucher_code'].Language::get('points_pointorderdesc');
		$points_model->savePointsLog('app',$points_arr,true);
		$result = $model->table('voucher')->insert($insert_arr);
		if ($result){
			//代金券模板的兑换数增加
			$model->table('voucher_template')->where(array('voucher_t_id'=>$voucher_info['voucher_t_id']))->update(array('voucher_t_giveout'=>array('exp','voucher_t_giveout+1')));
			showDialog(Language::get('home_voucher_exchangsucc'),'','succ',$js);
		}else {
			showDialog(Language::get('home_voucher_exchangfail'),'','error',$js);
		}
	}
}
