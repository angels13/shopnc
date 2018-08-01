<?php
/**
 * 预存款管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class predepositControl extends BaseMemberControl {
	public function __construct(){
		parent::__construct();
		Language::read('member_member_predeposit');
		Tpl::setLayout('member_pub_layout');
	}

	/**
	 * 充值添加
	 */
	public function recharge_addOp(){
		if (!chksubmit()){
		    //信息输出
		    self::profile_menu('recharge','recharge_list');
		    Tpl::output('menu_sign','predepositrecharge');
		    Tpl::output('menu_sign_url','index.php?act=predeposit');
		    Tpl::output('menu_sign1','predeposit_rechargeadd');
		    Tpl::showpage('member_pd.add');
		    exit();
		}
		$pdr_amount = abs(floatval($_POST['pdr_amount']));
		if ($pdr_amount <= 0) {
		    showMessage(Language::get('predeposit_recharge_add_pricemin_error'),'','html','error');
		}
        $model_pdr = Model('predeposit');
        $data = array();
        $data['pdr_sn'] = $pay_sn = $model_pdr->makeSn();
        $data['pdr_member_id'] = $_SESSION['member_id'];
        $data['pdr_member_name'] = $_SESSION['member_name'];
        $data['pdr_amount'] = $pdr_amount;
        $data['pdr_add_time'] = TIMESTAMP;
        $insert = $model_pdr->addPdRecharge($data);
        if ($insert) {
            //转向到商城支付页面
            redirect('index.php?act=buy&op=pd_pay&pay_sn='.$pay_sn);
        }
	}

	/**
	 * 充值列表
	 */
    public function indexOp(){
        $condition = array();
        $condition['pdr_member_id']	= $_SESSION['member_id'];
        if (!empty($_GET['pdr_sn'])) {
            $condition['pdr_sn'] = $_GET['pdr_sn'];
        }

        //查询用户信息
        $model_member = Model('member');
        $member_info = $model_member->infoMember(array('member_id'=>"{$_SESSION['member_id']}"));
        if (!is_array($member_info) || count($member_info)<=0){
            showMessage(Language::get('predeposit_userrecord_error'),'index.php?act=member_snsindex','html','error');
        }
        Tpl::output('member_info',$member_info);

        $model_pd = Model('predeposit');
        $list = $model_pd->getPdRechargeList($condition,20,'*','pdr_id desc');

        self::profile_menu('recharge','recharge_list');
        Tpl::output('menu_sign','predepositrecharge');
        Tpl::output('menu_sign_url','index.php?act=predeposit');
        Tpl::output('menu_sign1','predeposit_rechargelist');
        Tpl::output('list',$list);
        Tpl::output('show_page',$model_pd->showpage());

        Tpl::showpage('member_pd.list');
    }

    /**
     * 查看充值详细
     *
     */
    public function recharge_showOp(){
        $pdr_id = intval($_GET["id"]);
        if ($pdr_id <= 0){
            showDialog(Language::get('predeposit_parameter_error'),'','error');
        }
    
        $model_pd = Model('predeposit');
        $condition = array();
        $condition['pdr_member_id'] = $_SESSION['member_id'];
        $condition['pdr_id'] = $pdr_id;
        $condition['pdr_payment_state'] = 1;
        $info = $model_pd->getPdRechargeInfo($condition);
        if (!$info){
            showDialog(Language::get('predeposit_record_error'),'','error');
        }
        Tpl::output('info',$info);
        self::profile_menu('rechargeinfo','rechargeinfo');
        Tpl::output('menu_sign','predepositrecharge');
        Tpl::output('menu_sign_url','index.php?act=predeposit');
        Tpl::output('menu_sign1','predeposit_rechargeinfo');
        Tpl::showpage('member_pd.info');
    }

	/**
	 * 删除充值记录
	 *
	 */
	public function recharge_delOp(){
		$pdr_id = intval($_GET["id"]);
		if ($pdr_id <= 0){
		    showDialog(Language::get('predeposit_parameter_error'),'','error');
		}

		$model_pd = Model('predeposit');
		$condition = array();		
		$condition['pdr_member_id'] = $_SESSION['member_id'];
		$condition['pdr_id'] = $pdr_id;
		$condition['pdr_payment_state'] = 0;
		$result = $model_pd->delPdRecharge($condition);
		if ($result){
			showDialog(Language::get('nc_common_del_succ'),'reload','succ','CUR_DIALOG.close()');
		}else {
			showDialog(Language::get('nc_common_del_fail'),'','error');
		}
	}

	/**
	 * 预存款变更日志
	 */
	public function pd_log_listOp(){
		$model_pd = Model('predeposit');
		$condition = array();
		$condition['lg_member_id'] = $_SESSION['member_id'];
		$list = $model_pd->getPdLogList($condition,20,'*','lg_id desc');

		//查询用户信息
		$model_member = Model('member');
		$member_info = $model_member->infoMember(array('member_id'=>$_SESSION['member_id']));
		if (!is_array($member_info) || count($member_info)<=0){
			showMessage(Language::get('predeposit_userrecord_error'),'index.php?act=member_snsindex','html','error');			
		}

		//信息输出
		self::profile_menu('log','loglist');
		Tpl::output('menu_sign','pd_log_list');
		Tpl::output('menu_sign_url','index.php?act=predeposit&op=pd_log_list');
		Tpl::output('menu_sign1','predeposit_loglist');
		Tpl::output('show_page',$model_pd->showpage());
		Tpl::output('list',$list);
		Tpl::output('member_info',$member_info);
		Tpl::showpage('member_pd_log.list');	
	}

	/**
	 * 申请提现
	 */
	public function pd_cash_addOp(){
		if (chksubmit()){
			$obj_validate = new Validate();
			$pdc_amount = abs(floatval($_POST['pdc_amount']));
			$validate_arr[] = array("input"=>$pdc_amount, "require"=>"true",'validator'=>'Compare','operator'=>'>=',"to"=>'0.01',"message"=>Language::get('predeposit_cash_add_pricemin_error'));			
			$validate_arr[] = array("input"=>$_POST["pdc_bank_name"], "require"=>"true","message"=>Language::get('predeposit_cash_add_shoukuanbanknull_error'));
			$validate_arr[] = array("input"=>$_POST["pdc_bank_no"], "require"=>"true","message"=>Language::get('predeposit_cash_add_shoukuanaccountnull_error'));
			$validate_arr[] = array("input"=>$_POST["pdc_bank_user"], "require"=>"true","message"=>Language::get('predeposit_cash_add_shoukuannamenull_error'));
			$obj_validate -> validateparam = $validate_arr;
			$error = $obj_validate->validate();			
			if ($error != ''){
				showDialog($error,'','error');
			}
			$model_pd = Model('predeposit');
			//验证金额是否足够
			$model_member = Model('member');
			$member_info = $model_member->infoMember(array('member_id'=>$_SESSION['member_id']));
			if (floatval($member_info['available_predeposit']) < $pdc_amount){
				showDialog(Language::get('predeposit_cash_shortprice_error'),'index.php?act=predeposit&op=pd_cash_list','error');
			}
			try {
			    $model_pd->beginTransaction();
			    $pdc_sn = $model_pd->makeSn();
    			$data = array();
    			$data['pdc_sn'] = $pdc_sn;
    			$data['pdc_member_id'] = $_SESSION['member_id'];
    			$data['pdc_member_name'] = $_SESSION['member_name'];
    			$data['pdc_amount'] = $pdc_amount;
    			$data['pdc_bank_name'] = $_POST['pdc_bank_name'];
    			$data['pdc_bank_no'] = $_POST['pdc_bank_no'];
    			$data['pdc_bank_user'] = $_POST['pdc_bank_user'];
    			$data['pdc_add_time'] = TIMESTAMP;
    			$data['pdc_payment_state'] = 0;
    			$insert = $model_pd->addPdCash($data);
    			if (!$insert) {
    			    throw new Exception(Language::get('predeposit_cash_add_fail'));
    			}
    			//冻结可用预存款
    			$data = array();
    			$data['member_id'] = $member_info['member_id'];
    			$data['member_name'] = $member_info['member_name'];
    			$data['amount'] = $pdc_amount;
    			$data['order_sn'] = $pdc_sn;
    			$model_pd->changePd('cash_apply',$data);
    			$model_pd->commit();
    			showDialog(Language::get('predeposit_cash_add_success'),'index.php?act=predeposit&op=pd_cash_list','succ','CUR_DIALOG.close()');
			} catch (Exception $e) {
			    $model_pd->rollback();
			    showDialog($e->getMessage(),'index.php?act=predeposit&op=pd_cash_list','error');
			}
		}else {
			//查询会员信息
			$member_model = Model('member');
			$member_info = $member_model->infoMember(array('member_id'=>$_SESSION['member_id']),'member_id,available_predeposit');
			Tpl::output('member_info',$member_info);
			self::profile_menu('cashadd','cashadd');
			Tpl::output('menu_sign','predepositcash');
			Tpl::output('menu_sign_url','index.php?act=predeposit&op=pd_cash_add');
			Tpl::output('menu_sign1','predeposit_cashadd');
			Tpl::showpage('member_pd_cash.add');
		}
	}

	/**
	 * 提现列表
	 */
	public function pd_cash_listOp(){
		$condition = array();
		$condition['pdc_member_id'] =  $_SESSION['member_id'];
		if (!empty($_GET['sn_search'])) {
		    $condition['pdc_sn'] = $_GET['sn_search'];
		}
		if (isset($_GET['paystate_search'])){
			$condition['pdc_payment_state'] = intval($_GET['paystate_search']);
		}
		$model_pd = Model('predeposit');
		$cash_list = $model_pd->getPdCashList($condition,30,'*','pdc_id desc');

		self::profile_menu('cash','cashlist');
		Tpl::output('menu_sign','predepositcash');
		Tpl::output('menu_sign_url','index.php?act=predeposit&op=pd_cash_list');
		Tpl::output('menu_sign1','predeposit_cashlist');
		Tpl::output('list',$cash_list);
		Tpl::output('show_page',$model_pd->showpage());
		Tpl::showpage('member_pd_cash.list');
	}

	/**
	 * 提现记录详细 
	 */
	public function pd_cash_infoOp(){
		$pdc_id = intval($_GET["id"]);
		if ($pdc_id <= 0){
			showMessage(Language::get('predeposit_parameter_error'),'index.php?act=predeposit&op=pd_cash_list','html','error');
		}
		$model_pd = Model('predeposit');
		$condition = array();
		$condition['pdc_member_id'] = $_SESSION['member_id'];
		$condition['pdc_id'] = $pdc_id;
		$info = $model_pd->getPdCashInfo($condition);
		if (empty($info)){
			showMessage(Language::get('predeposit_record_error'),'index.php?act=predeposit&op=pd_cash_list','html','error');
		}

		self::profile_menu('cashinfo','cashinfo');
		Tpl::output('menu_sign','predepositcash');
		Tpl::output('menu_sign_url','index.php?act=predeposit&op=pd_cash_list');
		Tpl::output('menu_sign1','predeposit_cashinfo');
		Tpl::output('info',$info);
		Tpl::showpage('member_pd_cash.info');
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key=''){
		$menu_array	= array();
		switch ($menu_type) {
			case 'recharge':
				$menu_array	= array(
				array('menu_key'=>'recharge_list','menu_name'=>Language::get('nc_member_path_predeposit_rechargelist'),	'menu_url'=>'index.php?act=predeposit&op=index'),
				);
				break;
			case 'rechargeinfo':
				$menu_array	= array(
					2=>array('menu_key'=>'index','menu_name'=>Language::get('nc_member_path_predeposit_rechargelist'),	'menu_url'=>'index.php?act=predeposit&op=index'),
					3=>array('menu_key'=>'rechargeinfo','menu_name'=>Language::get('nc_member_path_predeposit_rechargeinfo'),	'menu_url'=>'')
				);
				break;
			case 'log':
				$menu_array	= array(
					1=>array('menu_key'=>'loglist','menu_name'=>Language::get('nc_member_path_predeposit_loglist'),	'menu_url'=>'index.php?act=predeposit&op=pd_log_list')
				);
				break;
			case 'cash':
				$menu_array	= array(
					array('menu_key'=>'cashlist','menu_name'=>Language::get('nc_member_path_predeposit_cashlist'),	'menu_url'=>'index.php?act=predeposit&op=pd_cash_list')
				);
				break;
				case 'cashadd':
				    $menu_array	= array(
				    array('menu_key'=>'cashlist','menu_name'=>Language::get('nc_member_path_predeposit_cashlist'),	'menu_url'=>'index.php?act=predeposit&op=pd_cash_list'),	    array('menu_key'=>'cashadd','menu_name'=>Language::get('nc_member_path_predeposit_cashadd'),	'menu_url'=>'index.php?act=predeposit&op=pd_cash_add')
		);
				break;
			case 'cashinfo':
				$menu_array	= array(
					array('menu_key'=>'cashlist','menu_name'=>Language::get('nc_member_path_predeposit_cashlist'),	'menu_url'=>'index.php?act=predeposit&op=pd_cash_list'),
					array('menu_key'=>'cashinfo','menu_name'=>Language::get('nc_member_path_predeposit_cashinfo'),	'menu_url'=>'')
				);
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
}
