<?php
/**
 * 商品管理
 *
 * 
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit ('Access Invalid!');
class store_goods_onlineControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct ();
        Language::read ('member_store_goods_index');
    }
    public function indexOp() {
        $this->goods_listOp();
    }
    
    /**
     * 出售中的商品列表
     */
    public function goods_listOp() {
        $model_goods = Model('goods');
        
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        if (intval($_GET['stc_id']) > 0) {
            $where['goods_stcids'] = array('like', '%' . intval($_GET['stc_id']) . '%');
        }
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case 0:
                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 1:
                    $where['goods_serial'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 2:
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }
        $goods_list = $model_goods->getGoodsCommonOnlineList($where);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::output('goods_list', $goods_list);
        
        // 计算库存
        $storage_array = $model_goods->calculateStorage($goods_list, $this->store_info['store_storage_alarm']);
        Tpl::output('storage_array', $storage_array);
        
        // 商品分类
        $store_goods_class = Model('my_goods_class')->getClassTree(array(
                                    'store_id' => $_SESSION['store_id'],
                                    'stc_state' => '1' 
                                ));
        Tpl::output('store_goods_class', $store_goods_class);

        $this->profile_menu('goods_list', 'goods_list');
        Tpl::showpage('store_goods_list.online');
    }
    
    /**
     * 编辑商品页面
     */
    public function edit_goodsOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_goods = Model('goods');
        $where = array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']);
        $goodscommon_info = $model_goods->getGoodeCommonInfo($where);
        if (empty($goodscommon_info)) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        
        $goodscommon_info['g_storage'] = $model_goods->getGoodsSum($where, 'goods_storage');
        $goodscommon_info['spec_name'] = unserialize($goodscommon_info['spec_name']);
        Tpl::output('goods', $goodscommon_info);

        if (intval($_GET['class_id']) > 0) {
            $goodscommon_info['gc_id'] = intval($_GET['class_id']);
        }
        $goods_class = Model('goods_class')->getGoodsClassLineForTag($goodscommon_info['gc_id']);
        Tpl::output('goods_class', $goods_class);

        $model_type = Model('type');
        // 获取类型相关数据
        if ($goods_class ['type_id'] > 0) {
            $typeinfo = $model_type->getAttr($goods_class['type_id'], $_SESSION['store_id'], $goodscommon_info['gc_id']);
            list($spec_json, $spec_list, $attr_list, $brand_list) = $typeinfo;
            Tpl::output('spec_json', $spec_json);
            Tpl::output('sign_i', count($spec_list));
            Tpl::output('spec_list', $spec_list);
            Tpl::output('attr_list', $attr_list);
            Tpl::output('brand_list', $brand_list);
        }

        // 取得商品规格的输入值
        $goods_array = $model_goods->getGoodsList($where, 'goods_id, goods_price,goods_storage,goods_serial,goods_spec');
        $sp_value = array();
        if (is_array($goods_array) && !empty($goods_array)) {

            // 取得已选择了哪些商品的属性
            $attr_checked_l = $model_type->typeRelatedList ( 'goods_attr_index', array (
                    'goods_id' => intval ( $goods_array[0]['goods_id'] )
            ), 'attr_value_id' );
            if (is_array ( $attr_checked_l ) && ! empty ( $attr_checked_l )) {
                $attr_checked = array ();
                foreach ( $attr_checked_l as $val ) {
                    $attr_checked [] = $val ['attr_value_id'];
                }
            }
            Tpl::output ( 'attr_checked', $attr_checked );
            
            $spec_checked = array();
            foreach ( $goods_array as $k => $v ) {
                $a = unserialize($v['goods_spec']);
                if (!empty($a)) {
                    foreach ($a as $key => $val){
                        $spec_checked[$key]['id'] = $key;
                        $spec_checked[$key]['name'] = $val;
                    }
                    $matchs = array_keys($a);
                    sort($matchs);
                    $id = str_replace ( ',', '', implode ( ',', $matchs ) );
                    $sp_value ['i_' . $id . '|price'] = $v['goods_price'];
                    $sp_value ['i_' . $id . '|id'] = $v['goods_id'];
                    $sp_value ['i_' . $id . '|stock'] = $v['goods_storage'];
                    $sp_value ['i_' . $id . '|sku'] = $v['goods_serial'];
                }
            }
            Tpl::output('spec_checked', $spec_checked);
        }
        Tpl::output ( 'sp_value', $sp_value );
        
        // 实例化店铺商品分类模型
        $store_goods_class = Model('my_goods_class')->getClassTree ( array (
                'store_id' => $_SESSION ['store_id'],
                'stc_state' => '1' 
        ) );
        Tpl::output('store_goods_class', $store_goods_class);
        $goodscommon_info['goods_stcids'] = trim($goodscommon_info['goods_stcids'], ',');
        Tpl::output('store_class_goods', explode(',', $goodscommon_info['goods_stcids']));
        
        // 是否能使用编辑器
        if(checkPlatformStore()){ // 平台店铺可以使用编辑器
            $editor_multimedia = true;
        } else {    // 三方店铺需要
            $editor_multimedia = false;
            if ($this->store_grade['sg_function'] == 'editor_multimedia') {
                $editor_multimedia = true;
            }
        }
        Tpl::output ( 'editor_multimedia', $editor_multimedia );
        
        // 小时分钟显示
        $hour_array = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
        Tpl::output('hour_array', $hour_array);
        $minute_array = array('05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');
        Tpl::output('minute_array', $minute_array);
        
        // 关联版式
        $plate_list = Model('store_plate')->getPlateList(array('store_id' => $_SESSION['store_id']), 'plate_id,plate_name,plate_position');
        $plate_list = array_under_reset($plate_list, 'plate_position', 2);
        Tpl::output('plate_list', $plate_list);
        
        $this->profile_menu('edit_detail','edit_detail');
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_add.step2');
    }
    
    /**
     * 编辑商品保存
     */
    public function edit_save_goodsOp() {
        $common_id = intval ( $_POST ['commonid'] );
        if (!chksubmit() || $common_id <= 0) {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }
        // 验证表单
        $obj_validate = new Validate ();
        $obj_validate->validateparam = array (
            array (
                "input" => $_POST["g_name"],
                "require" => "true",
                "message" => L('store_goods_index_goods_name_null')
            ),
            array (
                "input" => $_POST["g_price"],
                "require" => "true",
                "validator" => "Double",
                "message" => L('store_goods_index_goods_price_null')
            ) 
        );
        $error = $obj_validate->validate ();
        if ($error != '') {
            showDialog(L('error') . $error, urlShop('store_goods_online', 'index'));
        }

        $gc_id = intval($_POST['cate_id']);
        
        // 验证商品分类是否存在且商品分类是否为最后一级
        $data = H('goods_class') ? H('goods_class') : H('goods_class', true);
        if (!isset($data[$gc_id]) || isset($data[$gc_id]['child']) || isset($data[$gc_id]['childchild'])) {
            showDialog(L('store_goods_index_again_choose_category1'));
        }
        
        // 三方店铺验证是否绑定了该分类
        if (!checkPlatformStore()) {
            $where = array();
            $where['class_1|class_2|class_3'] = $gc_id;
            $where['store_id'] = $_SESSION['store_id'];
            $rs = Model('store_bind_class')->getStoreBindClassInfo($where);
            if (empty($rs)) {
                showDialog(L('store_goods_index_again_choose_category2'));
            }
        }
        
        $model_goods = Model ( 'goods' );

        $update_common = array();
        $update_common['goods_name']         = $_POST['g_name'];
        $update_common['goods_jingle']       = $_POST['g_jingle'];
        $update_common['gc_id']              = $gc_id;
        $update_common['gc_name']            = $_POST['cate_name'];
        $update_common['brand_id']           = $_POST['b_id'];
        $update_common['brand_name']         = $_POST['b_name'];
        $update_common['type_id']            = intval($_POST['type_id']);
        $update_common['goods_image']        = $_POST['image_path'];
        $update_common['goods_price']        = floatval($_POST['g_price']);
        $update_common['goods_marketprice']  = floatval($_POST['g_marketprice']);
        $update_common['goods_costprice']    = floatval($_POST['g_costprice']);
        $update_common['goods_discount']     = floatval($_POST['g_discount']);
        $update_common['goods_serial']       = $_POST['g_serial'];
        $update_common['goods_attr']         = serialize($_POST['attr']);
        $update_common['goods_body']         = $_POST['g_body'];
        $update_common['goods_commend']      = intval($_POST['g_commend']);
        $update_common['goods_state']        = ($this->store_info['store_state'] != 1) ? 0 : intval($_POST['g_state']);            // 店铺关闭时，商品下架
        $update_common['goods_selltime']     = strtotime($_POST['starttime']) + intval($_POST['starttime_H'])*3600 + intval($_POST['starttime_i'])*60;
        $update_common['goods_verify']       = (C('goods_verify') == 1) ? 10 : 1;
        $update_common['spec_name']          = is_array($_POST['spec']) ? serialize($_POST['sp_name']) : serialize(null);
        $update_common['spec_value']         = is_array($_POST['spec']) ? serialize($_POST['sp_val']) : serialize(null);
        $update_common['goods_vat']          = intval($_POST['g_vat']);
        $update_common['areaid_1']           = intval($_POST['province_id']);
        $update_common['areaid_2']           = intval($_POST['city_id']);
        $update_common['transport_id']       = ($_POST['freight'] == '0') ? '0' : intval($_POST['transport_id']); // 运费模板
        $update_common['transport_title']    = $_POST['transport_title'];
        $update_common['goods_freight']      = floatval($_POST['g_freight']);
        $update_common['goods_stcids']       = ',' . implode(',', array_unique($_POST['sgcate_id'])) . ',';    // 首尾需要加,
        $update_common['plateid_top']        = intval($_POST['plate_top']) > 0 ? intval($_POST['plate_top']) : '';
        $update_common['plateid_bottom']     = intval($_POST['plate_bottom']) > 0 ? intval($_POST['plate_bottom']) : '';
        
        $return = $model_goods->editGoodsCommon($update_common, array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
        if ($return) {
            // 清除原有规格数据
            $model_type = Model('type');
            $model_type->delGoodsAttr(array('goods_commonid' => $common_id));
            
            // 生成商品二维码
            require_once(BASE_RESOURCE_PATH.DS.'phpqrcode'.DS.'index.php');
            $PhpQRCode = new PhpQRCode();
            $PhpQRCode->set('pngTempDir',BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS);
                    
            // 更新商品规格
            $goodsid_array = array();
            $colorid_array = array();
            if (is_array ( $_POST ['spec'] )) {
                foreach ($_POST['spec'] as $value) {
                    $goods_info = $model_goods->getGoodsInfo(array('goods_id' => $value['goods_id'], 'goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']), 'goods_id');
                    if (!empty($goods_info)) {
                        $goods_id = $goods_info['goods_id'];
                        $update = array ();
                        $update['goods_commonid']    = $common_id;
                        $update['goods_name']        = $update_common['goods_name'] . ' ' . implode(' ', $value['sp_value']);
                        $update['goods_jingle']      = $update_common['goods_jingle'];
                        $update['store_id']          = $_SESSION['store_id'];
                        $update['store_name']        = $_SESSION['store_name'];
                        $update['gc_id']             = $update_common['gc_id'];
                        $update['brand_id']          = $update_common['brand_id'];
                        $update['goods_price']       = $value['price'];
                        $update['goods_marketprice'] = $update_common['goods_marketprice'];
                        $update['goods_serial']      = $value['sku'];
                        $update['goods_spec']        = serialize($value['sp_value']);
                        $update['goods_storage']     = $value['stock'];
                        $update['goods_state']       = $update_common['goods_state'];
                        $update['goods_verify']      = $update_common['goods_verify'];
                        $update['goods_edittime']    = TIMESTAMP;
                        $update['areaid_1']          = $update_common['areaid_1'];
                        $update['areaid_2']          = $update_common['areaid_2'];
                        $update['color_id']          = intval($value['color']);
                        $update['transport_id']      = $update_common['transport_id'];
                        $update['goods_freight']     = $update_common['goods_freight'];
                        $update['goods_vat']         = $update_common['goods_vat'];
                        $update['goods_commend']     = $update_common['goods_commend'];
                        $update['goods_stcids']      = $update_common['goods_stcids'];
                        $model_goods->editGoods($update, array('goods_id' => $goods_id));
                        // 生成商品二维码
                        $PhpQRCode->set('date',urlShop('goods', 'index', array('goods_id'=>$goods_id)));
                        $PhpQRCode->set('pngTempName', $goods_id . '.png');
                        $PhpQRCode->init();
                    } else {
                        $insert = array();
                        $insert['goods_commonid']    = $common_id;
                        $insert['goods_name']        = $update_common['goods_name'] . ' ' . implode(' ', $value['sp_value']);
                        $insert['goods_jingle']      = $update_common['goods_jingle'];
                        $insert['store_id']          = $_SESSION['store_id'];
                        $insert['store_name']        = $_SESSION['store_name'];
                        $insert['gc_id']             = $update_common['gc_id'];
                        $insert['brand_id']          = $update_common['brand_id'];
                        $insert['goods_price']       = $value['price'];
                        $insert['goods_marketprice'] = $update_common['goods_marketprice'];
                        $insert['goods_serial']      = $value['sku'];
                        $insert['goods_spec']        = serialize($value['sp_value']);
                        $insert['goods_storage']     = $value['stock'];
                        $insert['goods_image']       = $update_common['goods_image'];
                        $insert['goods_state']       = $update_common['goods_state'];
                        $insert['goods_verify']      = $update_common['goods_verify'];
                        $insert['goods_addtime']     = TIMESTAMP;
                        $insert['goods_edittime']    = TIMESTAMP;
                        $insert['areaid_1']          = $update_common['areaid_1'];
                        $insert['areaid_2']          = $update_common['areaid_2'];
                        $insert['color_id']          = intval($value['color']);
                        $insert['transport_id']      = $update_common['transport_id'];
                        $insert['goods_freight']     = $update_common['goods_freight'];
                        $insert['goods_vat']         = $update_common['goods_vat'];
                        $insert['goods_commend']     = $update_common['goods_commend'];
                        $insert['goods_stcids']      = $update_common['goods_stcids'];
                        $goods_id = $model_goods->addGoods($insert);
                        
                        // 生成商品二维码
                        $PhpQRCode->set('date',urlShop('goods', 'index', array('goods_id'=>$goods_id)));
                        $PhpQRCode->set('pngTempName', $goods_id . '.png');
                        $PhpQRCode->init();
                    }
                    $goodsid_array[] = intval($goods_id);
                    $colorid_array[] = intval($value['color']);
                    $model_type->addGoodsType($goods_id, $common_id, array('cate_id' => $_POST['cate_id'], 'type_id' => $_POST['type_id'], 'attr' => $_POST['attr']));
                }
            } else {
                $goods_info = $model_goods->getGoodsInfo(array('goods_spec' => serialize(null), 'goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']), 'goods_id');
                if (!empty($goods_info)) {
                    $goods_id = $goods_info['goods_id'];
                    $update = array ();
                    $update['goods_commonid']    = $common_id;
                    $update['goods_name']        = $update_common['goods_name'];
                    $update['goods_jingle']      = $update_common['goods_jingle'];
                    $update['store_id']          = $_SESSION['store_id'];
                    $update['store_name']        = $_SESSION['store_name'];
                    $update['gc_id']             = $update_common['gc_id'];
                    $update['brand_id']          = $update_common['brand_id'];
                    $update['goods_price']       = $update_common['goods_price'];
                    $update['goods_marketprice'] = $update_common['goods_marketprice'];
                    $update['goods_serial']      = $update_common['goods_serial'];
                    $update['goods_spec']        = serialize(null);
                    $update['goods_storage']     = intval($_POST['g_storage']);
                    $update['goods_state']       = $update_common['goods_state'];
                    $update['goods_verify']      = $update_common['goods_verify'];
                    $update['goods_edittime']    = TIMESTAMP;
                    $update['areaid_1']          = $update_common['areaid_1'];
                    $update['areaid_2']          = $update_common['areaid_2'];
                    $update['color_id']          = 0;
                    $update['transport_id']      = $update_common['transport_id'];
                    $update['goods_freight']     = $update_common['goods_freight'];
                    $update['goods_vat']         = $update_common['goods_vat'];
                    $update['goods_commend']     = $update_common['goods_commend'];
                    $update['goods_stcids']      = $update_common['goods_stcids'];
                    $model_goods->editGoods($update, array('goods_id' => $goods_id));
                    // 生成商品二维码
                    $PhpQRCode->set('date',urlShop('goods', 'index', array('goods_id'=>$goods_id)));
                    $PhpQRCode->set('pngTempName', $goods_id . '.png');
                    $PhpQRCode->init();
                } else {
                    $insert = array();
                    $insert['goods_commonid']    = $common_id;
                    $insert['goods_name']        = $update_common['goods_name'];
                    $insert['goods_jingle']      = $update_common['goods_jingle'];
                    $insert['store_id']          = $_SESSION['store_id'];
                    $insert['store_name']        = $_SESSION['store_name'];
                    $insert['gc_id']             = $update_common['gc_id'];
                    $insert['brand_id']          = $update_common['brand_id'];
                    $insert['goods_price']       = $update_common['goods_price'];
                    $insert['goods_marketprice'] = $update_common['goods_marketprice'];
                    $insert['goods_serial']      = $update_common['goods_serial'];
                    $insert['goods_spec']        = serialize(null);
                    $insert['goods_storage']     = intval($_POST['g_storage']);
                    $insert['goods_image']       = $update_common['goods_image'];
                    $insert['goods_state']       = $update_common['goods_state'];
                    $insert['goods_verify']      = $update_common['goods_verify'];
                    $insert['goods_addtime']     = TIMESTAMP;
                    $insert['goods_edittime']    = TIMESTAMP;
                    $insert['areaid_1']          = $update_common['areaid_1'];
                    $insert['areaid_2']          = $update_common['areaid_2'];
                    $insert['color_id']          = 0;
                    $insert['transport_id']      = $update_common['transport_id'];
                    $insert['goods_freight']     = $update_common['goods_freight'];
                    $insert['goods_vat']         = $update_common['goods_vat'];
                    $insert['goods_commend']     = $update_common['goods_commend'];
                    $insert['goods_stcids']      = $update_common['goods_stcids'];
                    $goods_id = $model_goods->addGoods($insert);
                    
                    // 生成商品二维码
                    $PhpQRCode->set('date',urlShop('goods', 'index', array('goods_id'=>$goods_id)));
                    $PhpQRCode->set('pngTempName', $goods_id . '.png');
                    $PhpQRCode->init();
                }
                $goodsid_array[] = intval($goods_id);
                $colorid_array[] = 0;
                $model_type->addGoodsType($goods_id, $common_id, array('cate_id' => $_POST['cate_id'], 'type_id' => $_POST['type_id'], 'attr' => $_POST['attr']));
            }
            // 清理商品数据
            $model_goods->delGoods(array('goods_id' => array('not in', $goodsid_array), 'goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
            // 清理商品图片表
            $colorid_array = array_unique($colorid_array);
            $model_goods->delGoodsImages(array('goods_commonid' => $common_id, 'color_id' => array('not in', $colorid_array)));
            // 更新商品默认主图
            $default_image_list = $model_goods->getGoodsImageList(array('goods_commonid' => $common_id, 'is_default' => 1), 'color_id,goods_image');
            if (!empty($default_image_list)) {
                foreach ($default_image_list as $val) {
                    $model_goods->editGoods(array('goods_image' => $val['goods_image']), array('goods_commonid' => $common_id, 'color_id' => $val['color_id']));
                }
            }
            
            // 商品加入上架队列
            if (isset($_POST['starttime'])) {
                $selltime = strtotime($_POST['starttime']) + intval($_POST['starttime_H'])*3600 + intval($_POST['starttime_i'])*60;
                if ($selltime > TIMESTAMP) {
                    $this->addcron(array('exetime' => $selltime, 'exeid' => $common_id, 'type' => 1), true);
                }
            }
            // 添加操作日志
            $this->recordSellerLog('编辑商品，平台货号：'.$common_id);
            showDialog(L('nc_common_op_succ'), $_POST['ref_url'], 'succ');
        } else {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }
        
    }
    
    /**
     * 编辑图片
     */
    public function edit_imageOp() {
        $common_id = intval($_GET['commonid']);
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }
        
        $model_goods = Model('goods');
        
        $image_list = $model_goods->getGoodsImageList(array('goods_commonid' => $common_id));
        $image_list = array_under_reset($image_list, 'color_id', 2);

        $img_array = $model_goods->getGoodsList(array('goods_commonid' => $common_id), 'color_id,goods_image', 'color_id');
        // 整理，更具id查询颜色名称
        if (!empty($img_array)) {
            foreach ($img_array as $val) {
                if (isset($image_list[$val['color_id']])) {
                    $image_array[$val['color_id']] = $image_list[$val['color_id']];
                } else {
                    $image_array[$val['color_id']][0]['goods_image'] = $val['goods_image'];
                    $image_array[$val['color_id']][0]['is_default'] = 1;
                }
                $colorid_array[] = $val['color_id'];
            }
        }
        Tpl::output('img', $image_array);

        $common_list = $model_goods->getGoodeCommonInfo(array('goods_commonid' => $common_id), 'spec_value');
        $spec_value = unserialize($common_list['spec_value']);
        Tpl::output('value', $spec_value['1']);
        
        $model_spec = Model('spec');
        $value_array = $model_spec->getSpecValueList(array('sp_value_id' => array('in', $colorid_array), 'store_id' => $_SESSION['store_id']), 'sp_value_id,sp_value_name');
        if (empty($value_array)) {
            $value_array[] = array('sp_value_id' => '0', 'sp_value_name' => '无颜色');
        }
        Tpl::output('value_array', $value_array);
        
        Tpl::output('commonid', $common_id);
        
        $this->profile_menu('edit_detail', 'edit_image');
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_add.step3');
    }
    
    /**
     * 保存商品图片
     */
    public function edit_save_imageOp() {
        if (chksubmit()) {
            $common_id = intval($_POST['commonid']);
            if ($common_id <= 0 || empty($_POST['img'])) {
                showDialog(L('wrong_argument'), urlShop('store_goods_online', 'index'));
            }
            $model_goods = Model('goods');
            // 删除原有图片信息
            $model_goods->delGoodsImages(array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
            // 保存
            $insert_array = array();
            foreach ($_POST['img'] as $key => $value) {
                foreach ($value as $k => $v) {
                    // 商品默认主图
                    $update_array = array();        // 更新商品主图
                    $update_where = array();
                    if ($k == 0 || $v['default'] == 1) {
                        $update_array['goods_image']    = $v['name'];
                        $update_where['goods_commonid'] = $common_id;
                        $update_where['store_id']       = $_SESSION['store_id'];
                        $update_where['color_id']       = $key;
                        // 更新商品主图
                        $model_goods->editGoods($update_array, $update_where);
                    }
                    if ($v['name'] == '') {
                        continue;
                    }
                    $tmp_insert = array();
                    $tmp_insert['goods_commonid']   = $common_id;
                    $tmp_insert['store_id']         = $_SESSION['store_id'];
                    $tmp_insert['color_id']         = $key;
                    $tmp_insert['goods_image']      = $v['name'];
                    $tmp_insert['goods_image_sort'] = ($v['default'] == 1) ? 0 : $v['sort'];
                    $tmp_insert['is_default']       = $v['default'];
                    $insert_array[] = $tmp_insert;
                }
            }
            $rs = $model_goods->addGoodsAll($insert_array, 'goods_images');
            if ($rs) {
            // 添加操作日志
            $this->recordSellerLog('编辑商品，平台货号：'.$common_id);
                showDialog(L('nc_common_op_succ'), $_POST['ref_url'], 'succ');
            } else {
                showDialog(L('nc_common_save_fail'), urlShop('store_goods_online', 'index'));
            }
        }
    }
    
    /**
     * 编辑分类
     */
    public function edit_classOp() {
        // 实例化商品分类模型
        $model_goodsclass = Model('goods_class');
        // 商品分类
        $goods_class = $model_goodsclass->getGoodsClass($_SESSION['store_id']);
        
        // 常用商品分类
        $model_staple = Model('goods_class_staple');
        $param_array = array();
        $param_array['member_id'] = $_SESSION['member_id'];
        $staple_array = $model_staple->getStapleList($param_array);
        
        Tpl::output('staple_array', $staple_array);
        Tpl::output('goods_class', $goods_class);
        
        Tpl::output('commonid', $_GET['commonid']);
        $this->profile_menu('edit_class', 'edit_class');
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_add.step1');
    }
    
    /**
     * 删除商品
     */
    public function drop_goodsOp() {
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        $commonid_array = explode(',', $common_id);
        $model_goods = Model('goods');
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $where['store_id'] = $_SESSION['store_id'];
        $return = $model_goods->delGoodsNoLock($where);
        if ($return) {
            // 添加操作日志
            $this->recordSellerLog('删除商品，平台货号：'.$common_id);
            showDialog(L('store_goods_index_goods_del_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_del_fail'), '', 'error');
        }
    }
    
    /**
     * 商品下架
     */
    public function goods_unshowOp() {
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        $commonid_array = explode(',', $common_id);
        $model_goods = Model('goods');
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $where['store_id'] = $_SESSION['store_id'];
        $return = Model('goods')->editProducesOffline($where);
        if ($return) {
            // 更新优惠套餐状态关闭
            $goods_list = $model_goods->getGoodsList($where, 'goods_id');
            if (!empty($goods_list)) {
                $goodsid_array = array();
                foreach ($goods_list as $val) {
                    $goodsid_array[] = $val['goods_id'];
                }
                Model('p_bundling')->editBundlingCloseByGoodsIds(array('goods_id' => array('in', $goodsid_array)));
            }
            // 添加操作日志
            $this->recordSellerLog('商品下架，平台货号：'.$common_id);
            showdialog(L('store_goods_index_goods_unshow_success'), getReferer() ? getReferer() : 'index.php?act=store_goods&op=goods_list', 'succ', '', 2);
        } else {
            showdialog(L('store_goods_index_goods_unshow_fail'), '', 'error');
        }
    }

    /**
     * 设置广告词
     */
    public function edit_jingleOp() {
        if (chksubmit()) {
            $common_id = $this->checkRequestCommonId($_POST['commonid']);
            $commonid_array = explode(',', $common_id);
            $where = array('goods_commonid' => array('in', $commonid_array), 'store_id' => $_SESSION['store_id']);
            $update = array('goods_jingle' => trim($_POST['g_jingle']));
            $return = Model('goods')->editProducesNoLock($where, $update);
            if ($return) {
                // 添加操作日志
                $this->recordSellerLog('设置广告词，平台货号：'.$common_id);
                showdialog(L('nc_common_op_succ'), 'reload', 'succ');
            } else {
                showdialog(L('nc_common_op_fail'), 'reload');
            }
        }
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        
        Tpl::showpage('store_goods_list.edit_jingle', 'null_layout');
    }

    /**
     * 设置关联版式
     */
    public function edit_plateOp() {
        if (chksubmit()) {
            $common_id = $this->checkRequestCommonId($_POST['commonid']);
            $commonid_array = explode(',', $common_id);
            $where = array('goods_commonid' => array('in', $commonid_array), 'store_id' => $_SESSION['store_id']);
            $update = array();
            $update['plateid_top']        = intval($_POST['plate_top']) > 0 ? intval($_POST['plate_top']) : '';
            $update['plateid_bottom']     = intval($_POST['plate_bottom']) > 0 ? intval($_POST['plate_bottom']) : '';
            $return = Model('goods')->editGoodsCommon($update, $where);
            if ($return) {
                // 添加操作日志
                $this->recordSellerLog('设置关联版式，平台货号：'.$common_id);
                showdialog(L('nc_common_op_succ'), 'reload', 'succ');
            } else {
                showdialog(L('nc_common_op_fail'), 'reload');
            }
        }
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        
        // 关联版式
        $plate_list = Model('store_plate')->getPlateList(array('store_id' => $_SESSION['store_id']), 'plate_id,plate_name,plate_position');
        $plate_list = array_under_reset($plate_list, 'plate_position', 2);
        Tpl::output('plate_list', $plate_list);
        
        Tpl::showpage('store_goods_list.edit_plate', 'null_layout');
    }
    
    /**
     * 验证commonid
     */
    private function checkRequestCommonId($common_ids) {
        if (!preg_match('/^[\d,]+$/i', $common_ids)) {
            showdialog(L('para_error'), '', 'error');
        }
        return $common_ids;
    }
    
    /**
     * ajax获取商品列表
     */
    public function get_goods_list_ajaxOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            echo 'false';exit();
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodeCommonInfo(array('store_id' => $_SESSION['store_id'], 'goods_commonid' => $common_id), 'spec_name');
        if (empty($goodscommon_list)) {
            echo 'false';exit();
        }
        $goods_list = $model_goods->getGoodsList(array('store_id' => $_SESSION['store_id'], 'goods_commonid' => $common_id), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage,goods_image');
        if (empty($goods_list)) {
            echo 'false';exit();
        }
        
        $spec_name = array_values((array)unserialize($goodscommon_list['spec_name']));
        foreach ($goods_list as $key => $val) {
            $goods_spec = array_values((array)unserialize($val['goods_spec']));
            $spec_array = array();
            foreach ($goods_spec as $k => $v) {
                $spec_array[] = '<div class="goods_spec">' . $spec_name[$k] . L('nc_colon') . '<em title="' . $v . '">' . $v .'</em>' . '</div>';
            }
            $goods_list[$key]['goods_image'] = thumb($val, '60');
            $goods_list[$key]['goods_spec'] = implode('', $spec_array);
            $goods_list[$key]['alarm'] = ($this->store_info['store_storage_alarm'] != 0 && $val['goods_storage'] <= $this->store_info['store_storage_alarm']) ? 'style="color:red;"' : '';
            $goods_list[$key]['url'] = urlShop('goods', 'index', array('goods_id' => $val['goods_id']));
        }

        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK') {
            Language::getUTF8($goods_list);
        }
        echo json_encode($goods_list);
    }

    /**
     * 用户中心右边，小导航
     * 
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key='') {
        $menu_array = array();
        switch ($menu_type) {
        	case 'goods_list':
        	    $menu_array = array(
        	       array('menu_key' => 'goods_list', 'menu_name' => '出售中的商品', 'menu_url' => urlShop('store_goods_online', 'index'))
        	    );
        	    break;
        	case 'edit_detail':
                $menu_array = array(
                    array('menu_key' => 'edit_detail',    'menu_name' => '编辑商品',   'menu_url' => urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                    array('menu_key' => 'edit_image',     'menu_name' => '编辑图片',     'menu_url' => urlShop('store_goods_online', 'edit_image', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))))
                );
        	    break;
        	case 'edit_class':
                $menu_array = array(
                    array('menu_key' => 'edit_class',     'menu_name' => '选择分类', 'menu_url' => urlShop('store_goods_online', 'edit_class', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                    array('menu_key' => 'edit_detail',    'menu_name' => '编辑商品',   'menu_url' => urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                    array('menu_key' => 'edit_image',     'menu_name' => '编辑图片',     'menu_url' => urlShop('store_goods_online', 'edit_image', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))))
                );
        	    break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }

}