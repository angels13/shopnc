<?php
/**
 * 咨询管理
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
class consultModel extends Model{
    public function __construct() {
        parent::__construct('consult');
    }
    
    /**
     * 咨询数量
     * 
     * @param array $condition
     * @return int
     */
    public function getConsultCount($condition) {
        return $this->where($condition)->count();
    }
    
	/**
	 * 添加咨询
	 * @param unknown_type $input
	 */
	public function addConsult($input){
		if(empty($input)) {
			return false;
		}
		$consult	= array();
		$consult['goods_id']		= $input['goods_id'];
		$consult['cgoods_name']		= $input['cgoods_name'];
		$consult['member_id']		= $input['member_id'];
		$consult['cmember_name']		= $input['cmember_name'];
		$consult['store_id']		= $input['store_id'];
		$consult['email']			= trim($input['email']);
		$consult['consult_content']	= trim($input['consult_content']);
		$consult['consult_addtime']	= time();
		$consult['isanonymous']	= $input['isanonymous'];
		$result	= Db::insert('consult',$consult);
		if($result) {
			return $result;
		} else {
			return false;
		}
	}
	/**
	 * 根据编号查询咨询
	 * 
	 * @param unknown_type $id
	 */
	public function getOneById($id){
		$param	= array();
		$param['table']	= 'consult';
		$param['field']	= 'consult_id';
		$param['value']	= $id;
		$result	= Db::getRow($param);
		return $result;
	}
	/**
	 * 查询咨询
	 * 
	 * @param unknown_type $condition 查询条件数组
	 * @param unknown_type $obj_page 分页对象
	 * @param unknown_type $type 查询范围
	 * @param unknown_type $ctype 咨询类型
	 */
	public function getConsultList($condition,$obj_page='',$type="simple"){
		$condition_str = $this->getCondition($condition);
		$param = array();
		$param['where'] = $condition_str;
		switch($type){
			case 'simple':
				$param['table'] 	= 'consult';
				break;
			case 'admin'://后台查看
				$param['field']		= 'consult.*,store.store_name,store.store_id';
				$param['table']		= 'consult,store';
				$param['join_on']	= array('consult.store_id=store.store_id');
				$param['join_type']	= 'LEFT JOIN';
				break;
		}
		$param['order']	= $condition['order']?$condition['order']:'consult.consult_addtime DESC';
		$consult_list = Db::select($param,$obj_page);
		return $consult_list;
	}
	/**
	 * 删除咨询
	 * 
	 * @param unknown_type $id
	 */
	public function dropConsult($id,$store_id=0){
		$condition_sql = "where consult_id in ({$id})";
		if($store_id > 0) $condition_sql .= " and store_id= '{$store_id}'";
		return Db::delete('consult',$condition_sql);
	}
	/**
	 * 回复咨询
	 * 
	 * @param unknown_type $input
	 */
	public function replyConsult($input,$condtion_arr){
		$condition_str = $this->getCondition($condtion_arr);
		$input['consult_reply_time']	= time();
		return Db::update('consult',$input,$condition_str);
	}
	/**
	 * 总数
	 *
	 */
	public function getCount($condition) {
		$condition_str	= $this->getCondition($condition);
		$count	= Db::getCount('consult',$condition_str);
		return $count;
	}
	/**
	 * 构造查询条件
	 * 
	 * @param unknown_type $condition_array
	 */
	private function getCondition($condition_array){
		$condition_sql = '';
		if($condition_array['member_id'] != '') {
			$condition_sql	.= " and consult.member_id= '{$condition_array['member_id']}'";
		}
		if($condition_array['store_id'] != '') {
			$condition_sql	.= " and consult.store_id= '{$condition_array['store_id']}'";
		}
		if($condition_array['goods_id'] != '') {
			$condition_sql	.= " and consult.goods_id= '{$condition_array['goods_id']}'";
		}
		if($condition_array['type'] != ''){
			if($condition_array['type'] == 'to_reply'){
				$condition_sql	.= " and (consult.consult_reply is NULL or consult.consult_reply='')";
			}
			if($condition_array['type'] == 'replied'){
				$condition_sql	.= " and consult.consult_reply <> ''";
			}
		}
		if($condition_array['consult_id'] != '') {
			$condition_sql	.= " and consult.consult_id= '{$condition_array['consult_id']}'";
		}
		if($condition_array['member_name'] != ''){
			$condition_sql	.= " and consult.cmember_name like '%".$condition_array['member_name']."%'";
		}
		if($condition_array['consult_content'] != ''){
			$condition_sql	.= " and consult.consult_content like '%".$condition_array['consult_content']."%'";
		}
		return $condition_sql;
	}
}