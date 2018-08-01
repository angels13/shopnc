<?php
/**
 * 商家中心团购管理
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

class store_groupbuyControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();

        //读取语言包
        Language::read('member_groupbuy');
        //检查团购功能是否开启
        if (intval($GLOBALS['setting_config']['groupbuy_allow']) !== 1){
            showMessage(Language::get('groupbuy_unavailable'),'index.php?act=seller_center','','error');
        }
    }
    /**
     * 默认显示团购列表
     **/
    public function indexOp() {
        $this->groupbuy_listOp();
    }

    /**
     * 团购套餐购买
     **/
    public function groupbuy_quota_addOp() {
        //输出导航
        self::profile_menu('groupbuy_quota_add');
        Tpl::showpage('store_groupbuy_quota.add');
    }

    /**
     * 团购套餐购买保存
     **/
    public function groupbuy_quota_add_saveOp() {
        $groupbuy_quota_quantity = intval($_POST['groupbuy_quota_quantity']);
        if($groupbuy_quota_quantity <= 0) {
            showDialog('购买数量不能为空');
        }

        $model_groupbuy_quota = Model('groupbuy_quota');

        //获取当前价格
        $current_price = intval($GLOBALS['setting_config']['groupbuy_price']);

        //获取该用户已有套餐
        $current_groupbuy_quota= $model_groupbuy_quota->getGroupbuyQuotaCurrent($_SESSION['store_id']);
        $add_time = 86400 * 30 * $groupbuy_quota_quantity;
        if(empty($current_groupbuy_quota)) {
            //生成套餐
            $param = array();
            $param['member_id'] = $_SESSION['member_id'];
            $param['member_name'] = $_SESSION['member_name'];
            $param['store_id'] = $_SESSION['store_id'];
            $param['store_name'] = $_SESSION['store_name'];
            $param['start_time'] = TIMESTAMP;
            $param['end_time'] = TIMESTAMP + $add_time;
            $model_groupbuy_quota->addGroupbuyQuota($param);
        } else {
            $param = array();
            $param['end_time'] = array('exp', 'end_time + ' . $add_time);
            $model_groupbuy_quota->editGroupbuyQuota($param, array('quota_id' => $current_groupbuy_quota['quota_id']));
        }

        //记录店铺费用
        $this->recordStoreCost($current_price * $groupbuy_quota_quantity, '购买团购');

        $this->recordSellerLog('购买'.$groupbuy_quota_quantity.'份团购套餐，单价'.$current_price.$lang['nc_yuan']);

        showDialog(Language::get('groupbuy_quota_add_success'), urlShop('store_groupbuy', 'groupbuy_list'), 'succ');
    }

    /**
     * 团购列表
     **/
    public function groupbuy_listOp() {
        $model_groupbuy = Model('groupbuy');
        $model_groupbuy_quota = Model('groupbuy_quota');

        $current_groupbuy_quota = $model_groupbuy_quota->getGroupbuyQuotaCurrent($_SESSION['store_id']);
        Tpl::output('current_groupbuy_quota', $current_groupbuy_quota);

        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if(!empty($_GET['groupbuy_state'])) {
            $condition['state'] = $_GET['groupbuy_state'];
        }
        $condition['groupbuy_name'] = array('like', '%'.$_GET['groupbuy_name'].'%');
        $groupbuy_list = $model_groupbuy->getGroupbuyList($condition, $page);
        Tpl::output('group',$groupbuy_list);
        Tpl::output('show_page',$model_groupbuy->showpage());
        Tpl::output('groupbuy_state_array', $model_groupbuy->getGroupbuyStateArray());

        self::profile_menu('groupbuy_list');
        Tpl::showpage('store_groupbuy.list');
    }

    /**
     * 添加团购页面
     **/
    public function groupbuy_addOp() {
        $model_groupbuy_quota = Model('groupbuy_quota');

        $current_groupbuy_quota = $model_groupbuy_quota->getGroupbuyQuotaCurrent($_SESSION['store_id']);
        if(empty($current_groupbuy_quota)) {
            showMessage('当前没有可用套餐，请先购买套餐',urlShop('store_groupbuy', 'groupbuy_quota_add'),'','error');
        }
        Tpl::output('current_groupbuy_quota', $current_groupbuy_quota);

        // 根据后台设置的审核期重新设置团购开始时间
        Tpl::output('groupbuy_start_time', TIMESTAMP + intval(C('groupbuy_review_day')) * 86400);

        $_cache = ($h = H('groupbuy')) ? $h : H('groupbuy',true);
        Tpl::output('class_list',$_cache['category']);
        Tpl::output('area_list',$_cache['area']);
        self::profile_menu('groupbuy_add');
        Tpl::showpage('store_groupbuy.add');

    }

    /**
     * 团购保存
     **/
    public function groupbuy_saveOp() {
        //获取提交的数据
        $goods_id = intval($_POST['groupbuy_goods_id']);
        if(empty($goods_id)) {
            showDialog(Language::get('param_error'));
        }

        $model_groupbuy = Model('groupbuy');
        $model_goods = Model('goods');
        $model_groupbuy_quota = Model('groupbuy_quota');

        // 检查套餐
        $current_groupbuy_quota = $model_groupbuy_quota->getGroupbuyQuotaCurrent($_SESSION['store_id']);
        if(empty($current_groupbuy_quota)) {
            showDialog('当前没有可用套餐，请先购买套餐',urlShop('store_groupbuy', 'groupbuy_quota_add'),'error');
        }

        $goods_info = $model_goods->getGoodsInfo(array('goods_id' => $goods_id));
        if(empty($goods_info) || $goods_info['store_id'] != $_SESSION['store_id']) {
            showDialog(Language::get('param_error'));
        }

        $param = array();
        $param['groupbuy_name'] = $_POST['groupbuy_name'];
        $param['remark'] = $_POST['remark'];
        $param['start_time'] = strtotime($_POST['start_time']);
        $param['end_time'] = strtotime($_POST['end_time']);
        $param['groupbuy_price'] = floatval($_POST['groupbuy_price']); 
        $param['groupbuy_rebate'] = ncPriceFormat(floatval($_POST['groupbuy_price'])/floatval($goods_info['goods_price'])*10); 
        $param['groupbuy_image'] = $_POST['groupbuy_image']; 
        $param['groupbuy_image1'] = $_POST['groupbuy_image1']; 
        $param['virtual_quantity'] = intval($_POST['virtual_quantity']);
        $param['upper_limit'] = intval($_POST['upper_limit']);
        $param['groupbuy_intro'] = $_POST['groupbuy_intro'];
        $param['class_id'] = intval($_POST['class_id']);
        $param['area_id'] = intval($_POST['area_id']);
        $param['goods_id'] = $goods_info['goods_id'];
        $param['goods_commonid'] = $goods_info['goods_commonid'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['store_id'] = $_SESSION['store_id'];
        $param['store_name'] = $_SESSION['store_name'];

        //保存
        $result = $model_groupbuy->addGroupbuy($param);
        if($result) {
            $this->recordSellerLog('发布团购活动，团购名称：'.$param['groupbuy_name'].'，商品名称：'.$param['goods_name']);
            showDialog(Language::get('groupbuy_add_success'),'index.php?act=store_groupbuy','succ');
        }else {
            showDialog(Language::get('groupbuy_add_fail'),'index.php?act=store_groupbuy');
        }
    }

    public function groupbuy_goods_infoOp() {
        $goods_commonid = intval($_GET['goods_commonid']);

        $data = array();
        $data['result'] = true;

        $model_goods = Model('goods');

        $condition = array();
        $condition['goods_commonid'] = $goods_commonid;
        $goods_list = $model_goods->getGoodsOnlineList($condition);

        if(empty($goods_list)) {
            $data['result'] = false;
            $data['message'] = L('param_error');
            echo json_encode($data);die;
        }
        
        $goods_info = $goods_list[0];
        $data['goods_id'] = $goods_info['goods_id'];
        $data['goods_name'] = $goods_info['goods_name'];
        $data['goods_price'] = $goods_info['goods_price'];
        $data['goods_image'] = thumb($goods_info, 240);
        $data['goods_href'] = urlShop('goods', 'index', array('goods_id' => $goods_info['goods_id']));
        echo json_encode($data);die;
    }

    public function check_groupbuy_goodsOp() {
        $start_time = strtotime($_GET['start_time']);
        $goods_id = $_GET['goods_id'];

        $model_groupbuy = Model('groupbuy');

        $data = array();
        $data['result'] = true;

        //检查商品是否已经参加同时段活动
        $condition = array();
        $condition['end_time'] = array('gt', $start_time);
        $condition['goods_id'] = $goods_id;
        $groupbuy_list = $model_groupbuy->getGroupbuyAvailableList($condition);
        if(!empty($groupbuy_list)) {
            $data['result'] = false;
            echo json_encode($data);die;
        }

        echo json_encode($data);die;
    }

    /**
     * 上传图片
     **/
    public function image_uploadOp() {
        if(!empty($_POST['old_groupbuy_image'])) {
            $this->_image_del($_POST['old_groupbuy_image']);
        }
        $this->_image_upload('groupbuy_image');
    }

    private function _image_upload($file) {
        $data = array();
        $data['result'] = true;
        if(!empty($_FILES[$file]['name'])) {
            $upload	= new UploadFile();
            $uploaddir = ATTACH_PATH.DS.'groupbuy'.DS.$_SESSION['store_id'].DS;
            $upload->set('default_dir', $uploaddir);
            $upload->set('thumb_width',	'480,296,168');
            $upload->set('thumb_height', '480,296,168');
            $upload->set('thumb_ext', '_max,_mid,_small');
            $upload->set('fprefix', $_SESSION['store_id']);
            $result = $upload->upfile($file);
            if($result) {
                $data['file_name'] = $upload->file_name;
                $data['origin_file_name'] = $_FILES[$file]['name'];
                $data['file_url'] = gthumb($upload->file_name, 'mid');
            } else {
                $data['result'] = false;
                $data['message'] = $upload->error;
            }
        } else {
            $data['result'] = false;
        }
        echo json_encode($data);die;
    }

    /**
     * 图片删除
     */
    private function _image_del($image_name) {
        list($base_name, $ext) = explode(".", $image_name);
        $base_name = str_replace('/', '', $base_name);
        $base_name = str_replace('.', '', $base_name);
        list($store_id) = explode('_', $base_name);
        $image_path = BASE_UPLOAD_PATH.DS.ATTACH_GROUPBUY.DS.$store_id.DS;
        $image = $image_path.$base_name.'.'.$ext;
        $image_small = $image_path.$base_name.'_small.'.$ext;
        $image_mid = $image_path.$base_name.'_mid.'.$ext;
        $image_max = $image_path.$base_name.'_max.'.$ext;
        @unlink($image);
        @unlink($image_small);
        @unlink($image_mid);
        @unlink($image_max);
    }

    /**
     * 选择活动商品
     **/
    public function search_goodsOp() {
        $model_goods = Model('goods');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['goods_name'] = array('like', '%'.$_GET['goods_name'].'%');
        $goods_list = $model_goods->getGoodsCommonOnlineList($condition, '*', 8);

        Tpl::output('goods_list', $goods_list);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::showpage('store_groupbuy.goods', 'null_layout');
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string 	$menu_key	当前导航的menu_key
     * @param array 	$array		附加菜单
     * @return 
     */
    private function profile_menu($menu_key='') {
        $menu_array	= array(
            1=>array('menu_key'=>'groupbuy_list','menu_name'=>L('nc_member_path_group_list'),'menu_url'=>urlShop('store_groupbuy', 'groupbuy_list'))
        );
        switch ($menu_key){
        case 'groupbuy_add':
            $menu_array[] = array('menu_key'=>'groupbuy_add','menu_name'=>L('nc_member_path_new_group'),'menu_url'=>'index.php?act=store_groupbuy&groupbuy_add');
            break;
        case 'groupbuy_quota_add':
            $menu_array[] = array('menu_key'=>'groupbuy_quota_add','menu_name'=>'购买套餐','menu_url'=>urlShop('store_groupbuy', 'groupbuy_quota_add'));
            break;
        case 'groupbuy_edit':
            $menu_array[] = array('menu_key'=>'groupbuy_edit','menu_name'=>L('nc_member_path_edit_group'),'menu_url'=>'index.php?act=store_groupbuy');
            break;
        case 'cancel':
            $menu_array[] = array('menu_key'=>'groupbuy_cancel','menu_name'=>L('nc_member_path_cancel_group'));
            break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
