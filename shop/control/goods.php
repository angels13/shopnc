<?php
/**
 * 前台商品
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

class goodsControl extends BaseGoodsControl {
    public function __construct() {
        parent::__construct ();
        Language::read ( 'store_goods_index' );
    }

    /**
     * 单个商品信息页
     */
    public function indexOp() {
        $goods_id = intval($_GET ['goods_id']);
        
        // 商品详细信息
        $model_goods = Model('goods');
        $goods_detail = $model_goods->getGoodsDetail($goods_id, '*');
        $goods_info = $goods_detail['goods_info'];
        if (empty($goods_info)) {
            showMessage(L('goods_index_no_goods'), '', 'html', 'error');
        }
        
        $this->getStoreInfo($goods_info['store_id']);

        Tpl::output('spec_list', $goods_detail['spec_list']);
        Tpl::output('spec_image', $goods_detail['spec_image']);
        Tpl::output('goods_image', $goods_detail['goods_image']);
        Tpl::output('groupbuy_info', $goods_detail['groupbuy_info']);
        Tpl::output('xianshi_info', $goods_detail['xianshi_info']);
        Tpl::output('mansong_info', $goods_detail['mansong_info']);

        // 生成缓存的键值
        $hash_key = $goods_info['goods_id'];
        // 先查找$hash_key缓存
        $cachekey_arr = array (
                'likenum',
                'sharenum'
        );
        if ($_cache = rcache($hash_key, 'product')) {
            foreach ($_cache as $k => $v) {
                $goods_info[$k] = $v;
            }
        } else {
            // 查询SNS中该商品的信息
            $snsgoodsinfo = Model('sns_goods')->getSNSGoodsInfo(array('snsgoods_goodsid' => $goods_info['goods_id']), 'snsgoods_likenum,snsgoods_sharenum');
            $goods_info['likenum'] = $snsgoodsinfo['snsgoods_likenum'];
            $goods_info['sharenum'] = $snsgoodsinfo['snsgoods_sharenum'];
            
            $data = array();
            if (! empty ( $goods_info )) {
                foreach ( $goods_info as $k => $v ) {
                    if (in_array ( $k, $cachekey_arr )) {
                        $data [$k] = $v;
                    }
                }
            }
            // 缓存商品信息
            wcache ( $hash_key, $data, 'product' );
        }
        
        // 检查是否为店主本人
        $store_self = false;
        if (!empty($_SESSION['store_id'])) {
            if ($goods_info['store_id'] == $_SESSION['store_id']) {
                $store_self = true;
            }
        }
        Tpl::output('store_self',$store_self );
        
        // 如果使用运费模板
        if ($goods_info['transport_id'] > 0) {
            // 取得三种运送方式默认运费
            $model_transport = Model('transport');
            $transport = $model_transport->getExtendList(array('transport_id' => $goods_info['transport_id'], 'is_default' => 1));
            if (!empty($transport) && is_array($transport)) {
                foreach ($transport as $v) {
                    $goods_info[$v['type'] . "_price"] = $v['sprice'];
                }
            }
        }
        Tpl::output('goods', $goods_info);

        // 关联版式
        $plateid_array = array();
        if (!empty($goods_info['plateid_top'])) {
            $plateid_array[] = $goods_info['plateid_top'];
        }
        if (!empty($goods_info['plateid_bottom'])) {
            $plateid_array[] = $goods_info['plateid_bottom'];
        }
        if (!empty($plateid_array)) {
            $plate_array = Model('store_plate')->getPlateList(array('plate_id' => array('in', $plateid_array), 'store_id' => $goods_info['store_id']));
            $plate_array = array_under_reset($plate_array, 'plate_position', 2);
            Tpl::output('plate_array', $plate_array);
        }
        
        Tpl::output('store_id', $goods_info ['store_id']);
        
        // 输出一级地区
        $area_list = array(1 => '北京', 2 => '天津', 3 => '河北', 4 => '山西', 5 => '内蒙古', 6 => '辽宁', 7 => '吉林', 8 => '黑龙江', 9 => '上海',
                            10 => '江苏', 11 => '浙江', 12 => '安徽', 13 => '福建', 14 => '江西', 15 => '山东', 16 => '河南', 17 => '湖北', 18 => '湖南',
                            19 => '广东', 20 => '广西', 21 => '海南', 22 => '重庆', 23 => '四川', 24 => '贵州', 25 => '云南', 26 => '西藏', 27 => '陕西',
                            28 => '甘肃', 29 => '青海', 30 => '宁夏', 31 => '新疆', 32 => '台湾', 33 => '香港', 34 => '澳门', 35 => '海外' 
                        );
        if (strtoupper(CHARSET) == 'GBK') {
            $area_list = Language::getGBK($area_list);
        }
        Tpl::output('area_list', $area_list);
        
        // 生成浏览过产品
        $cookievalue = $goods_id . '-' . $goods_info ['store_id'];
        if (cookie('viewed_goods')) {
            $string_viewed_goods = decrypt(cookie('viewed_goods'), MD5_KEY);
            if (get_magic_quotes_gpc()) {
                $string_viewed_goods = stripslashes($string_viewed_goods); // 去除斜杠
            }
            $vg_ca = @unserialize($string_viewed_goods);
            $sign = true;
            if ( !empty($vg_ca) && is_array($vg_ca)) {
                foreach ($vg_ca as $vk => $vv) {
                    if ($vv == $cookievalue) {
                        $sign = false;
                    }
                }
            } else {
                $vg_ca = array();
            }
            
            if ($sign) {
                if (count($vg_ca) >= 6) {
                    $vg_ca[] = $cookievalue;
                    array_shift($vg_ca);
                } else {
                    $vg_ca[] = $cookievalue;
                }
            }
        } else {
            $vg_ca[] = $cookievalue;
        }
        $vg_ca = encrypt(serialize($vg_ca), MD5_KEY);
        setNcCookie('viewed_goods', $vg_ca);
        
        //优先得到推荐商品
        $goods_commend_list = $model_goods->getGoodsOnlineList(array('store_id' => $goods_info['store_id'], 'goods_commend' => 1), 'goods_id,goods_name,goods_jingle,goods_image,store_id,goods_price', 0, 'rand()', 5, 'goods_commonid');
        Tpl::output('goods_commend',$goods_commend_list);
        
        
        // 当前位置导航
        $nav_link_list = Model('goods_class')->getGoodsClassNav($goods_info['gc_id'], 0);
        $nav_link_list[] = array('title' => $goods_info['goods_name']);
        Tpl::output('nav_link_list', $nav_link_list );

        //评价信息
        $goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsInfoByGoodsID($goods_id);
        Tpl::output('goods_evaluate_info', $goods_evaluate_info);
        
        $seo_param = array ();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['key'] = $goods_info['goods_keywords'];
        $seo_param['description'] = $goods_info['goods_description'];
        Model('seo')->type('product')->param($seo_param)->show();
        Tpl::showpage('goods');
    }

    private function get_btn_state($promotion_info) {
        $btn_state = array();
        $btn_state['btn_buynow'] = TRUE;
        $btn_state['btn_addcart'] = TRUE;

        if($promotion_info['group']) {
            $btn_state['btn_addcart'] = FALSE;
        }

        if($promotion_info['xianshi']) {
            if($promotion_info['xianshi']['start_time'] < TIMESTAMP) {
                $btn_state['btn_addcart'] = FALSE;
            }
        }
        return $btn_state;
    }

    /**
	 * 商品评论
	 */
	public function commentsOp() {
        $goods_id = intval($_GET['goods_id']);
        $this->_get_comments($goods_id, $_GET['type'], 10);
		Tpl::showpage('goods.comments','null_layout');
	}

    /**
     * 商品评价详细页
     */
    public function comments_listOp() {
        $goods_id = intval($_GET ['goods_id']);

        // 商品详细信息
        $model_goods = Model('goods');
        $goods_info = $model_goods->getGoodsInfo(array('goods_id' => intval($_GET['goods_id'])), '*');
        // 验证商品是否存在
        if (empty($goods_info)) {
            showMessage(L('goods_index_no_goods'), '', 'html', 'error');
        }
        Tpl::output('goods', $goods_info);

        $this->getStoreInfo($goods_info['store_id']);

        // 当前位置导航
        $nav_link_list = Model('goods_class')->getGoodsClassNav($goods_info['gc_id'], 0);
        $nav_link_list[] = array('title' => $goods_info['goods_name'], 'link' => urlShop('goods', 'index', array('goods_id' => $goods_id)));
        $nav_link_list[] = array('title' => '商品评价');
        Tpl::output('nav_link_list', $nav_link_list );

        //评价信息
        $goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsInfoByGoodsID($goods_id);
        Tpl::output('goods_evaluate_info', $goods_evaluate_info);
        
        $seo_param = array ();
        $seo_param['name'] = $goods_info['goods_name'];
        $seo_param['key'] = $goods_info['goods_keywords'];
        $seo_param['description'] = $goods_info['goods_description'];
        Model('seo')->type('product')->param($seo_param)->show();

        $this->_get_comments($goods_id, $_GET['type'], 20);

		Tpl::showpage('goods.comments_list');
    }

    private function _get_comments($goods_id, $type, $page) {
        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
        switch ($type) {
            case '1':
                $condition['geval_scores'] = array('in', '5,4');
                Tpl::output('type', '1');
                break;
            case '2':
                $condition['geval_scores'] = array('in', '3,2');
                Tpl::output('type', '2');
                break;
            case '3':
                $condition['geval_scores'] = array('in', '1');
                Tpl::output('type', '3');
                break;
        }

        //查询商品评分信息
        $model_evaluate_goods = Model("evaluate_goods");
        $goodsevallist = $model_evaluate_goods->getEvaluateGoodsList($condition, $page);
        Tpl::output('goodsevallist',$goodsevallist);
        Tpl::output('show_page',$model_evaluate_goods->showpage('5'));
    }
    
    /**
     * 销售记录
     */
    public function salelogOp() {
        $goods_id	 = intval($_GET['goods_id']);
        $order_class = Model('order');
        $sales = $order_class->getOrderAndOrderGoodsSalesRecordList(array('order_goods.goods_id'=>$goods_id), 'order_goods.*, order.buyer_name, order.add_time', 10);
        Tpl::output('show_page',$order_class->showpage());
        Tpl::output('sales',$sales);
        
        Tpl::output('order_type', array(2=>'团', 3=>'折', '4'=>'套装'));
        Tpl::showpage('goods.salelog','null_layout');
    }

	/**
	 * 产品咨询
	 */
	public function cosultingOp() {
		$goods_id	 = intval($_GET['goods_id']);
		if($goods_id <= 0){
			showMessage(Language::get('wrong_argument'),'','html','error');
		}
		// 分页信息
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
				
		//得到商品咨询信息
		$consult		= Model('consult');
		$consult_list	= $consult->getConsultList(array('goods_id'=>$goods_id),$page,'simple');
		Tpl::output('consult_list',$consult_list);
		Tpl::output('show_page', $page->show());		
		
		//检查是否为店主本身
		$store_self = false;
        if(!empty($_SESSION['store_id'])) {
            if (intval($_GET['store_id']) == $_SESSION['store_id']) {
                $store_self = true;
            }
        }
        //查询会员信息
        $member_info	= array();
        $member_model = Model('member');
        if(!empty($_SESSION['member_id'])) $member_info = $member_model->infoMember(array('member_id'=>"{$_SESSION['member_id']}"));
		//检查是否可以评论
        $consult_able = true;
        if((!$GLOBALS['setting_config']['guest_comment'] && !$_SESSION['member_id'] ) || $store_self == true || ($_SESSION['member_id']>0 && $member_info['is_allowtalk'] == 0)){
        	$consult_able = false;
        }
        Tpl::output('nchash',substr(md5(SHOP_SITE_URL.$_GET['act'].$_GET['op']),0,8));
        Tpl::output('consult_able',$consult_able);
		Tpl::showpage('goods.cosulting', 'null_layout');
	}

	/**
	 * 商品咨询添加
	 */
	public function save_consultajaxOp(){
		//检查是否可以评论
        if(!C('guest_comment') && !$_SESSION['member_id']){
        	echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_goods_noallow')));
        	die;
        }
		$goods_id	 = intval($_GET['goods_id']);
		if($goods_id <= 0){
			echo json_encode(array('done'=>'false','msg'=>Language::get('wrong_argument')));
        	die;
		}
		//咨询内容的非空验证
		if(trim($_GET['goods_content'])== ""){
			echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_input_consult')));
        	die;
		}
		$_POST = $_GET;
		//表单验证
		$result = chksubmit(true,C('captcha_status_goodsqa'),'num');
		if (!$result){
		    echo json_encode(array('done'=>'false','msg'=>Language::get('invalid_request')));
		    die;
		}elseif ($result === -11){
	        echo json_encode(array('done'=>'false','msg'=>Language::get('invalid_request')));
	        die;
	    }elseif ($result === -12){
		   echo json_encode(array('done'=>'false','msg'=>Language::get('wrong_checkcode')));
    	   die;
	    }
        if (processClass::islock('commit')){
        	echo json_encode(array('done'=>'false','msg'=>Language::get('nc_common_op_repeat')));
        	die;
        }else{
        	processClass::addprocess('commit');
        }
        if($_SESSION['member_id']){
	        //查询会员信息
	        $member_model = Model('member');
	        $member_info = $member_model->infoMember(array('member_id'=>"{$_SESSION['member_id']}"));
			if(empty($member_info) || $member_info['is_allowtalk'] == 0){
	        	echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_goods_noallow')));
        		die;
	        }
        }
		//判断商品编号的存在性和合法性
		$goods	= Model('goods');
		$goods_info	= array();
		$goods_info	= $goods->getGoodsInfo(array('goods_id'=> $goods_id));
		if(empty($goods_info)){
			echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_goods_not_exists')));
        	die;
		}
        //判断是否是店主本人
        if($_SESSION['store_id'] && $goods_info['store_id'] == $_SESSION['store_id']) {
            echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_consult_store_error')));
        	die;
        }
		//检查店铺状态
		$store_model = Model('store');
		$store_info	= $store_model->getStoreInfoByID($goods_info['store_id']);
		if($store_info['store_state'] == '0' || intval($store_info['store_state']) == '2' || (intval($store_info['store_end_time']) != 0 && $store_info['store_end_time'] <= time())){
			echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_goods_store_closed')));
        	die;
		}
		//接收数据并保存
		$input	= array();
		$input['goods_id']			= $goods_id;
		$input['cgoods_name']		= $goods_info['goods_name'];
		$input['member_id']			= intval($_SESSION['member_id']) > 0?$_SESSION['member_id']:0;
		$input['cmember_name']		= $_SESSION['member_name']?$_SESSION['member_name']:'';
		$input['store_id']			= $store_info['store_id'];
		$input['email']				= $_GET['email'];
		if (strtoupper(CHARSET) == 'GBK') {
			$input['consult_content']	= Language::getGBK($_GET['goods_content']);
		}else{
			$input['consult_content']	= $_GET['goods_content'];
		}
		$input['isanonymous']		= $_GET['hide_name']=='hide'?1:0;
		$consult_model	= Model('consult');
		if($consult_model->addConsult($input)){
			echo json_encode(array('done'=>'true'));
        	die; 
		}else{
			echo json_encode(array('done'=>'false','msg'=>Language::get('goods_index_consult_fail')));
        	die; 
		}
	}
    
    /**
     * 异步显示优惠套装
     */
    public function get_bundlingOp() {
        $goods_id = intval($_GET['goods_id']);
        $store_id = intval($_GET['store_id']);
        if ($goods_id <= 0 || $store_id <= 0) {
            exit();
        }
        $model_bundling = Model('p_bundling');
        
        // 更新优惠套装状态
        $model_bundling->editBundlingTimeout(array('store_id' => $store_id));
        
        // 查询店铺套餐活动是否开启
        $quota_list = $model_bundling->getBundlingQuotaOpenList(array('store_id' => $store_id), 0, 1);
        if (!empty($quota_list)) {
            // 根据商品id查询bl_id
            $b_g_list = $model_bundling->getBundlingGoodsList(array('goods_id' => $goods_id, 'bl_appoint' => 1), 'bl_id');
            if (!empty($b_g_list) && is_array($b_g_list)) {
                $b_id_array = array();
                foreach ($b_g_list as $val) {
                    $b_id_array[] = $val['bl_id'];
                }
                
                // 查询套餐列表
                $bundling_list = $model_bundling->getBundlingOpenList(array('bl_id' => array('in', $b_id_array)));
                // 整理
                if (!empty($bundling_list) && is_array($bundling_list)) {
                    $bundling_array = array();
                    foreach ($bundling_list as $val) {
                        $bundling_array[$val['bl_id']]['id'] = $val['bl_id'];
                        $bundling_array[$val['bl_id']]['name'] = $val['bl_name'];
                        $bundling_array[$val['bl_id']]['cost_price'] = 0;
                        $bundling_array[$val['bl_id']]['price'] = $val['bl_discount_price'];
                        $bundling_array[$val['bl_id']]['freight'] = $val['bl_freight'];
                    }
                    $blid_array = array_keys($bundling_array);
                    
                    $b_goods_list = $model_bundling->getBundlingGoodsList(array('bl_id' => array('in', $blid_array)));
                    if (!empty($b_goods_list)) {
                        $goodsid_array = array();
                        foreach ($b_goods_list as $val) {
                            $goodsid_array[] = $val['goods_id'];
                        }
                        $goods_list = Model('goods')->getGoodsAsGoodsShowList(array('goods_id' => array('in', $goodsid_array)), 'goods_id,goods_name,goods_price,goods_image');
                        $goods_list = array_under_reset($goods_list, 'goods_id');
                    }
                    // 整理
                    if (! empty ( $b_goods_list ) && is_array ( $b_goods_list )) {
                        $b_goods_array = array ();
                        foreach ( $b_goods_list as $val ) {
                            if (isset($goods_list[$val['goods_id']])) {
                                $k = (intval($val['goods_id']) == $goods_id) ? 0 : $val['goods_id'];    // 排序当前商品放到最前面
                                $b_goods_array[$val['bl_id']][$k]['id'] = $val['goods_id'];
                                $b_goods_array[$val['bl_id']][$k]['image'] = thumb($goods_list[$val['goods_id']], 240);
                                $b_goods_array[$val['bl_id']][$k]['name'] = $goods_list[$val['goods_id']]['goods_name'];
                                $b_goods_array[$val['bl_id']][$k]['shop_price'] = ncPriceFormat($goods_list[$val['goods_id']]['goods_price']);
                                $b_goods_array[$val['bl_id']][$k]['price'] = ncPriceFormat($val['bl_goods_price']);
                                $bundling_array[$val['bl_id']]['cost_price'] += ncPriceFormat($goods_list[$val['goods_id']]['goods_price']);
                            }
                        }
                    }
                    Tpl::output('bundling_array', $bundling_array);
                    Tpl::output('b_goods_array', $b_goods_array);
                }
            }
        }
        Tpl::showpage('goods_bundling', 'null_layout');
    }

	/**
	 * 商品详细页运费显示
	 *
	 * @return unknown
	 */
	function calcOp(){
		if (!is_numeric($_GET['id']) || !is_numeric($_GET['tid'])) return false;

		$model_transport = Model('transport');
		$extend = $model_transport->getExtendList(array('transport_id'=>array(intval($_GET['tid']))));
		if (!empty($extend) && is_array($extend)){
			$calc = array();
			$calc_default = array();
			foreach ($extend as $v) {
				if (strpos($v['top_area_id'],",".intval($_GET['id']).",") !== false){
					$calc = $v['sprice'];
				}
				if ($v['is_default']==1){
					$calc_default = $v['sprice'];
				}
			}
			//如果运费模板中没有指定该地区，取默认运费
			if (empty($calc) && !empty($calc_default)){
				$calc = $calc_default;
			}
		}
		echo json_encode($calc);
	}
}
