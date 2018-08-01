<?php
/**
 * 代金券
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
class member_voucherControl extends BaseMemberControl{
	private $voucherstate_arr;
	
	public function __construct() {
		parent::__construct();
		Language::read('member_layout,member_voucher');
		//判断系统是否开启代金券功能
		if (intval(C('voucher_allow')) !== 1){
			showMessage(Language::get('member_voucher_unavailable'),'index.php?act=member_snsindex','html','error');
		}
		//领取的代金券状态
		$this->voucherstate_arr = array('unused'=>array(1,Language::get('voucher_voucher_state_unused')),'used'=>array(2,Language::get('voucher_voucher_state_used')),'expire'=>array(3,Language::get('voucher_voucher_state_expire')));
		Tpl::output('voucherstate_arr',$this->voucherstate_arr);
	}
	/*
	 * 默认显示代金券模版列表
	 */
	public function indexOp() {
        $this->voucher_listOp() ;
    }
	/*
	 * 获取代金券模版详细信息 
	 */
    public function voucher_listOp(){
        //检查过期的代金券，状态设置为过期(vouchet_state=3)
        $this->check_voucher_expire();
		$model = Model();
		$where = array('voucher_owner_id'=>$_SESSION['member_id']);
		if (intval($_GET['select_detail_state'])>0){
			$where['voucher_state'] = intval($_GET['select_detail_state']);
		}
		$field = "voucher_id,voucher_code,voucher_title,voucher_desc,voucher_start_date,voucher_end_date,voucher_price,voucher_limit,voucher_state,voucher_order_id,voucher_store_id,store_name,store_id,store_domain,voucher_t_customimg";
		$list = $model->table('voucher,store,voucher_template')->field($field)->join('inner,inner')->on('voucher.voucher_store_id = store.store_id,voucher.voucher_t_id=voucher_template.voucher_t_id')->where($where)->order('voucher_id desc')->page(10)->select();
		if(is_array($list)){
			foreach ($list as $key=>$val){
				if (empty($val['voucher_t_customimg']) || !file_exists(BASE_UPLOAD_PATH.DS.ATTACH_VOUCHER.DS.$val['store_id'].DS.$val['voucher_t_customimg'])){
					$list[$key]['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(60);
				}else{
					$list[$key]['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.ATTACH_VOUCHER.DS.$val['store_id'].DS.str_ireplace('.', '_small.', $val['voucher_t_customimg']);
				}
			}
		}
		
		Tpl::output('list', $list);
        Tpl::output('show_page',$model->showpage(2)) ;
		//查询会员信息
		$this->get_member_info();
        $this->profile_menu('voucher_list');
		Tpl::output('menu_sign','myvoucher');
		Tpl::output('menu_sign_url','index.php?act=member_voucher');
		Tpl::output('menu_sign1','member_voucher');
        Tpl::showpage('member_voucher.list');
    }
    //检查过期的代金券，状态设置为过期(vouchet_state=3)
    private function check_voucher_expire() {
        $model = Model();
        $model->table('voucher')->where(array('voucher_owner_id'=>$_SESSION['member_id'],'voucher_state'=>$this->voucherstate_arr['unused'][0],'voucher_end_date'=>array('lt',time())))->update(array('voucher_state'=>$this->voucherstate_arr['expire'][0]));
    }
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @param array 	$array		附加菜单
	 * @return 
	 */
	private function profile_menu($menu_key='') {
		$menu_array = array(
			1=>array('menu_key'=>'voucher_list','menu_name'=>Language::get('nc_myvoucher'),'menu_url'=>'index.php?act=member_voucher&op=voucher_list'),
		);
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
    }	

}
