<?php
/**
 * 买家发票模型
 *
 * @copyright  Copyright (c) 2007-2014 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class invoiceModel extends Model {
    
    public function __construct() {
        parent::__construct('invoice');
    }

    /**
     * 取得买家默认发票
     *
     * @param array $condition
     */
    public function getDefaultInvInfo($condition = array()) {
        return $this->where($condition)->order('inv_state asc')->find();
    }

    /**
     * 取得单条发票信息
     *
     * @param array $condition
     */
    public function getInvInfo($condition = array()) {
        return $this->where($condition)->find();
    }
    
    /**
     * 取得发票列表
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function getInvList($condition, $limit = '') {
        return $this->where($condition)->limit($limit)->select();
    }

    /**
     * 删除发票信息
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function delInv($condition) {
        return $this->where($condition)->delete();
    }

    /**
     * 新增发票信息
     *
     * @param unknown_type $data
     * @return unknown
     */
    public function addInv($data) {
        return $this->insert($data);
    }

}