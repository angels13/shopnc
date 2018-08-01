<?php 
/**
 * 投诉模型 
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
class complainModel extends model{
    public function __construct() {
        parent::__construct('complain');
    }
    
    /**
     * 投诉数量
     * @param array $condition
     * @return int
     */
    public function getComplainCount($condition) {
        return $this->where($condition)->count();
    }

	/*
	 * 构造条件
	 */
	private function getCondition($condition){
		$condition_str = '' ;
        if(!empty($condition['complain_id'])) {
            $condition_str.= " and  complain_id = '{$condition['complain_id']}'";
        }
        if(!empty($condition['complain_state'])) {
            $condition_str.= " and  complain_state = '{$condition['complain_state']}'";
        }
        if(!empty($condition['accuser_id'])) {
            $condition_str.= " and  accuser_id = '{$condition['accuser_id']}'";
        }
        if(!empty($condition['accused_id'])) {
            $condition_str.= " and  accused_id = '{$condition['accused_id']}'";
        }
        if(!empty($condition['order_id'])) {
            $condition_str.= " and  order_id = '{$condition['order_id']}'";
        }
        if(!empty($condition['accused_progressing'])) {
            $condition_str.= " and complain_state > 10 and complain_state < 90 ";
        }
        if(!empty($condition['progressing'])) {
            $condition_str.= " and  complain_state < 90 ";
        }
        if(!empty($condition['finish'])) {
            $condition_str.= " and  complain_state = 99 ";
        }
        if(!empty($condition['accused_finish'])) {
            $condition_str.= " and  complain_state = 99 and complain_active = 2 ";
        }
        if(!empty($condition['accused_all'])) {
            $condition_str.= " and  complain_active = 2 ";
        }
        if(!empty($condition['complain_accuser'])) {
            $condition_str.= " and  accuser_name like '%".$condition['complain_accuser']."%'";
        }
        if(!empty($condition['complain_accused'])) {
            $condition_str.= " and  accused_name like '%".$condition['complain_accused']."%'";
        }
        if(!empty($condition['complain_subject_content'])) {
            $condition_str.= " and  complain_subject_content like '%".$condition['complain_subject_content']."%'";
        }
        if(!empty($condition['complain_datetime_start'])) {
            $condition_str.= " and  complain_datetime > '{$condition['complain_datetime_start']}'";
        }
        if(!empty($condition['complain_datetime_end'])) {
            $end = $condition['complain_datetime_end'] + 86400;
            $condition_str.= " and  complain_datetime < '$end'";
        }

		return $condition_str;
    }

	/*
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
	public function saveComplain($param){
	
		return Db::insert('complain',$param) ;
	
	}
	
	/*
	 * 更新
	 * @param array $update_array
	 * @param array $where_array
	 * @return bool
	 */
	public function updateComplain($update_array, $where_array){
	
		$where = $this->getCondition($where_array) ;
		return Db::update('complain',$update_array,$where) ;
    
    }
	
	/*
	 * 删除
	 * @param array $param
	 * @return bool
	 */
	public function dropComplain($param){

		$where = $this->getCondition($param) ;
		return Db::delete('complain', $where) ;
	
	}

	/*
	 *  获得列表
	 *  @param array $condition
	 *  @param obj $page 	//分页对象
	 *  @return array
	 */
	public function getComplain($condition='',$page='') {

        $param = array() ;
        $param['table'] = 'complain' ;
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order']: ' complain_id desc ';
        return Db::select($param,$page);
	}

	/*
	 *  获得列表
	 *  @param array $condition
	 *  @param obj $page 	//分页对象
	 *  @return array
	 */
	public function isExist($condition='') {

        $param = array() ;
        $param['table'] = 'complain' ;
        $param['where'] = $this->getCondition($condition);
        $list = Db::select($param);
        if(empty($list)) {
            return false;
        }
        else {
            return true;
        }
	}

    /*
     *   根据id获取投诉详细信息
     */
    public function getoneComplain($complain_id) {
        
        $param = array() ;
    	$param['table'] = 'complain';
    	$param['field'] = 'complain_id' ;
    	$param['value'] = intval($complain_id);
    	return Db::getRow($param) ;

    }
	/**
	 * 总数
	 *
	 */
	public function getCount($condition) {
		$condition_str	= $this->getCondition($condition);
		$count	= Db::getCount('complain',$condition_str);
		return $count;
	}

}
