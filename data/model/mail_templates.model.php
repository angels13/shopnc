<?php
/**
 * 文件的简短描述
 *
 * 文件的详细描述
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class mail_templatesModel{
	/**
	 * 模板列表
	 *
	 * @param array $condition 检索条件
	 * @return array 数组形式的返回结果
	 */
	public function getTemplatesList($condition){
		$condition_str = $this->_condition($condition);
		$param = array();
		$param['table'] = 'mail_msg_temlates';
		$param['where'] = $condition_str;
		$result = Db::select($param);
		return $result;
	}
	
	/**
	 * 构造检索条件
	 *
	 * @param array $condition 检索条件
	 * @return string 字符串形式的返回结果
	 */
	private function _condition($condition){
		$condition_str = '';
		
		if ($condition['type'] != ''){
			$condition_str .= " and type = '". $condition['type'] ."'";
		}
		if ($condition['code'] != ''){
			$condition_str .= " and code = '". $condition['code'] ."'";
		}
		return $condition_str;
	}
	
	/**
	 * 取得模板内容
	 *
	 * @param string $code 模板编号
	 * @return array $rs_row 返回数组形式的查询结果
	 */
	public function getOneTemplates($code){
		if (!empty($code)){
			$param = array();
			$param['table'] = 'mail_msg_temlates';
			$param['field'] = 'code';
			$param['value'] = $code;
			$result = Db::getRow($param);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 更新模板内容
	 *
	 * @param array $param 更新参数
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
			$where = " code = '". $param['code'] ."'";
			$result = Db::update('mail_msg_temlates',$tmp,$where);
			return $result;
		}else {
			return false;
		}
	}
	
}