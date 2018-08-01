<?php
/**
 * 前台品牌分类
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
class brandControl extends BaseHomeControl {
	public function indexOp(){
		//读取语言包
		Language::read('home_brand_index');
		//分类导航
		$nav_link = array(
			0=>array(
				'title'=>Language::get('homepage'),
				'link'=>'index.php'
			),
			1=>array(
				'title'=>Language::get('brand_index_all_brand')
			)
		);
		Tpl::output('nav_link_list',$nav_link);
		
        //获得品牌列表
        $model = Model();
        $brand_c_list = $model->table('brand')->where(array('brand_apply'=>'1'))->order('brand_sort asc')->select();
        $brands = $this->_tidyBrand($brand_c_list);
        extract($brands);
        Tpl::output('brand_c',$brand_listnew);
        Tpl::output('brand_class',$brand_class);
        Tpl::output('brand_r',$brand_r_list);
        Tpl::output('html_title',Language::get('brand_index_brand_list'));
		
		//页面输出
		Tpl::output('index_sign','brand');
		Model('seo')->type('brand')->show();
		Tpl::showpage('brand');
	}
	
	/**
	 * 整理品牌
	 * 所有品牌全部显示在一级类目下，不显示二三级类目
	 * @param array $brand_c_list
	 * @return array
	 */
	private function _tidyBrand($brand_c_list) {
	    $brand_listnew = array();
	    $brand_class = array();
	    $brand_r_list = array();
	    if (!empty($brand_c_list) && is_array($brand_c_list)){
	        $goods_class = H('goods_class') ? H('goods_class') : H('goods_class', true);
	        foreach ($brand_c_list as $key=>$brand_c){
                $gc_array = $this->_getTopClass($goods_class, $brand_c['class_id']);
                if (empty($gc_array)) {
                    $brand_listnew[0][] = $brand_c;
                    $brand_class[0]['brand_class'] = '其他';
                } else {
                    $brand_listnew[$gc_array['gc_id']][] = $brand_c;
                    $brand_class[$gc_array['gc_id']]['brand_class'] = $gc_array['gc_name'];
                }
	            //推荐品牌
	            if ($brand_c['brand_recommend'] == 1){
	                $brand_r_list[] = $brand_c;
	            }
	        }
	    }
	    krsort($brand_class);
	    krsort($brand_listnew);
	    return array('brand_listnew' => $brand_listnew, 'brand_class' => $brand_class, 'brand_r_list' => $brand_r_list);
	}
	
	/**
	 * 获取顶级商品分类
	 * 递归调用
	 * @param array $goods_class
	 * @param int $gc_id
	 * @return array
	 */
	private function _getTopClass($goods_class, $gc_id) {
	    if (!isset($goods_class[$gc_id])) {
	        return null;
	    }
	    return $goods_class[$gc_id]['gc_parent_id'] == 0 ? $goods_class[$gc_id] : $this->_getTopClass($goods_class, $goods_class[$gc_id]['gc_parent_id']);
	}
	
	/**
	 * 品牌商品列表
	 */
	public function listOp(){
		Language::read('home_brand_index');
		$lang	= Language::getLangContent();
		/**
		 * 验证品牌
		 */
		$model_brand = Model('brand');
		$brand_id = intval($_GET['brand']);
		$brand_lise = $model_brand->getOneBrand($brand_id);
		if(!$brand_lise){
			showMessage($lang['wrong_argument'],'index.php','html','error');
		}

		/**
		 * 获得推荐品牌
		 */
		$brand_class = Model('brand');
		//获得列表
		$brand_r_list = $brand_class->getBrandList(array(
			'brand_recommend'=>1,
			'field'=>'brand_id,brand_name,brand_pic',
			'brand_apply'=>1,
			'limit'=>'0,10'
		));
		Tpl::output('brand_r',$brand_r_list);

        // 得到排序方式
        $order = 'goods_id desc';
        if (!empty($_GET['key'])) {
            $order_tmp = trim($_GET['key']);
            $sequence = $_GET['order'] == 1 ? 'asc' : 'desc';
            switch ($order_tmp) {
                case '1' : // 销量
                    $order = 'goods_salenum' . ' ' . $sequence;
                    break;
                case '2' : // 浏览量
                    $order = 'goods_click' . ' ' . $sequence;
                    break;
                case '3' : // 价格
                    $order = 'goods_price' . ' ' . $sequence;
                    break;
            }
        }

        // 字段
        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count";
        // 条件
        $where = array();
        $where['brand_id'] = $brand_id;
        if (intval($_GET['area_id']) > 0) {
            $where['areaid_1'] = intval($_GET['area_id']);
        }
        if (in_array($_GET['type'], array(1,2))) {
            if ($_GET['type'] == 1) {
                $where['store_id'] = DEFAULT_PLATFORM_STORE_ID;
            } else if ($_GET['type'] == 2) {
                $where['store_id'] = array('neq', DEFAULT_PLATFORM_STORE_ID);
            }
        }
        $model_goods = Model('goods');
        $goods_list = $model_goods->getGoodsListByColorDistinct($where, $fieldstr, $order, 24);
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
            $goodsimage_more = $model_goods->getGoodsImageList(array('goods_commonid' => array('in', $commonid_array)));
            // 店铺
            $store_list = Model('store')->getStoreMemberIDList($storeid_array);
            // 团购
            $groupbuy_list = Model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));
            // 限时折扣
            $xianshi_list = Model('p_xianshi_goods')->getXianshiGoodsListByGoodsString(implode(',', $goodsid_array));
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

        // 地区
        require(BASE_DATA_PATH.'/area/area.php');
        Tpl::output('area_array', $area_array);
        
        loadfunc('search');
        /**
         * 取浏览过产品的cookie(最大四组)
         */
        $viewed_goods = $model_goods->getViewedGoodsList();
        Tpl::output('viewed_goods',$viewed_goods);

		/**
		 * 分类导航
		 */
		$nav_link = array(
			0=>array(
				'title'=>$lang['homepage'],
				'link'=>'index.php'
			),
			1=>array(
				'title'=>$lang['brand_index_all_brand'],
				'link'=>'index.php?act=brand'
			),
			2=>array(
				'title'=>$brand_lise['brand_name']
			)
		);
		Tpl::output('nav_link_list',$nav_link);
		/**
		 * 页面输出
		 */
		Tpl::output('index_sign','brand');


		Model('seo')->type('brand_list')->param(array('name'=>$brand_lise['brand_name']))->show();
		Tpl::showpage('brand_goods');
	}
}
