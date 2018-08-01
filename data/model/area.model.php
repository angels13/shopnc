<?php
/**
 * 地区模型
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

class areaModel extends Model{

    public function __construct() {
        parent::__construct('area');
    }

    public function getAreaList($condition = array(),$fields = '*', $group = '') {
        return $this->where($condition)->field($fields)->limit(false)->group($group)->select();
    }
}