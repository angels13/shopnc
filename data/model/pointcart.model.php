<?php
/**
 * 积分礼品购物车
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class pointcartModel {
	/**
	 * 礼品购物车保存
	 *
	 * @param	array $param 资料
	 */
	public function addPointCart($param) {
		if(empty($param)) {
			return false;
		}
		$result	= Db::insert('points_cart',$param);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 礼品购物车列表
	 *
	 * @param array $condition 条件数组
	 * @param array $page   分页
	 * @param array $field   查询字段
	 * @param array $page   分页  
	 */
	public function getPointCartList($condition,$page='',$field='*'){
		$condition_str	= $this->getCondition($condition);
		$param	= array();
		$param['table']	= 'points_cart';
		$param['where']	= $condition_str;
		$param['field'] = $field;
		$param['order'] = $condition['order'] ? $condition['order'] : 'points_cart.pgoods_id desc';
		$param['limit'] = $condition['limit'];
		$param['group'] = $condition['group'];
		return Db::select($param,$page);
	}
	/**
	 * 礼品购物车信息单条
	 *
	 * @param array $condition 条件数组
	 * @param array $field   查询字段
	 */
	public function getPointCartInfo($condition,$field='*'){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$array			= array();
		$array['table']	= 'points_cart';
		$array['where']	= $condition_str;
		$array['field']	= $field;
		$info		= Db::select($array);
		return $info[0];
	}
	/**
	 * 礼品购物车礼品数量
	 *
	 * @param array $condition 条件数组
	 * @param array $field   查询字段
	 */
	public function countPointCart($condition) {
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$array			= array();
		$array['table']	= 'points_cart';
		$array['where']	= $condition_str;
		$array['field']	= "count(pcart_id) as count";
		$cart_goods_num = Db::select($array);
		return $cart_goods_num[0]['count'];
	}
	
	/**
	 * 删除礼品购物车信息按照购物车编号
	 * @param	mixed $pc_id 删除记录编号
	 */
	public function dropPointCartById($pc_id){
		if(empty($pc_id)) {
			return false;
		}
		$condition_str = ' 1=1 ';
		if (is_array($pc_id) && count($pc_id)>0){
			$pc_idStr = implode(',',$pc_id);
			$condition_str .= " and	pcart_id in({$pc_idStr}) ";
		}else {
			$condition_str .= " and pcart_id = '{$pc_id}' ";
		}
		$result = Db::delete('points_cart',$condition_str);		
		return $result;
	}
	/**
	 * 删除特定条件礼品购物车信息
	 * @param	mixed $pc_id 删除记录编号
	 */
	public function dropPointCart($condition){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result = Db::delete('points_cart',$condition_str);		
		return $result;
	}
	/**
	 * 积分礼品购物车信息修改
	 *
	 * @param	array $param 修改信息数组
	 * @param	array $condition 条件数组 
	 */
	public function updatePointCart($param,$condition) {
		if(empty($param)) {
			return false;
		}
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result	= Db::update('points_cart',$param,$condition_str);
		return $result;
	}
	/**
	 * 将条件数组组合为SQL语句的条件部分
	 *
	 * @param	array $condition_array
	 * @return	string
	 */
	private function getCondition($condition_array){
		$condition_sql = '';
		//礼品购物车编号
		if ($condition_array['pcart_id']) {
			$condition_sql	.= " and `points_cart`.pcart_id = '{$condition_array['pcart_id']}'";
		}
		//会员编号
		if ($condition_array['pmember_id']) {
			$condition_sql	.= " and pmember_id = '{$condition_array['pmember_id']}'";
		}
		//积分礼品记录编号
		if ($condition_array['pgoods_id']) {
			$condition_sql	.= " and `points_cart`.pgoods_id = '{$condition_array['pgoods_id']}'";
		}
		return $condition_sql;
	}
}