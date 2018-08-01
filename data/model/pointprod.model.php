<?php
/**
 * 积分礼品管理
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class pointprodModel extends Model {
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 礼品保存
	 *
	 * @param	array $param 商品资料
	 */
	public function addPointGoods($param) {
		if(empty($param)) {
			return false;
		}
		$result	= Db::insert('points_goods',$param);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 礼品信息列表
	 *
	 * @param array $condition 条件数组
	 * @param array $page   分页
	 * @param array $field   查询字段
	 * @param array $page   分页  
	 */
	public function getPointProdList($condition,$page='',$field='*'){
		$condition_str	= $this->getCondition($condition);
		$param	= array();
		$param['table']	= 'points_goods';
		$param['where']	= $condition_str;
		$param['field'] = $field;
		$param['order'] = $condition['order'] ? $condition['order'] : 'points_goods.pgoods_id desc';
		$param['limit'] = $condition['limit'];
		$param['group'] = $condition['group'];
		return Db::select($param,$page);
	}
	/**
	 * 礼品信息列表
	 *
	 * @param array $condition 条件数组
	 * @param array $page   分页
	 * @param array $field   查询字段
	 * @param array $page   分页  
	 */
	public function getPointProdListNew($field='*',$where='',$order='',$limit='',$page=''){
		if (empty($order)){
			$order = 'pgoods_sort asc';
		}
		$list = $this->table('points_goods')->field($field)->where($where)->order($order)->limit($limit)->page($page)->select();
		if (is_array($list) && count($list)>0){
			foreach ($list as $k=>$v){
				$v['pgoods_image'] = pointprodThumb($v['pgoods_image'], 'small');
				$v['ex_state'] = $this->getPointProdExstate($v);
				$list[$k] = $v;
			}
		}
		return $list;
	}
	/**
	 * 礼品信息单条
	 *
	 * @param array $condition 条件数组
	 * @param array $field   查询字段
	 */
	public function getPointProdInfo($condition,$field='*'){
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$array			= array();
		$array['table']	= 'points_goods';
		$array['where']	= $condition_str;
		$array['field']	= $field;
		$prod_info		= Db::select($array);
		return $prod_info[0];
	}
	/**
	 * 礼品信息单条
	 *
	 * @param array $condition 条件数组
	 * @param array $field   查询字段
	 */
	public function getPointProdInfoNew($where = '',$field='*'){
		$prodinfo = $this->table('points_goods')->where($where)->find();
		if (!empty($prodinfo)){
			$prodinfo['pgoods_image_small'] = pointprodThumb($prodinfo['pgoods_image'], 'small');
			$prodinfo['pgoods_image'] = pointprodThumb($prodinfo['pgoods_image']);
			$prodinfo['ex_state'] = $this->getPointProdExstate($prodinfo);
		}
		return $prodinfo;
	}
	/**
	 * 获得礼品兑换状态
	 * @param array $condition 礼品数组
	 * return array $field   查询字段
	 */
	public function getPointProdExstate($prodinfo){
		$datetime = time();
		$ex_state = 'end';//兑换按钮的可用状态
		if ($prodinfo['pgoods_islimittime'] == 1){
			//即将开始
			if ($prodinfo['pgoods_starttime']>$datetime && $prodinfo['pgoods_storage']>0){
				$ex_state = 'willbe';
			}
			//时间进行中
			if ($prodinfo['pgoods_starttime'] <= $datetime && $datetime < $prodinfo['pgoods_endtime'] && $prodinfo['pgoods_storage']>0){
				$ex_state = 'going';
			}
		}else {
			if ($prodinfo['pgoods_storage']>0){
				$ex_state = 'going';
			}
		}
		return $ex_state;
	}
	/**
	 * 获得礼品可兑换数量
	 * @param array $condition 礼品数组
	 * return array $field   查询字段
	 */
	public function getPointProdExnum($prodinfo,$quantity){
		if ($quantity <= 0){
			$quantity = 1;
		}
		if ($prodinfo['pgoods_islimit'] == 1 && $prodinfo['pgoods_limitnum'] < $quantity ){
			//如果兑换数量大于限兑数量，则兑换数量为限兑数量
			$quantity = $prodinfo['pgoods_limitnum'];
		}
		if ($prodinfo['pgoods_storage'] < $quantity){
			//如果兑换数量大于库存，则兑换数量为库存数量
			$quantity = $prodinfo['pgoods_storage'];
		}
		return $quantity;
	}
	/**
	 * 删除礼品信息
	 * @param	mixed $ztc_id 删除申请记录编号
	 */
	public function dropPointProdById($pg_id){
		if(empty($pg_id)) {
			return false;
		}
		$condition_str = ' 1=1 ';
		if (is_array($pg_id) && count($pg_id)>0){
			$pg_idStr = implode(',',$pg_id);
			$condition_str .= " and	pgoods_id in({$pg_idStr}) ";
		}else {
			$condition_str .= " and pgoods_id = '{$pg_id}' ";
		}
		$result = Db::delete('points_goods',$condition_str);
		//删除积分礼品下的图片信息
		if ($result){
			//删除积分礼品下的图片信息
			$upload_model = Model('upload');
			if (is_array($pg_id) && count($pg_id)>0){
				$pg_idStr = implode(',',$pg_id);
				$upload_list = $upload_model->getUploadList(array('upload_type_in' =>'5,6','item_id_in'=>$pg_idStr));
			}else {
				$upload_list = $upload_model->getUploadList(array('upload_type_in' =>'5,6','item_id'=>$pg_id));
			}			
			if (is_array($upload_list) && count($upload_list)>0){
				$upload_idarr = array();
				foreach ($upload_list as $v){
					@unlink(BASE_UPLOAD_PATH.DS.ATTACH_POINTPROD.DS.$v['file_name']);
					@unlink(BASE_UPLOAD_PATH.DS.ATTACH_POINTPROD.DS.$v['file_thumb']);
					$upload_idarr[] = $v['upload_id'];
				}
				//删除图片
				$upload_model->dropUploadById($upload_idarr);
			}
		}
		return $result;
	}
	/**
	 * 积分礼品信息修改
	 *
	 * @param	array $param 修改信息数组
	 * @param	int $pg_id 团购商品id
	 */
	public function updatePointProd($param,$condition) {
		if(empty($param)) {
			return false;
		}
		//得到条件语句
		$condition_str	= $this->getCondition($condition);
		$result	= Db::update('points_goods',$param,$condition_str);
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
		//积分礼品名称
		if ($condition_array['pgoods_name_like']) {
			$condition_sql	.= " and `points_goods`.pgoods_name like '%{$condition_array['pgoods_name_like']}%'";
		}
		//状态搜索
		if ($condition_array['pg_liststate']) {
			switch ($condition_array['pg_liststate']){
				case 'show':
					$condition_sql	.= " and `points_goods`.pgoods_show = 1 ";
					break;
				case 'nshow':
					$condition_sql	.= " and `points_goods`.pgoods_show = 0 ";
					break;
				case 'commend':
					$condition_sql	.= " and `points_goods`.pgoods_commend = 1 ";
					break;
				case 'forbid':
					$condition_sql	.= " and `points_goods`.pgoods_state = 1 ";
					break;
			}
		}
		//积分礼品记录编号
		if (isset($condition_array['pgoods_id_in'])) {
			if ($condition_array['pgoods_id_in'] == ''){
				$condition_sql	.= " and `points_goods`.pgoods_id in('') ";
			}else {
				$condition_sql	.= " and `points_goods`.pgoods_id in({$condition_array['pgoods_id_in']})";
			}
		}
		//积分礼品记录编号
		if (isset($condition_array['pgoods_id'])) {
			$condition_sql	.= " and `points_goods`.pgoods_id = '{$condition_array['pgoods_id']}'";
		}
		//上架状态
		if (isset($condition_array['pgoods_show'])) {
			$condition_sql	.= " and `points_goods`.pgoods_show = '{$condition_array['pgoods_show']}'";
		}
		//禁售状态
		if (isset($condition_array['pgoods_state'])) {
			$condition_sql	.= " and `points_goods`.pgoods_state = '{$condition_array['pgoods_state']}'";
		}
		//推荐状态
		if (isset($condition_array['pgoods_commend'])) {
			$condition_sql	.= " and `points_goods`.pgoods_commend = '{$condition_array['pgoods_commend']}'";
		}
		return $condition_sql;
	}
}
