<?php
/**
 * 我的地址
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
class addressModel extends Model {
    
    public function __construct() {
        parent::__construct('address');
    }
    
    /**
     * 取得买家默认收货地址
     *
     * @param array $condition
     */
    public function getDefaultAddressInfo($condition = array()) {
        return $this->where($condition)->order('is_default desc,address_id desc')->find();
    }
    
    public function getAddressInfo($condition) {
        return $this->where($condition)->find();
    }
    
	/**
	 * 读取地址列表
	 *
	 * @param 
	 * @return array 数组格式的返回结果
	 */
	public function getAddressList($condition, $order='address_id asc'){
        return $this->where($condition)->order($order)->select();
	}

	/**
	 * 构造检索条件
	 *
	 * @param array $condition 检索条件
	 * @return string 数组形式的返回结果
	 */
	private function _condition($condition){
		$condition_str = '';
		
		if ($condition['member_id'] != ''){
			$condition_str .= " member_id = '". intval($condition['member_id']) ."'";
		}
		
		return $condition_str;
	}
	
	/**
	 * 新增地址
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function addAddress($param){
        return $this->insert($param);
	}
	
	/**
	 * 取单个地址
	 *
	 * @param int $area_id 地址ID
	 * @return array 数组类型的返回结果
	 */
	public function getOneAddress($id){
		if (intval($id) > 0){
			$param = array();
			$param['table'] = 'address';
			$param['field'] = 'address_id';
			$param['value'] = intval($id);
			$result = Db::getRow($param);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 更新地址信息
	 *
	 * @param array $param 更新数据
	 * @return bool 布尔类型的返回结果
	 */
	public function editAddress($update, $condition){
        return $this->where($condition)->update($update);
	}
	/**
	 * 验证地址是否属于当前用户
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function checkAddress($member_id,$address_id) {
		/**
		 * 验证地址是否属于当前用户
		 */
		$check_array = self::getOneAddress($address_id);
		if ($check_array['member_id'] == $member_id){
			unset($check_array);
			return true;
		}
		unset($check_array);
		return false;
	}
	/**
	 * 删除地址
	 *
	 * @param int $id 记录ID
	 * @return bool 布尔类型的返回结果
	 */
	public function delAddress($condition){
	    return $this->where($condition)->delete();
	}
}
