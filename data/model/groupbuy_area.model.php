<?php
/**
 * 团购地区模型
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
class groupbuy_areaModel{

    const TABLE_NAME = 'groupbuy_area';
    const PK = 'area_id';

	/**
	 * 构造检索条件
	 *
	 * @param array $condition 检索条件
	 * @return string 
	 */
	private function getCondition($condition){
		$condition_str = '';
		if (!empty($condition['area_parent_id'])){
			$condition_str .= "and area_parent_id = '".$condition['area_parent_id']."'";
		}
		if (!empty($condition['area_id'])){
			$condition_str .= "and area_id = '".$condition['area_id']."'";
		}
		if (!empty($condition['in_area_id'])){
			$condition_str .= "and area_id in (". $condition['in_area_id'] .")";
		}
		return $condition_str;
	}

	/**
	 * 读取列表 
	 *
	 */
	public function getList($condition='',$page=''){

        $param = array() ;
        $param['table'] = self::TABLE_NAME ;
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order']: ' '.self::PK.' desc ';
        return Db::select($param,$page);
	}

    /**
     * 读取列表 
     *
     */
    public function getTreeList($condition='',$page='',$max_deep=2){

        $area_list = $this->getList($condition,$page);
        $tree_list = array();
        if(is_array($area_list)) {
            $tree_list = $this->_getTreeList($area_list,0,0,$max_deep);
        }
        return $tree_list;
    }

    //按照顺序显示树形结构
    private function _getTreeList($list,$parent_id,$deep=0,$max_deep) {

        $result = array();
        foreach($list as $node) {
            if($node['area_parent_id'] == $parent_id) {
                if($deep <= $max_deep) {
                    $temp = $this->_getTreeList($list,$node['area_id'],$deep+1,$max_deep);
                    //标记是否有子节点
                    if(!empty($temp)) {
                        $node['have_child'] = 1;
                    }
                    else {
                        $node['have_child'] = 0;
                    }
                    //标记是否为叶子节点
                    if($deep == $max_deep) {
                        $node['node'] = 1;
                    }
                    else {
                        $node['node'] = 0;
                    }
                    $node['deep'] = $deep;
                    $result[] = $node;
                    if(!empty($temp)) {
                        $result = array_merge($result,$temp);
                    }
                    unset($temp);
                    }
                }
            }
        return $result;
    }

    /**
     * 根据编号获取所有下级编号的数组
     *
     * @param array 
     * @return array 数组类型的返回结果
     */
    public function getAllAreaId($area_id_array) {

        $all_area_id_array = array();
        $area_list = $this->getList();
        foreach($area_id_array as $area_id) {
            $all_area_id_array[] = $area_id;
            foreach($area_list as $area) {
                if($area['area_parent_id'] == $area_id) {
                    $all_area_id_array[] = $area['area_id'];
                }
            }
        }
        return $all_area_id_array;
    }




    /**
	 * 根据编号获取单个内容
	 *
	 * @param int $groupbuy_area_id 地区ID
	 * @return array 数组类型的返回结果
	 */
	public function getOne($id){
		if (intval($id) > 0){
			$param = array();
			$param['table'] = self::TABLE_NAME;
			$param['field'] = self::PK;
			$param['value'] = intval($id);
			$result = Db::getRow($param);
			return $result;
		}else {
			return false;
		}
	}

	/*
	 *  判断是否存在 
	 *  @param array $condition
	 *  @param obj $page 	//分页对象
	 *  @return array
	 */
	public function isExist($condition='') {

        $param = array() ;
        $param['table'] = self::TABLE_NAME ;
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
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
	public function save($param){
	
		return Db::insert(self::TABLE_NAME,$param) ;
	
	}
	
	/*
	 * 更新
	 * @param array $update_array
	 * @param array $where_array
	 * @return bool
	 */
	public function update($update_array, $where_array){
	
		$where = $this->getCondition($where_array) ;
		return Db::update(self::TABLE_NAME,$update_array,$where) ;
    
    }
	
	/*
	 * 删除
	 * @param array $param
	 * @return bool
	 */
	public function drop($param){

		$where = $this->getCondition($param) ;
		return Db::delete(self::TABLE_NAME, $where) ;
	}
	
}
