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

class pointorderModel {
	private $product_sn;	//订单编号
	
	/**
	 * 生成积分兑换订单编号
	 * @return string
	 */
	public function point_snOrder() {
		$this->product_sn = 'gift'.date('Ymd').substr( implode(NULL,array_map('ord',str_split(substr(uniqid(),7,13),1))) , -8 , 8);
		return $this->product_sn;
	}
	/**
	 * 生成外部积分兑换订单
	 *
	 * @return string
	 */
	public function point_outSnOrder() {
		if($this->product_sn) {
			return $this->product_sn;
		}
	}
	/**
	 * 兑换礼品加入订单
	 *
	 * @param array	$param
	 * @return bool
	 */
	public function addPointOrder($param) {
		if(is_array($param) and !empty($param)) {
			$result = Db::insert('points_order',$param);
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 订单积分礼品添加
	 * @param array	$param	订单礼品信息
	 * @return bool
	 */
	public function addPointOrderProd($param) {
		if(is_array($param) && count($param)>0) {
			$result = Db::insert('points_ordergoods',$param);
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 订单积分礼品列表
	 * @param array	$param	订单礼品信息
	 * @return bool
	 */
	public function getPointOrderProdList($condition,$page,$field='*',$type='simple') {		
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$param	= array();
		switch($type){
			case 'all':
				$param['table']	= 'points_ordergoods,points_order';
				$param['join_type']	= 'left join';
				$param['join_on']	= array('`points_ordergoods`.point_orderid = `points_order`.point_orderid');
				break;
			case 'simple':
				$param['table']	= 'points_ordergoods';
				break;
		}
		$param['where']	= $condition_str;
		$param['field']	= $field;
		$param['order'] = $condition['order'] ? $condition['order'] : 'points_ordergoods.point_recid desc ';
		$param['limit'] = $condition['limit'];
		$param['group'] = $condition['group'];
		$prod_list	= Db::select($param,$page);
		return $prod_list;
	}
	/**
	 * 删除礼品兑换信息
	 * @param	array 删除条件
	 */
	public function dropPointOrderProd($condition){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result = Db::delete('points_ordergoods',$condition_str);
		return $result;
	}
	/**
	 * 删除礼品兑换地址信息
	 * @param	array 删除条件
	 */
	public function dropPointOrderAddress($condition){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result = Db::delete('points_orderaddress',$condition_str);
		return $result;
	}
	/**
	 * 添加积分兑换订单地址
	 * @param array	$param	订单收货地址信息
	 * @return bool
	 */
	public function addPointOrderAddress($param){
		if(is_array($param) and count($param)) {
			$result = Db::insert('points_orderaddress',$param);
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 根据兑换订单编号查询指定订单
	 *
	 * @param int $order_id 订单序号
	 * @param string $type 查询类型
	 * @return array
	 */
	public function getPointOrderInfo($condition,$type='all',$field='*'){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$param	= array();
		switch($type){
			case 'all':
				$param['table']	= 'points_order,points_orderaddress';
				$param['join_type']	= 'left join';
				$param['join_on']	= array('`points_order`.point_orderid=`points_orderaddress`.point_orderid');
				break;
			case 'simple':
				$param['table']	= 'points_order';
				break;
		}
		$param['where']	= $condition_str;
		$param['field']	= $field;
		$order_list	= Db::select($param);
		return $order_list[0];
	}
	/**
	 * 根据兑换订单编号查询指定订单
	 *
	 * @param int $order_id 订单序号
	 * @param string $type 查询类型
	 * @return array
	 */
	public function getPointOrderList($condition,$page,$type='all',$field='*'){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$param	= array();
		switch($type){
			case 'all':
				$param['table']	= 'points_order,points_orderaddress';
				$param['join_type']	= 'left join';
				$param['join_on']	= array('`points_order`.point_orderid=`points_orderaddress`.point_orderid');
				break;
			case 'simple':
				$param['table']	= 'points_order';
				break;
		}
		$param['where']	= $condition_str;
		$param['field']	= $field;
		$param['order'] = $condition['order'] ? $condition['order'] : 'points_order.point_orderid desc';
		$param['limit'] = $condition['limit'];
		$param['group'] = $condition['group'];
		$order_list	= Db::select($param,$page);
		return $order_list;
	}
	/**
	 * 积分礼品兑换订单信息修改
	 *
	 * @param	array $param 修改信息数组
	 * @param	array $condition
	 */
	public function updatePointOrder($condition,$param) {
		if(empty($param)) {
			return false;
		}
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result	= Db::update('points_order',$param,$condition_str);
		return $result;
	}
	/**
	 * 删除礼品兑换信息
	 * @param	array 删除条件
	 */
	public function dropPointOrder($condition){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result = Db::delete('points_order',$condition_str);
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
		//订单序号
		if ($condition_array['point_orderid']) {
			$condition_sql	.= " and `points_order`.point_orderid = '{$condition_array['point_orderid']}'";
		}
		//订单序号
		if ($condition_array['point_orderid_del']) {
			$condition_sql	.= " and point_orderid = '{$condition_array['point_orderid_del']}'";
		}
		//订单序号in
		if (isset($condition_array['point_orderid_in'])) {
			if ($condition_array['point_orderid_in'] == ''){
				$condition_sql	.= " and point_orderid in('') ";
			}else {
				$condition_sql	.= " and point_orderid in({$condition_array['point_orderid_in']}) ";
			}
		}
		//订单会员编号
		if ($condition_array['point_buyerid']) {
			$condition_sql	.= " and `points_order`.point_buyerid = '{$condition_array['point_buyerid']}'";
		}
		//订单运费承担方式
		if ($condition_array['point_shippingcharge']) {
			$condition_sql	.= " and `points_order`.point_shippingcharge = '{$condition_array['point_shippingcharge']}'";
		}
		//订单状态
		if ($condition_array['point_orderstate']) {
			$condition_sql	.= " and `points_order`.point_orderstate = '{$condition_array['point_orderstate']}'";
		}
		//订单状态
		if ($condition_array['point_orderstatetxt']) {
			switch ($condition_array['point_orderstatetxt']){
				case 'canceled':
					$condition_sql	.= " and `points_order`.point_orderstate = 2 ";
					break;
				case 'waitpay':
					$condition_sql	.= " and `points_order`.point_orderstate = 10 ";
					break;
				case 'waitconfirmpay':
					$condition_sql	.= " and `points_order`.point_orderstate = 11 ";
					break;
				case 'waitship':
					$condition_sql	.= " and `points_order`.point_orderstate = 20 ";
					break;
				case 'waitreceiving':
					$condition_sql	.= " and `points_order`.point_orderstate = 30 ";
					break;
				case 'finished':
					$condition_sql	.= " and `points_order`.point_orderstate = 40 ";
					break;
			}
		}
		//订单编号like
		if ($condition_array['point_ordersn']) {
			$condition_sql	.= " and `points_order`.point_ordersn = '{$condition_array['point_ordersn']}' ";
		}
		//订单编号like
		if ($condition_array['point_ordersn_like']) {
			$condition_sql	.= " and `points_order`.point_ordersn like '%{$condition_array['point_ordersn_like']}%' ";
		}
		//会员名称like
		if ($condition_array['point_buyername_like']) {
			$condition_sql	.= " and `points_order`.point_buyername like '%{$condition_array['point_buyername_like']}%' ";
		}
		//支付方式
		if ($condition_array['point_paymentcode']) {
			$condition_sql	.= " and `points_order`.point_paymentcode = '{$condition_array['point_paymentcode']}' ";
		}
		//订单状态 in
		if (isset($condition_array['point_orderstate_in'])) {
			if ($condition_array['point_orderstate_in'] == ''){
				$condition_sql	.= " and point_orderstate in ('') ";
			}else {
				$condition_sql	.= " and point_orderstate in ({$condition_array['point_orderstate_in']}) ";
			}
		}
		//兑换商品订单编号
		if ($condition_array['prod_orderid']) {
			$condition_sql	.= " and points_ordergoods.point_orderid = '{$condition_array['prod_orderid']}' ";
		}
		//兑换商品订单编号in
		if (isset($condition_array['prod_orderid_in'])) {
			if ($condition_array['prod_orderid_in'] == ''){
				$condition_sql	.= " and points_ordergoods.point_orderid in('')";
			}else{
				$condition_sql	.= " and points_ordergoods.point_orderid in({$condition_array['prod_orderid_in']})";
			}
		}
		//兑换商品订单编号删除
		if ($condition_array['prod_orderid_del']) {
			$condition_sql	.= " and point_orderid = '{$condition_array['prod_orderid_del']}' ";
		}
		//兑换商品订单编号in删除
		if (isset($condition_array['prod_orderid_in_del'])) {
			if ($condition_array['prod_orderid_in_del'] == ''){
				$condition_sql	.= " and point_orderid in('')";
			}else{
				$condition_sql	.= " and point_orderid in({$condition_array['prod_orderid_in_del']})";
			}
		}
		//兑换商品商品编号
		if ($condition_array['prod_goodsid']) {
			$condition_sql	.= " and points_ordergoods.point_goodsid = '{$condition_array['prod_goodsid']}' ";
		}
		//可取消兑换信息
		if ($condition_array['point_order_enablecancel']) {
			$condition_sql	.= " and ((points_order.point_shippingcharge = 1 and points_order.point_orderstate =10) or (points_order.point_shippingcharge = 0 and points_order.point_orderstate =20))";
		}
		//兑换商品订单编号删除
		if ($condition_array['address_orderid_del']) {
			$condition_sql	.= " and point_orderid = '{$condition_array['address_orderid_del']}' ";
		}
		//兑换商品订单编号in删除
		if (isset($condition_array['address_orderid_in_del'])) {
			if ($condition_array['address_orderid_in_del'] == ''){
				$condition_sql	.= " and point_orderid in('')";
			}else{
				$condition_sql	.= " and point_orderid in({$condition_array['address_orderid_in_del']})";
			}
		}
		return $condition_sql;
	}
}