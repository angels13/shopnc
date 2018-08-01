<?php
/**
 * 店铺模型管理
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
class storeModel extends Model {
    public function __construct(){
        parent::__construct('store');
    }

	/**
	 * 查询店铺列表
     *
	 * @param array $condition 查询条件
	 * @param int $page 分页数
	 * @param string $order 排序
	 * @param string $field 字段
	 * @param string $limit 取多少条
     * @return array
	 */
    public function getStoreList($condition, $page = null, $order = '', $field = '*', $limit = '') {
        $result = $this->field($field)->where($condition)->order($order)->limit($limit)->page($page)->select();
        return $result;
    }

	/**
	 * 查询有效店铺列表
     *
	 * @param array $condition 查询条件
	 * @param int $page 分页数
	 * @param string $order 排序
	 * @param string $field 字段
     * @return array
	 */
    public function getStoreOnlineList($condition, $page = null, $order = '', $field = '*') {
        $condition['store_state'] = 1;
        return $this->getStoreList($condition, $page, $order, $field);
    }

    /**
     * 店铺数量
     * @param array $condition
     * @return int
     */
    public function getStoreCount($condition) {
        return $this->where($condition)->count();
    }

    /**
	 * 按店铺编号查询店铺的开店信息
     *
	 * @param array $storeid_array 店铺编号
     * @return array
	 */
    public function getStoreMemberIDList($storeid_array) {
        $store_list = $this->table('store')->where(array('store_id'=> array('in', $storeid_array)))->field('store_id,member_id,store_domain')->key('store_id')->select();
        return $store_list;
    }

    /**
	 * 查询店铺信息
     *
	 * @param array $condition 查询条件
     * @return array
	 */
    public function getStoreInfo($condition) {
        $store_info = $this->where($condition)->find();
        if(!empty($store_info)) {
            if(!empty($store_info['store_presales'])) $store_info['store_presales'] = unserialize($store_info['store_presales']);
            if(!empty($store_info['store_aftersales'])) $store_info['store_aftersales'] = unserialize($store_info['store_aftersales']);

            //商品数
            $model_goods = Model('goods');
            $store_info['goods_count'] = $model_goods->getGoodsCommonOnlineCount(array('store_id' => $store_info['store_id']));

            //店铺评价
            $model_evaluate_store = Model('evaluate_store');
            $store_evaluate_info = $model_evaluate_store->getEvaluateStoreInfoByStoreID($store_info['store_id'], $store_info['sc_id']);

            $store_info = array_merge($store_info, $store_evaluate_info);
        }
        return $store_info;
    }

    /**
	 * 通过店铺编号查询店铺信息
     *
	 * @param int $store_id 店铺编号
     * @return array
	 */
    public function getStoreInfoByID($store_id) {
        $store_info = rcache($store_id, 'store_info');
        if(empty($store_info)) {
            $store_info = $this->getStoreInfo(array('store_id' => $store_id));
            wcache($store_id, $store_info, 'store_info');
        }
        return $store_info;
    }

    public function getStoreOnlineInfoByID($store_id) {
        $store_info = $this->getStoreInfoByID($store_id);
        if(empty($store_info) || $store_info['store_state'] == '0') {
            return null;
        } else {
            return $store_info;
        }
    }

    public function getStoreIDString($condition) {
        $condition['store_state'] = 1;
        $store_list = $this->getStoreList($condition);
        $store_id_string = '';
        foreach ($store_list as $value) {
            $store_id_string .= $value['store_id'].',';
        }
        return $store_id_string;
    }

	/*
	 * 添加店铺
     *
	 * @param array $param 店铺信息
	 * @return bool
	 */
    public function addStore($param){
        return $this->insert($param);
    }

	/*
	 * 编辑店铺
     *
	 * @param array $update 更新信息
	 * @param array $condition 条件
	 * @return bool
	 */
    public function editStore($update, $condition){
        //清空缓存
        $store_list = $this->getStoreList($condition);
        foreach ($store_list as $value) {
            wcache($value['store_id'], array(), 'store_info');
        }

        return $this->where($condition)->update($update);
    }

	/*
	 * 删除店铺
     *
	 * @param array $condition 条件
	 * @return bool
	 */
    public function delStore($condition){
        $store_info = $this->getStoreInfo($condition);
        //删除店铺相关图片
        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_label']);
        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_banner']);
        if($store_info['store_slide'] != ''){
            foreach(explode(',', $store_info['store_slide']) as $val){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_SLIDE.DS.$val);
            }
        }

        //清空缓存
        wcache($store_info['store_id'], array(), 'store_info');

        return $this->where($condition)->delete();
    }

    /**
     * 获取商品销售排行
     *
     * @param int $store_id 店铺编号
     * @param int $limit 数量
     * @return array	商品信息
     */
    public function getHotSalesList($store_id, $limit = 5) {
        $prefix = 'store_hot_sales_list_' . $limit;
        $hot_sales_list = rcache($store_id, $prefix);
        if(empty($hot_sales_list)) {
            $model_goods = Model('goods');
            $hot_sales_list = $model_goods->getGoodsOnlineList(array('store_id' => $store_id), '*', 0, 'goods_salenum desc', $limit);
            wcache($store_id, $hot_sales_list, $prefix);
        }
        return $hot_sales_list;
    }

    /**
     * 获取商品收藏排行
     *
     * @param int $store_id 店铺编号
     * @param int $limit 数量
     * @return array	商品信息
     */
    public function getHotCollectList($store_id, $limit = 5) {
        $prefix = 'store_collect_sales_list_' . $limit;
        $hot_collect_list = rcache($store_id, $prefix);
        if(empty($hot_collect_list)) {
            $model_goods = Model('goods');
            $hot_collect_list = $model_goods->getGoodsOnlineList(array('store_id' => $store_id), '*', 0, 'goods_collect desc', $limit);
            wcache($store_id, $hot_collect_list, $prefix);
        }
        return $hot_collect_list;
    }

}
