<?php
/**
 * 商户中心
 *
 * @copyright  Copyright (c) 2007-2014 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class seller_centerControl extends BaseSellerControl {

    /**
     * 构造方法
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 商户中心首页
     *
     */
    public function indexOp() {
		Language::read('member_home_index');
        // 店铺信息
        $store_info = $this->store_info;
        if(intval($store_info['store_end_time']) > 0) {
            $store_info['store_end_time_text']	= date('Y-m-d', $store_info['store_end_time']);
        } else {
            $store_info['store_end_time_text'] = L('store_no_limit');
        }

        // 店铺等级信息
        $store_info['grade_name'] = $this->store_grade['sg_name'];
        $store_info['grade_goodslimit'] = $this->store_grade['sg_goods_limit'];
        $store_info['grade_albumlimit'] = $this->store_grade['sg_album_limit'];

        Tpl::output('store_info',$store_info);
        // 文章分类
        $article_class_info = Model('article_class')->getOneClass(3);
        Tpl::output('article_class_info', $article_class_info);
        // 文章列表
        $model_article	= Model('article');
        $condition	= array();
        $condition['article_show'] = '1';
        $condition['ac_id'] = '3';
        $condition['order'] = 'article_sort asc,article_time desc';
        $condition['limit'] = '5';
        $show_article	= $model_article->getArticleList($condition);
        Tpl::output('show_article',$show_article);

        // 销售情况统计
        $model_order = Model('order');
        $field = 'count(order_id) as count, sum(order_amount) sum';
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        // 日销量
        $condition['add_time'] = array('gt', strtotime(date('Y-m-d')));
        $daily_sales = $model_order->getOrderList($condition, 0, $field);

        Tpl::output('daily_sales', $daily_sales[0]);
        // 月销量
        $condition['add_time'] = array('gt', strtotime(date('Y-m')));
        $monthly_sales = $model_order->getOrderList($condition, 0, $field);
        Tpl::output('monthly_sales', $monthly_sales[0]);

        // 单品销售排行
        $goods_list = Model('goods')->getGoodsList(array('store_id' => $_SESSION['store_id']), 'goods_id,goods_name,goods_image,store_id,goods_salenum', '', 'goods_salenum desc', 8);
        Tpl::output('goods_list', $goods_list);
        if (C('groupbuy_allow') == 1){
            // 团购套餐
            $groupquota_info = Model('groupbuy_quota')->getGroupbuyQuotaCurrent($_SESSION['store_id']);
            Tpl::output('groupquota_info', $groupquota_info);
        }
        if (intval(C('promotion_allow')) == 1){
            // 限时折扣套餐
            $xianshiquota_info = Model('p_xianshi_quota')->getXianshiQuotaCurrent($_SESSION['store_id']);
            Tpl::output('xianshiquota_info', $xianshiquota_info);
            // 满即送套餐
            $mansongquota_info = Model('p_mansong_quota')->getMansongQuotaCurrent($_SESSION['store_id']);
            Tpl::output('mansongquota_info', $mansongquota_info);
            // 优惠套装套餐
            $binglingquota_info = Model('p_bundling')->getBundlingQuotaInfoCurrent(array('store_id' => $_SESSION['store_id']));
            Tpl::output('binglingquota_info', $binglingquota_info);
            // 推荐展位套餐
            $boothquota_info = Model('p_booth')->getBoothQuotaInfoCurrent(array('store_id' => $_SESSION['store_id']));
            Tpl::output('boothquota_info', $boothquota_info);
        }
        if (C('voucher_allow') == 1){
            $voucherquota_info = Model('voucher')->getCurrentQuota($_SESSION['store_id']);
            Tpl::output('voucherquota_info', $voucherquota_info);
        }
        $phone_array = explode(',',C('site_phone'));
        Tpl::output('phone_array',$phone_array);

        Tpl::output('menu_sign','index');
        Tpl::showpage('index');
    }
    /**
     * 异步取得卖家统计类信息
     *
     */
    public function statisticsOp() {
        $add_time_to = strtotime(date("Y-m-d")+60*60*24);   //当前日期 ,从零点来时
        $add_time_from = strtotime(date("Y-m-d",(strtotime(date("Y-m-d"))-60*60*24*30)));   //30天前
        $goods_online = 0;      // 出售中商品
        $goods_waitverify = 0;  // 等待审核
        $goods_verifyfail = 0;  // 审核失败
        $goods_offline = 0;     // 仓库待上架商品
        $goods_lockup = 0;      // 违规下架商品
        $consult = 0;           // 待回复商品咨询
        $no_payment = 0;        // 待付款
        $no_delivery = 0;       // 待发货
        $no_receipt = 0;        // 待收货
        $refund_lock  = 0;      // 售前退款
        $refund = 0;            // 售后退款
        $return_lock  = 0;      // 售前退货
        $return = 0;            // 售后退货
        $complain = 0;          //进行中投诉

        $model_goods = Model('goods');
        // 全部商品数
        $goodscount = $model_goods->getGoodsCommonCount(array('store_id' => $_SESSION['store_id']));
        // 出售中的商品
        $goods_online = $model_goods->getGoodsCommonOnlineCount(array('store_id' => $_SESSION['store_id']));
        if (C('goods_verify')) {
            // 等待审核的商品
            $goods_waitverify = $model_goods->getGoodsCommonWaitVerifyCount(array('store_id' => $_SESSION['store_id']));
            // 审核失败的商品
            $goods_verifyfail = $model_goods->getGoodsCommonVerifyFailCount(array('store_id' => $_SESSION['store_id']));
        }
        // 仓库待上架的商品
        $goods_offline = $model_goods->getGoodsCommonOfflineCount(array('store_id' => $_SESSION['store_id']));
        // 违规下架的商品
        $goods_lockup = $model_goods->getGoodsCommonLockUpCount(array('store_id' => $_SESSION['store_id']));
        // 等待回复商品咨询
        $consult = Model('consult')->getConsultCount('store_id='.$_SESSION['store_id'].' and consult_reply is null');

        // 商品图片数量
        $imagecount = Model('album')->getAlbumPicCount(array('store_id' => $_SESSION['store_id']));

        $model_order = Model('order');
        // 交易中的订单
        $progressing = $model_order->getOrderCount(array('store_id'=>$_SESSION['store_id'],'order_state'=>array(array('neq',0),array('neq',40),'and')));
        // 待付款
        $no_payment = $model_order->getOrderStateNewCount(array('store_id'=>$_SESSION['store_id']));
        // 待发货
        $no_delivery = $model_order->getOrderStatePayCount(array('store_id'=>$_SESSION['store_id']));

        $model_refund_return = Model('refund_return');
        // 售前退款
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_type'] = 1;
        $condition['order_lock'] = 2;
        $condition['refund_state'] = array('lt', 3);
        $refund_lock = $model_refund_return->getRefundReturnCount($condition);
        // 售后退款
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_type'] = 1;
        $condition['order_lock'] = 1;
        $condition['refund_state'] = array('lt', 3);
        $refund = $model_refund_return->getRefundReturnCount($condition);
        // 售前退货
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_type'] = 2;
        $condition['order_lock'] = 2;
        $condition['refund_state'] = array('lt', 3);
        $return_lock = $model_refund_return->getRefundReturnCount($condition);
        // 售后退货
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_type'] = 2;
        $condition['order_lock'] = 1;
        $condition['refund_state'] = array('lt', 3);
        $return = $model_refund_return->getRefundReturnCount($condition);

		$condition = array();
		$condition['accused_id'] = $_SESSION['store_id'];
		$condition['complain_state'] = array(array('gt',10),array('lt',90),'and');
		$complain = Model()->table('complain')->where($condition)->count();

		//待确认的结算账单
		$model_bill = Model('bill');
		$condition = array();
		$condition['ob_store_id'] = $_SESSION['store_id'];
		$condition['ob_state'] = BILL_STATE_CREATE;
		$bill_confirm_count = $model_bill->getOrderBillCount($condition);

        //统计数组
        $statistics = array(
            'goodscount' => $goodscount,
            'online' => $goods_online,
            'waitverify' => $goods_waitverify,
            'verifyfail' => $goods_verifyfail,
            'offline' => $goods_offline,
            'lockup' => $goods_lockup,
            'imagecount' => $imagecount,
            'consult' => $consult,
            'progressing' => $progressing,
            'payment' => $no_payment,
            'delivery' => $no_delivery,
            'refund_lock' => $refund_lock,
            'refund' => $refund,
            'return_lock' => $return_lock,
            'return' => $return,
            'complain' => $complain,
            'bill_confirm' => $bill_confirm_count
        );
        exit(json_encode($statistics));
    }
    /**
     * 添加快捷操作
     */
    function quicklink_addOp() {
        if(!empty($_POST['item'])) {
            $_SESSION['seller_quicklink'][$_POST['item']] = $_POST['item'];
        }
        $this->_update_quicklink();
        echo 'true';
    }

    /**
     * 删除快捷操作
     */
    function quicklink_delOp() {
        if(!empty($_POST['item'])) {
            unset($_SESSION['seller_quicklink'][$_POST['item']]);
        }
        $this->_update_quicklink();
        echo 'true';
    }

    private function _update_quicklink() {
        $quicklink = implode(',', $_SESSION['seller_quicklink']);
        $update_array = array('seller_quicklink' => $quicklink);
        $condition = array('seller_id' => $_SESSION['seller_id']);
        $model_seller = Model('seller');
        $model_seller->editSeller($update_array, $condition);
    }

}
