<?php
/**
 * 积分礼品
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class pointprodControl extends BaseHomeControl {
	private $templatestate_arr;
	public function __construct() {
		parent::__construct();
		//读取语言包
		Language::read('home_pointprod,home_voucher');
		//判断系统是否开启积分和积分中心功能
		if (C('points_isuse') != 1 || C('pointshop_isuse') != 1){
			showMessage(Language::get('pointshop_unavailable'),'index.php','html','error');
		}
		//根据op判断积分兑换功能是否开启
		if (in_array($_GET['op'],array('plist','pinfo')) && C('pointprod_isuse') != 1){
			showMessage(Language::get('pointprod_unavailable'),'index.php','html','error');
		}
		Tpl::output('index_sign','pointprod');
		//代金券模板状态
		$this->templatestate_arr = array('usable'=>array(1,Language::get('voucher_templatestate_usable')),'disabled'=>array(2,Language::get('voucher_templatestate_disabled')));
		//领取的代金券状态
		$this->voucherstate_arr = array('unused'=>array(1,Language::get('voucher_voucher_state_unused')),'used'=>array(2,Language::get('voucher_voucher_state_used')),'expire'=>array(3,Language::get('voucher_voucher_state_expire')));
		if($_SESSION['is_login'] == '1'){
			$model = Model();
			if (C('pointprod_isuse') == 1){
				//已选择兑换商品数
				$pcartnum = $model->table('points_cart')->where(array('pmember_id'=>$_SESSION['member_id']))->count();
				Tpl::output('pcartnum',$pcartnum);
			}
			//查询会员信息
			$member_info = $model->table('member')->field('member_points,member_avatar')->where(array('member_id'=>$_SESSION['member_id']))->find();
			Tpl::output('member_info',$member_info);
		}
	}
	public function indexOp(){
		$model = Model();
		//开启代金券功能后查询代金券相应信息
		if (C('voucher_allow') == 1){
			//查询已兑换代金券券数量
			$vouchercount = 0;
			if($_SESSION['is_login'] == '1'){
				$vouchercount = $model->table('voucher')->where(array('voucher_owner_id'=>$_SESSION['member_id'],'voucher_state'=>$this->voucherstate_arr['unused'][0]))->count();
			}
			Tpl::output('vouchercount',$vouchercount);
			//查询代金券面额
			$pricelist =  $model->table('voucher_price')->order('voucher_price asc')->select();
			Tpl::output('pricelist',$pricelist);
			//查询代金券列表
			$field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
			$on = 'voucher_template.voucher_t_store_id=store.store_id';
			$new_voucher = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_state'=>$this->templatestate_arr['usable'][0],'voucher_t_end_date'=>array('gt',time())))->limit(16)->select();
			if (!empty($new_voucher)){
				foreach ($new_voucher as $k=>$v){
					if (!empty($v['voucher_t_customimg'])){
						$v['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.ATTACH_VOUCHER.DS.$v['voucher_t_store_id'].DS.$v['voucher_t_customimg'];
					}else{
						$v['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(240);
					}
					$v['voucher_t_limit'] = intval($v['voucher_t_limit']);
					$new_voucher[$k] = $v;
				}
			}
			Tpl::output('new_voucher',$new_voucher);
		}
		//开启积分兑换功能后查询代金券相应信息
		if (C('pointprod_isuse') == 1){
			//最新积分兑换商品
			$model_pointsprod = Model('pointprod');
			$new_pointsprod = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_sort asc,pgoods_id desc',16);
			Tpl::output('new_pointsprod',$new_pointsprod);
			//兑换排行
			$converlist = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_salenum desc,pgoods_id desc',3);
			Tpl::output('converlist',$converlist);
		}
		//SEO
		Model('seo')->type('point')->show();
		Tpl::showpage('pointprod');
	}
	/**
	 * 积分商品列表
	 */
	public function plistOp(){
		$model_pointsprod = Model('pointprod');
		$pointprod_list = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_sort asc,pgoods_id desc','',16);
		Tpl::output('pointprod_list',$pointprod_list);
		//兑换排行
		$converlist = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_salenum desc,pgoods_id desc',3);
		Tpl::output('converlist',$converlist);
		Tpl::showpage('pointprod_list');
	}
	/**
	 * 积分礼品详细
	 */
	public function pinfoOp() {
		$pid = intval($_GET['id']);
		if (!$pid){
			showMessage(Language::get('pointprod_parameter_error'),'index.php?act=pointprod','html','error');
		}
		$model = Model('pointprod');
		$prodinfo = $model->getPointProdInfoNew(array('pgoods_id'=>$pid));
		if (!is_array($prodinfo) || count($prodinfo)<=0){
			showMessage(Language::get('pointprod_record_error'),'index.php?act=pointprod','html','error');
		}
		//兑换按钮的可用状态
		if ($prodinfo['pgoods_islimittime'] == 1 && $prodinfo['ex_state'] == 'going'){
			$timediff = intval($prodinfo['pgoods_endtime'])-time();
			$prodinfo['timediff']['diff_day']  = intval($timediff/86400);
			$prodinfo['timediff']['diff_hour'] = intval($timediff%86400/3600);
			$prodinfo['timediff']['diff_mins'] = intval($timediff%86400%3600/60);
			$prodinfo['timediff']['diff_secs'] = intval($timediff%86400%3600%60);
		}
		//更新礼品浏览次数
		$model->table('points_goods')->where(array('pgoods_id'=>$pid))->update(array('pgoods_view'=>array('exp','pgoods_view+1')));

		//查询兑换信息
		$orderprod_list = $model->table('points_ordergoods,points_order')->join('left')->on('points_ordergoods.point_orderid = points_order.point_orderid')->where(array('point_orderstate'=>array('gt',10)))->order('points_ordergoods.point_recid desc')->limit(5)->select();
		Tpl::output('orderprod_list',$orderprod_list);
		Tpl::output('prodinfo',$prodinfo);

		$seo_param = array();
		$seo_param['name'] = $prodinfo['pgoods_name'];
		$seo_param['key'] = $prodinfo['pgoods_keywords'];
		$seo_param['description'] = $prodinfo['pgoods_description'];
		Model('seo')->type('point_content')->param($seo_param)->show();
		Tpl::showpage('pointprod_info');
	}
	/**
	 * 推荐礼品
	 */
	private function getCommendPointProd(){
		$condition_arr = array();
		$condition_arr['pgoods_show'] = '1';
		$condition_arr['pgoods_state'] = '0';
		$condition_arr['pgoods_commend'] = '1';
		$condition_arr['order'] = ' pgoods_sort asc,pgoods_id desc ';
		$condition_arr['limit'] = 4;
		$pointprod_model = Model('pointprod');
		$list = $pointprod_model->getPointProdList($condition_arr,$page);
		return $list;
	}
}
