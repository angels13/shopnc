<?php
/**
 * 货到付款地区设置
 *
 * @copyright  Copyright (c) 2007-2014 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class offpay_areaModel extends Model {
    public function __construct() {
        parent::__construct('offpay_area');
    }
    
    /**
     * 增加某店铺设置
     *
     * @param unknown_type $data
     * @return unknown
     */
    public function addArea($data) {
        return $this->insert($data);
    }
    
    /**
     * 取得某店铺设置
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function getAreaInfo($condition) {
        return $this->where($condition)->find();
    }
    
    /**
     * 更新某店铺设置
     *
     * @param unknown_type $condition
     * @param unknown_type $data
     * @return unknown
     */
    public function updateArea($condition,$data) {
        return $this->where($condition)->update($data);
    }
    
    /**
     * 某县级地区是否支持货到付款
     *
     * @param unknown_type $area_id
     * @param int $store_id 店铺ID（目前只会传平台店铺）
     * @return unknown
     */
    public function checkSupportOffpay($area_id, $store_id) {
        if (empty($area_id)) return false;
        $area = $this->getAreaInfo(array('store_id'=>$store_id));
        if (!empty($area['area_id'])) {
            $area_id_array = unserialize($area['area_id']);
        } else {
            $area_id_array = array();
        }
        if (empty($area_id_array)) {
            $area_id_array = array();
        }
        return in_array($area_id,$area_id_array) ? true : false;
    }
}