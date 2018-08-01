<?php
/**
 * 广告模型类
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

class advModel{
	/**
	 * 新增广告位
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function ap_add($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::insert('adv_position',$tmp);
			return $result;
		}else {
			return false;
		}
	}
   /**
	 * 新增广告
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function adv_add($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::insert('adv',$tmp);
			return $result;
		}else {
			return false;
		}
	}
   /**
	 * 新增一条广告统计记录信息
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function adv_click_add($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::insert('adv_click',$tmp);
			return $result;
		}else {
			return false;
		}
	}
	/**
	 * 删除一条广告
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function adv_del($adv_id){
		$where  = "where adv_id = '$adv_id'";
		$result = Db::delete("adv",$where);
		return $result;
	}
	/**
	 * 删除一个广告位
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function ap_del($ap_id){
		$where  = "where ap_id = '$ap_id'";
		$result = Db::delete("adv_position",$where);
		return $result;
	}
	/**
	 * 获取广告位列表
	 *
	 * @param array $condition 查询条件
	 * @param obj $page 分页对象
	 * @return array 二维数组
	 */
	public function getApList($condition=array(), $page='', $orderby=''){
		$param	= array();
		$param['table']	= 'adv_position';
		$param['where']	= $this->getCondition($condition);
	    if($orderby == ''){
			$param['order'] = 'ap_id desc';
		}else{
			$param['order'] = $orderby;
		}
		return Db::select($param,$page);
	}
	/**
	 * 根据条件查询多条记录
	 *
	 * @param array $condition 查询条件
	 * @param obj $page 分页对象
	 * @return array 二维数组
	 */
	public function getList($condition=array(), $page='', $limit='', $orderby=''){
		$param	= array();
		$param['table']	= 'adv';
		$param['field'] = $condition['field']?$condition['field']:'*';
		$param['where']	= $this->getCondition($condition);
		if($orderby == ''){
			$param['order'] = 'slide_sort, adv_id desc';
		}else{
			$param['order'] = $orderby;
		}
		$param['limit'] = $limit;
		return Db::select($param,$page);
	}
	/**
	 * 根据id查询一条记录
	 *
	 * @param int $id 广告id
	 * @return array 一维数组
	 */
	public function getOneById($id){
		$param	= array();
		$param['table']	= 'adv';
		$param['field']	= 'adv_id';
		$param['value']	= $id;
		return Db::getRow($param);
	}

	/**
	 * 更新记录
	 *
	 * @param array $param 更新内容
	 * @return bool
	 */
	public function update($param){
		return Db::update('adv',$param,"adv_id='{$param['adv_id']}'");
	}
    /**
	 * 更新广告位记录
	 *
	 * @param array $param 更新内容
	 * @return bool
	 */
	public function ap_update($param){
		return Db::update('adv_position',$param,"ap_id='{$param['ap_id']}'");
	}
   /**
	 * 根据adv_id,year,month查询一条广告点击率记录
	 *
	 * @param int $id 广告id,年份,月份
	 * @return array 一维数组
	 */
	public function getOneClickById($id,$year,$month){
		$param	= array();
		$param['table']	= 'adv_click';
		$param['field']['0'] = 'adv_id';
		$param['value']['0'] = $id;
		$param['field']['1'] = 'click_year';
		$param['value']['1'] = $year;
		$param['field']['2'] = 'click_month';
		$param['value']['2'] = $month;
		return Db::getRow($param);
	}
	/**
	 * 获取指定adv_id、指定年份的广告点击记录
	 *
	 * @param array $condition 查询条件
	 * @return array 一维数组
	 */
	public function getclickinfo($condition = array()){
		$param	= array();
		$param['table']	= 'adv_click';
		$param['where']	= $this->getCondition($condition);
		return Db::select($param);
	}
	/**
	 * 更新广告点击率表
	 * @param array $param 更新内容
	 * @return bool
	 */
	public function adv_click_update($param){
		return Db::update('adv_click',$param,"adv_id='{$param['adv_id']}' and click_year='{$param['click_year']}' and click_month='{$param['click_month']}'");
	}
	/**
	 * 构造查询条件
	 *
	 * @param array $condition
	 * @return string
	 */
	private function getCondition($condition = array()){
		$return	= '';
		$time   = time();
		if($condition['adv_type'] != ''){
			$return	.= " and adv_type='".$condition['adv_type']."'";
		}
		if($condition['adv_code'] != ''){
			$return	.= " and adv_code='".$condition['adv_code']."'";
		}
		if($condition['no_adv_type'] != ''){
			$return	.= " and adv_type!='".$condition['no_adv_type']."'";
		}
		if ($condition['adv_state'] != '') {
			$return .= " and adv_state='".$condition['adv_state']."'";
		}
	    if ($condition['ap_id'] != '') {
			$return .= " and ap_id='".$condition['ap_id']."'";
		}
	    if ($condition['adv_id'] != '') {
			$return .= " and adv_id='".$condition['adv_id']."'";
		}
		if ($condition['adv_end_date'] == 'over'){
			$return .= " and adv_end_date<'".$time."'";
		}
	    if ($condition['adv_end_date'] == 'notover'){
			$return .= " and adv_end_date>'".$time."'";
		}
	    if ($condition['ap_name'] != ''){
			$return .= " and ap_name like '%".$condition['ap_name']."%'";
		}
	    if ($condition['adv_title'] != ''){
			$return .= " and adv_title like '%".$condition['adv_title']."%'";
		}
	    if ($condition['add_time_from'] != ''){
			$return .= " and adv_start_date > '{$condition['add_time_from']}'";
		}
	    if ($condition['add_time_to'] != ''){
			$return .= " and adv_end_date < '{$condition['add_time_to']}'";
		}
		if ($condition['member_name'] != ''){
			$return .= " and member_name ='".$condition['member_name']."'";
		}
		if($condition['click_year'] != ''){
			$return .= " and click_year ='".$condition['click_year']."'";
		}
	    if($condition['is_allow'] != ''){
			$return .= " and is_allow = '".$condition['is_allow']."' ";
		}
	    if($condition['buy_style'] != ''){
			$return .= " and buy_style = '".$condition['buy_style']."' ";
		}
	    if($condition['adv_start_date'] == 'nowshow'){
			$return .= " and adv_start_date <'".$time."'";
		}
	    if($condition['member_id'] != ''){
			$return .= " and member_id = '".$condition['member_id']."'";
		}
	    if($condition['is_use'] != ''){
			$return .= " and is_use = '".$condition['is_use']."' ";
		}
	    if ($condition['adv_buy_id'] != '') {
			$return .= " and ap_id not in (".$condition['adv_buy_id'].")";
		}
		return $return;
	}

	public function delapcache($id){
		if (!is_numeric($id)) return ;
		$filename = BASE_DATA_PATH.DS.'cache/adv/'.$id.'.php';
		if(file_exists($filename)) @unlink($filename);
		return true;
	}

	/**
	 * 生成广告位缓存
	 *
	 * @param int $ap_id
	 */
	public function makeApCache($ap_id = null){
		if (empty($ap_id)) return '';
		$model = model();
		$ap_info = $model->table('adv_position')->where(array('ap_id'=>$ap_id))->find();
		$ap_info['adv_list'] = $model->table('adv')->where(array('ap_id'=>$ap_id))->order('slide_sort, adv_id desc')->select();
		write_file(BASE_DATA_PATH.'/cache/adv/'.$ap_id.'.php',$ap_info);
	}

	/**
	 * 广告
	 *
	 * @return array
	 */
	public function makeApAllCache(){
		delCacheFile('adv');
		$model = model();
		$ap_list =$model->table('adv_position')->where(true)->select();
		$adv_list =$model->table('adv')->where(true)->order('slide_sort, adv_id desc')->select();
		$array = array();
		foreach ((array)$ap_list as $v) {
			foreach ((array)$adv_list as $xv) {
				if ($v['ap_id'] == $xv['ap_id']){
					$v['adv_list'][] = $xv;
				}
			}
			write_file(BASE_DATA_PATH.'/cache/adv/'.$v['ap_id'].'.php',$v);
		}
	}
}