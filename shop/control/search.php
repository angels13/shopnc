<?php
/**
 * 商品列表
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class searchControl extends BaseHomeControl {


    //每页显示商品数
    const PAGESIZE = 24;

    //模型对象
    private $_model_search;

    public function indexOp() {
        Language::read('home_goods_class_index');
        $this->_model_search = Model('search');

        //优先从全文索引库里查找
        list($indexer_ids,$indexer_count) = $this->_indexer_search();
        $data_attr = $this->_get_attr_list();

        //处理排序
        $order = 'goods_id desc';
        if (in_array($_GET['key'],array('1','2','3'))) {
            $sequence = $_GET['order'] == '1' ? 'asc' : 'desc';
            $order = str_replace(array('1','2','3'), array('goods_salenum','goods_click','goods_price'), $_GET['key']);
            $order .= ' '.$sequence;
        }
        $model_goods = Model('goods');
        if (!isset($data_attr['sign']) || $data_attr['sign'] === true) {
            // 字段
            $fields = "goods_id,goods_commonid,goods_name,goods_jingle,gc_id,store_id,store_name,goods_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count";

            $condition = array();
            if (is_array($indexer_ids)) {

                //商品主键搜索
                $condition['goods_id'] = array('in',$indexer_ids);
                $goods_list = $model_goods->getGoodsOnlineList($condition, $fields, 0, $order, self::PAGESIZE, null, false);
                pagecmd('setEachNum',self::PAGESIZE);
                pagecmd('setTotalNum',$indexer_count);

            } else {

                //执行正常搜索
                if (isset($data_attr['gcid_array'])) {
                    $condition['gc_id'] = array('in', $data_attr['gcid_array']);
                }
                if (intval($_GET['b_id']) > 0) {
                    $condition['brand_id'] = intval($_GET['b_id']);
                }
                if ($_GET['keyword'] != '') {
                    $condition['goods_name|goods_jingle'] = array('like', '%' . $_GET['keyword'] . '%');
                }
                if (intval($_GET['area_id']) > 0) {
                    $condition['areaid_1'] = intval($_GET['area_id']);
                }
                if (in_array($_GET['type'], array(1,2))) {
                    if ($_GET['type'] == 1) {
                        $condition['store_id'] = DEFAULT_PLATFORM_STORE_ID;
                    } else if ($_GET['type'] == 2) {
                        $condition['store_id'] = array('neq', DEFAULT_PLATFORM_STORE_ID);
                    }
                }
                if (isset($data_attr['goodsid_array'])){
                    $condition['goods_id'] = array('in', $data_attr['goodsid_array']);
                }
                $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fields, $order, self::PAGESIZE);
            }

            Tpl::output('show_page1', $model_goods->showpage(4));
            Tpl::output('show_page', $model_goods->showpage(5));

            // 商品多图
            if (!empty($goods_list)) {
                $goodsid_array = array();       // 商品id数组
                $commonid_array = array(); // 商品公共id数组
                $storeid_array = array();       // 店铺id数组
                foreach ($goods_list as $value) {
                    $goodsid_array[] = $value['goods_id'];
                    $commonid_array[] = $value['goods_commonid'];
                    $storeid_array[] = $value['store_id'];
                }
                $goodsid_array = array_unique($goodsid_array);
                $commonid_array = array_unique($commonid_array);
                $storeid_array = array_unique($storeid_array);

                // 商品多图
                $goodsimage_more = Model('goods')->getGoodsImageList(array('goods_commonid' => array('in', $commonid_array)));

                // 店铺
                $store_list = Model('store')->getStoreMemberIDList($storeid_array);

                // 团购
                if (C('groupbuy_allow')) {
                    $groupbuy_list = Model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));
                }

                if (C('promotion_allow')) {
                    // 限时折扣
                    $xianshi_list = Model('p_xianshi_goods')->getXianshiGoodsListByGoodsString(implode(',', $goodsid_array));
                }

                foreach ($goods_list as $key => $value) {
                    // 商品多图
                    foreach ($goodsimage_more as $v) {
                        if ($value['goods_commonid'] == $v['goods_commonid'] && $value['store_id'] == $v['store_id'] && $value['color_id'] == $v['color_id']) {
                            $goods_list[$key]['image'][] = $v;
                        }
                    }
                    // 店铺的开店会员编号
                    $store_id = $value['store_id'];
                    $goods_list[$key]['member_id'] = $store_list[$store_id]['member_id'];
                    $goods_list[$key]['store_domain'] = $store_list[$store_id]['store_domain'];
                    // 团购
                    if (isset($groupbuy_list[$value['goods_commonid']])) {
                        $goods_list[$key]['goods_price'] = $groupbuy_list[$value['goods_commonid']]['groupbuy_price'];
                        $goods_list[$key]['group_flag'] = true;
                    }
                    if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                        $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshi_price'];
                        $goods_list[$key]['xianshi_flag'] = true;
                    }
                }
            }
            Tpl::output('goods_list', $goods_list);
        }
        Tpl::output('class_name',  @$data_attr['gc_name']);

        //显示左侧分类
        if (intval($_GET['cate_id']) > 0) {
            $goods_class_array = $this->_model_search->getLeftCategory(array($_GET['cate_id']));
        } elseif ($_GET['keyword'] != '') {
            $goods_class_array = $this->_model_search->getTagCategory($_GET['keyword']);
        }
        Tpl::output('goods_class_array', $goods_class_array);

        if ($_GET['keyword'] == ''){
            //不显示无商品的搜索项
            if (C('fullindexer.open')) {
                $data_attr['brand_array'] = $this->_model_search->delInvalidBrand($data_attr['brand_array']);
                $data_attr['attr_array'] = $this->_model_search->delInvalidAttr($data_attr['attr_array']);   
            }
        }

        //抛出搜索属性
        Tpl::output('brand_array',$data_attr['brand_array']);
        Tpl::output('attr_array',$data_attr['attr_array']);
//         Tpl::output('cate_array',$data_attr['cate_array']);
        Tpl::output('checked_brand', $data_attr['checked_brand']);
        Tpl::output('checked_attr', $data_attr['checked_attr']);

        $model_goods_class = Model('goods_class');

        // SEO
        if ($_GET['keyword'] == '') {
            $seo_class_name = @$data_attr['gc_name'];
            if (is_numeric($_GET['cate_id']) && empty($_GET['keyword'])) {
                $seo_info = $model_goods_class->getKeyWords(intval($_GET['cate_id']));
                if (empty($seo_info[1])) {
                    $seo_info[1] = C('site_name') . ' - ' . $seo_class_name;
                }
                Model('seo')->type($seo_info)->param(array('name' => $seo_class_name))->show();
            } elseif ($_GET['keyword'] != '') {
                Tpl::output('html_title', (empty($_GET['keyword']) ? '' : $_GET['keyword'] . ' - ') . C('site_name') . L('nc_common_search'));
            }
        }

        // 当前位置导航
        $nav_link_list = $model_goods_class->getGoodsClassNav(intval($_GET['cate_id']));
        Tpl::output('nav_link_list', $nav_link_list );

        // 得到自定义导航信息
        $nav_id = intval($_GET['nav_id']) ? intval($_GET['nav_id']) : 0;
        Tpl::output('index_sign', $nav_id);

        // 地区
        require(BASE_DATA_PATH.'/area/area.php');
        Tpl::output('area_array', $area_array);

        loadfunc('search');

        // 浏览过的商品
        $viewed_goods = $model_goods->getViewedGoodsList();
        Tpl::output('viewed_goods',$viewed_goods);

        Tpl::showpage('search');

    }

    /**
     * 全文搜索
     * @return array 商品主键，搜索结果总数
     */
    private function _indexer_search() {
        if (!C('fullindexer.open')) return array(null,0);

        $condition = array();

        //拼接条件
        if (intval($_GET['cate_id']) > 0) {
            $cate_id = intval($_GET['cate_id']);
            $goods_class = H('goods_class') ? H('goods_class') : H('goods_class', true);
            $depth = $goods_class[$cate_id]['depth'];
            $cate_field = 'cate_'.$depth;
            $condition['cate']['key'] = $cate_field;
            $condition['cate']['value'] = $cate_id;
        }
        if ($_GET['keyword'] != '') {
            $condition['keyword'] = $_GET['keyword'];
        }
        if (intval($_GET['b_id']) > 0) {
            $condition['brand_id'] = intval($_GET['b_id']);
        }
        if (preg_match('/^[\d_]+$/',$_GET['a_id'])) {
            $attr_ids = explode('_',$_GET['a_id']);
            if (is_array($attr_ids)){
                foreach ($attr_ids as $v) {
                    if (intval($v) > 0) {
                        $condition['attr_id'][] = intval($v);
                    }
                }
            }
        }
        if (in_array($_GET['type'],array('1','2'))) {
            $condition['store_id'] = $_GET['type'];
        }
        if (intval($_GET['area_id']) > 0) {
            $condition['area_id'] = intval($_GET['area_id']);
        }

        //拼接排序(销量,浏览量,价格)
        $order = array();
        $order['key'] = 'goods_id';
        $order['value'] = false;
        if (in_array($_GET['key'],array('1','2','3'))) {
            $order['value'] = $_GET['order'] == '1' ? true : false;
            $order['key'] = str_replace(array('1','2','3'), array('goods_salenum','goods_click','goods_price'), $_GET['key']);
        }

        //取得商品主键等信息
        $result = $this->_model_search->getIndexerList($condition,$order,self::PAGESIZE);
        if ($result !== false) {
            list($indexer_ids,$indexer_count) = $result;
            //如果全文搜索发生错误，后面会再执行数据库搜索
        } else {
            $indexer_ids = null;
            $indexer_count = 0;
        }

        return array($indexer_ids,$indexer_count);
    }

    /**
     * 取得商品属性
     */
    private function _get_attr_list() {
        if (intval($_GET['cate_id']) > 0) {
            $data = $this->_model_search->getAttrList();
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * 获得推荐商品
     */
    public function get_booth_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = H('goods_class') ? H('goods_class') : H('goods_class', true);
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('p_booth')->getBoothGoodsList(array('gc_id' => array('in', $gcid_array)), 'goods_id', 0, 4, 'rand()');
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            return false;
        }
        $commonid_array = array();
        foreach ($goods_list as $val) {
            $commonid_array[] = $val['goods_commonid'];
        }
        $groupbuy_list = Model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));
        $xianshi_list = Model('p_xianshi_goods')->getXianshiGoodsListByGoodsString(implode(',', $goodsid_array));
        foreach ($goods_list as $key => $value) {
            // 团购
            if (isset($groupbuy_list[$value['goods_commonid']])) {
                $goods_list[$key]['goods_price'] = $groupbuy_list[$value['goods_commonid']]['groupbuy_price'];
                $goods_list[$key]['group_flag'] = true;
            }
            if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshi_price'];
                $goods_list[$key]['xianshi_flag'] = true;
            }
        }
        Tpl::output('goods_list', $goods_list);
        Tpl::output('groupbuy_list', $groupbuy_list);
        Tpl::output('xianshi_list', $xianshi_list);
        Tpl::showpage('goods.booth', 'null_layout');
    }

	public function auto_completeOp() {
	    require(BASE_DATA_PATH.'/xs/lib/XS.php');
	    $obj_doc = new XSDocument();
	    $obj_xs = new XS('2014');
	    $obj_index = $obj_xs->index;
	    $obj_search = $obj_xs->search;
	    $obj_search->setCharset(CHARSET);
        try {
            $corrected = $obj_search->getExpandedQuery($_GET['term']);
            if (count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word)
                {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                exit(json_encode($data));
            }
        } catch (XSException $e) {
            print_R($e->getMessage());exit;
        }
	}

}