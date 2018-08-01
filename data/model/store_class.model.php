<?php
/**
 * 店铺类别模型
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

class store_classModel{
	/**
	 * 类别列表
	 *
	 * @param array $condition 检索条件
	 * @return array 数组结构的返回结果
	 */
	public function getClassList($condition){
		$condition_str = $this->_condition($condition);
		$param = array();
		$param['table'] = 'store_class';
		$param['order'] = $condition['order'] ? $condition['order'] : 'sc_parent_id asc,sc_sort asc,sc_id asc';
		$param['where'] = $condition_str;
		$result = Db::select($param);
		return $result;
	}
	
	/**
	 * 构造检索条件
	 *
	 * @param int $id 记录ID
	 * @return string 字符串类型的返回结果
	 */
	private function _condition($condition){
		$condition_str = '';
		
		if ($condition['sc_parent_id'] != ''){
			$condition_str .= " and sc_parent_id = '". intval($condition['sc_parent_id']) ."'";
		}
		if ($condition['no_sc_id'] != ''){
			$condition_str .= " and sc_id != '". intval($condition['no_sc_id']) ."'";
		}
		if ($condition['sc_name'] != ''){
			$condition_str .= " and sc_name = '". $condition['sc_name'] ."'";
		}
		
		return $condition_str;
	}
	
	/**
	 * 取单个分类的内容
	 *
	 * @param int $id 分类ID
	 * @return array 数组类型的返回结果
	 */
	public function getOneClass($id){
		if (intval($id) > 0){
			$param = array();
			$param['table'] = 'store_class';
			$param['field'] = 'sc_id';
			$param['value'] = intval($id);
			$result = Db::getRow($param);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 新增
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function add($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::insert('store_class',$tmp);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 更新信息
	 *
	 * @param array $param 更新数据
	 * @return bool 布尔类型的返回结果
	 */
	public function update($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$where = " sc_id = '". $param['sc_id'] ."'";
			$result = Db::update('store_class',$tmp,$where);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 删除分类
	 *
	 * @param int $id 记录ID
	 * @return bool 布尔类型的返回结果
	 */
	public function del($id){
		if (intval($id) > 0){
			$where = " sc_id = '". intval($id) ."'";
			$result = Db::delete('store_class',$where);
			return $result;
		}else {
			return false;
		}
	}

	/**
	 * 取分类列表
	 *
	 * @return array 数组类型的返回结果
	 */
	public function getTreeList(){
		$show_class = array();
		$class_list = $this->getClassList(array('order'=>'sc_parent_id asc,sc_sort asc,sc_id asc'));
		if(is_array($class_list) && !empty($class_list)) {
			foreach ($class_list as $val) {
				if($val['sc_parent_id'] == 0) {
					$show_class[$val['sc_id']] = $val;
				} else {
					if(isset($show_class[$val['sc_parent_id']])){
						$show_class[$val['sc_parent_id']]['child'][] = $val;
					}
				}
			}
			unset($class_list);
		}
		return $show_class;
	}

	/**
	 * 取分类列表，按照深度归类
	 *
	 * @param int $show_deep 显示深度
	 * @return array 数组类型的返回结果
	 */
	public function getTreeClassList($show_deep='2', $condition = array()){
		$class_list = $this->getClassList($condition);
		$show_deep = intval($show_deep);
		$result = array();
		if(is_array($class_list) && !empty($class_list)) {
			$result = $this->_getTreeClassList($show_deep,$class_list);
		}
		return $result;
	}

	/**
	 * 递归 整理分类
	 *
	 * @param int $show_deep 显示深度
	 * @param array $class_list 类别内容集合
	 * @param int $deep 深度
	 * @param int $parent_id 父类编号
	 * @param int $i 上次循环编号
	 * @return array $show_class 返回数组形式的查询结果
	 */
	private function _getTreeClassList($show_deep,$class_list,$deep=1,$parent_id=0,$i=0){
		static $show_class = array();//树状的平行数组
		if(is_array($class_list) && !empty($class_list)) {
			$size = count($class_list);
			if($i == 0) $show_class = array();//从0开始时清空数组，防止多次调用后出现重复
			for ($i;$i < $size;$i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
				$val = $class_list[$i];
				$sc_id = $val['sc_id'];
				$sc_parent_id	= $val['sc_parent_id'];
				if($sc_parent_id == $parent_id) {
					$val['deep'] = $deep;
					$show_class[] = $val;
					if($deep < $show_deep && $deep < 2) {//本次深度小于显示深度时执行，避免取出的数据无用
						$this->_getTreeClassList($show_deep,$class_list,$deep+1,$sc_id,$i+1);
					}
				}
				if($sc_parent_id > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
			}
		}
		return $show_class;
	}
	
	/**
	 * 取指定分类ID下的所有子类
	 *
	 * @param int/array $parent_id 父ID 可以单一可以为数组
	 * @return array $result 返回数组形式的查询结果
	 */
	public function getChildClass($parent_id){
		$all_class = $this->getClassList(array('order'=>'sc_parent_id asc,sc_sort asc,sc_id asc'));
		if (is_array($all_class)){
			if (!is_array($parent_id)){
				$parent_id = array($parent_id);
			}
			$result = array();
			foreach ($all_class as $k => $v){
				$sc_id	= $v['sc_id'];//返回的结果包括父类
				$sc_parent_id	= $v['sc_parent_id'];
				if (in_array($sc_id,$parent_id) || in_array($sc_parent_id,$parent_id)){
					$result[] = $v;
				}
			}
			return $result;
		}else {
			return false;
		}
	}
	/**
	 * 获取店铺分类下的子分类或者包含其自身
	 * @param int 店铺分类
	 * @return mixed
	 */
	public function getChildAndSelfClass($sc_id){
		$sc_id_arr = array();
		$sclasslist = $this->getClassList(array('sc_parent_id'=>$sc_id));
		if (is_array($sclasslist) && count($sclasslist)>0){
			foreach ($sclasslist as $v){
				$sc_id_arr[] = $v['sc_id'];
			}
		}
		if (is_array($sc_id_arr) && count($sc_id_arr)>0){
			$sc_id_arr[] = $sc_id;//加上一级分类本身
			return $sc_id_arr; 
		}else{
			return $sc_id;
		}
	}
}