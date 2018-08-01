<?php
/**
 * 类型管理
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

class typeModel extends Model {

    public function __construct(){
        parent::__construct('type');
    }
    
    public function getTypeBrandList($condition, $field = '*') {
        return $this->table('type_brand')->field($field)->where($condition)->select();
    }

    public function getGoodsAttrIndexList($conditoin,$page = 0, $fields = '', $order = '', $limit = '') {
        return $this->table('goods_attr_index')->where($conditoin)->order($order)->limit($limit)->page($page)->select();
    }

    /**
     * 根据类型查找规格
     * 
     * @param   array   $where  条件
     * @param   string  $field  字段
     * @param   string  $order  排序
     * @return  array   返回二位数组
     */
    public function getSpecByType($where, $field, $order = 'spec.sp_sort asc, spec.sp_id asc') {
        $result = $this->table('type_spec,spec')->field($field)->where($where)->join('inner')->on('type_spec.sp_id = spec.sp_id')->order($order)->select();
        return $result;
    }

    /**
     * 根据类型获得规格、类型、属性信息
     * 
     * @param int $type_id 类型id
     * @param int $store_id 店铺id
     * @return array 二位数组
     */
    public function getAttr($type_id, $store_id, $gc_id) {
        $spec_list = $this->typeRelatedJoinList(array('type_id' => $type_id), 'spec', 'spec.sp_id as sp_id, spec.sp_name as sp_name');
        $attr_list = $this->typeRelatedJoinList(array('attribute.type_id' => $type_id), 'attr', 'attribute.attr_id as attr_id, attribute.attr_name as attr_name, attribute_value.attr_value_id as attr_value_id, attribute_value.attr_value_name as attr_value_name');
        $brand_list = $this->typeRelatedJoinList(array('type_id' => $type_id, 'brand_apply' => 1), 'brand', 'brand.brand_id as brand_id,brand.brand_name as brand_name');
        
        // 整理数组
        $spec_json = array();
        if (is_array($spec_list) && !empty($spec_list)) {
            $array = array();
            foreach ($spec_list as $val) {
                $spec_value_list = Model('spec')->getSpecValueList(array('sp_id'=>$val['sp_id'], 'gc_id'=>$gc_id, 'store_id' => $store_id));
                $a = array();
                foreach ($spec_value_list as $v) {
                    $b = array();
                    $b['sp_value_id'] = $v['sp_value_id'];
                    $b['sp_value_name'] = $v['sp_value_name'];
                    $b['sp_value_color'] = $v['sp_value_color'];
                    $a[] = $b;
                    $spec_json[$val['sp_id']][$v['sp_value_id']]['sp_value_name'] = $v['sp_value_name'];
                    $spec_json[$val['sp_id']][$v['sp_value_id']]['sp_value_color'] = $v['sp_value_color'];
                }
                $array[$val['sp_id']]['sp_name'] = $val['sp_name'];
                $array[$val['sp_id']]['sp_format'] = $val['sp_format'];
                $array[$val['sp_id']]['value'] = $a;
            }
            $spec_list = $array;
        }
        if (is_array($attr_list) && !empty($attr_list)) {
            $array = array();
            foreach ($attr_list as $val) {
                $a = array();
                $a['attr_value_id'] = $val['attr_value_id'];
                $a['attr_value_name'] = $val['attr_value_name'];
                
                $array[$val['attr_id']]['attr_name'] = $val ['attr_name'];
                $array[$val['attr_id']]['value'][] = $a;
            }
            $attr_list = $array;
        }
        
        if (empty($brand_list)) {
            $condition = array('brand_apply' => 1);
            $brand_list = Model('brand')->getBrandList($condition);
        }
        return array($spec_json, $spec_list, $attr_list, $brand_list);
    }
    
    /**
     * 新增商品商品与属性对应
     * 
     * @param int $goods_id
     * @param int $commonid
     * @param array $param
     * @return boolean
     */
    public function addGoodsType($goods_id, $commonid, $param) {
        // 商品与属性对应
        $sa_array = array();
        $sa_array['goods_id']       = $goods_id;
        $sa_array['goods_commonid'] = $commonid;
        $sa_array['gc_id']          = $param['cate_id'];
        $sa_array['type_id']        = $param['type_id'];
        if (is_array($param['attr'])) {
            $sa_array['value'] = $param['attr'];
            $this->typeGoodsRelatedAdd($sa_array, 'goods_attr_index');
            return true;
        } else {
            return false;
        }
    }
    
    public function delGoodsAttr($conditoin) {
        return $this->table('goods_attr_index')->where($conditoin)->delete();
    }
	/**
	 * 类型列表
	 * @param array  $param 
	 * @param object $page  
	 * @param string $field 
	 */
	public function typeList($param,$page = '',$field = '*') {
		$condition_str = $this->getCondition($param);
		$array = array();
		$array['table']		= 'type';
		$array['where']		= $condition_str;
		$array['field']		= $field;
		$array['order']		= $param['order'];
		$list_type		= Db::select($array,$page);
		return $list_type;
	}
	/**
	 * 添加类型信息
	 * @param string $table 表名
	 * @param array $param 一维数组
	 * @return bool
	 */
	public function typeAdd($table,$param){
		return Db::insert($table, $param);
	}
	/**
	 * 添加对应关系信息
	 * @param string $table 表名
	 * @param array $param 一维数组
	 * @param string $id
	 * @param string $row 列名
	 * @return bool
	 */
	public function typeRelatedAdd($table, $param, $id, $row=''){
		$insert_str = '';
		if(is_array($param)){
			foreach($param as $v){
				$insert_str .= "('". $id ."', '". $v ."'),";
			}
		}else{
			$insert_str .= "('". $id ."', '". $param ."'),";
		}
		$insert_str = rtrim($insert_str,',');
		return Db::query("insert into `".DBPRE.$table."` ". $row ." values ".$insert_str);
	}
    /**
     * 添加商品与规格、属性对应关系信息
     * 
     * @param array $param
     *            一维数组
     * @param string $table
     *            表名
     * @return bool
     */
    public function typeGoodsRelatedAdd($param, $table, $type = "") {
        if (is_array ( $param ['value'] ) && ! empty ( $param ['value'] )) {
            $insert_array = array ();
            foreach ( $param ['value'] as $key => $val ) {
                if (is_array ( $val ) && ! empty ( $val )) {
                    foreach ( $val as $k => $v ) {
                        if (intval ( $k ) > 0 && $k != 'name') {
                            $insert = array ();
                            $insert ['goods_id'] = $param ['goods_id'];
                            $insert ['goods_commonid'] = $param ['goods_commonid'];
                            $insert ['gc_id'] = $param ['gc_id'];
                            $insert ['type_id'] = $param ['type_id'];
                            $insert ['attr_id'] = $key;
                            $insert ['attr_value_id'] = $k;
                            $insert_array [] = $insert;
                        }
                    }
                }
            }
            $this->table($table)->insertAll($insert_array);
        }
    }
	/**
	 * 对应关系信息列表
	 * @param string $table 表名
	 * @param array $param 一维数组
	 * @param string $id
	 * @param string $row 列名
	 * @return Array
	 */
	public function typeRelatedList($table, $param, $field = '*'){
		$condition_str = $this->getCondition($param);
		$array = array();
		$array['table']		= $table;
		$array['where']		= $condition_str;
		$array['field']		= $field;
		$array['order']		= $param['order'];
		$list_type		= Db::select($array);
		return $list_type;
	}
	/**
	 * 更新属性信息
	 * @param	array $update 更新数据
	 * @param	array $param 条件
	 * @param	string $table 表名
	 * @return	bool
	 */
	public function typeUpdate($update, $param, $table){
		$condition_str = $this->getCondition($param);
		if (empty($update)){
			return false;
		}
		if (is_array($update)){
			$tmp = array();
			foreach ($update as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::update($table,$tmp,$condition_str);
			return $result;
		}else {
			return false;
		}
	}
    /**
     * 类型与属性关联信息,多表查询
     *
     * @param array $param 条件
     * @param int $type 参数
     * @param string $field 字段
     * @param string $order 排序
     * @return boolean
     */
    public function typeRelatedJoinList($param, $type = '', $field = '*', $order = '') {
        $array = array();
        switch ($type) {
            case 'spec':
                $table = 'type_spec,spec';
                $join = 'inner';
                $on = 'type_spec.sp_id=spec.sp_id';
                $order = !empty($order) ? $order : 'spec.sp_id asc, spec.sp_sort asc';
                break;
            case 'attr':
                $table = 'attribute,attribute_value';
                $join = 'inner';
                $on = 'attribute.attr_id=attribute_value.attr_id';
                $order = !empty($order) ? $order : 'attribute.attr_sort asc, attribute_value.attr_value_sort asc, attribute_value.attr_value_id asc';
                break;
            case 'brand':
                $table = 'type_brand,brand';
                $join = 'inner';
                $on = 'type_brand.brand_id=brand.brand_id';
                $order = !empty($order) ? $order : 'brand.brand_sort asc';
                break;
        }
        $result = $this->table($table)->field($field)->join($join)->on($on)->where($param)->order($order)->select();
        return $result;
    }
	/**
	 * 类型与属性关联信息,多表查询
	 * 
	 * @param array $param 类型相关信息
	 * @param int   $type  参数
	 * @param string $field 
	 */
	public function typeRelatedJoinListForCache($param, $type='', $field='*') {
		if(!$param){
			return false;
		}
		$condition_str = $this->getCondition($param);
		$array	= array();
		switch ($type){
			case 'spec':
				$array['table']		= 'type_spec, spec, spec_value, goods_class';
				$array['join_type']	= 'INNER JOIN';
				$array['join_on']	= array(
					'type_spec.sp_id=spec.sp_id',
					'type_spec.sp_id=spec_value.sp_id',
					'type_spec.type_id=goods_class.type_id'
				);
				$array['order'] = $param['order'] ? $param['order'] : 'spec.sp_sort asc, spec_value.sp_value_sort asc';
				break;
			case 'attr':
				$array['table']		= 'attribute, attribute_value, goods_class';
				$array['join_type']	= 'INNER JOIN';
				$array['join_on']	= array(
					'attribute.attr_id=attribute_value.attr_id',
					'attribute.type_id=goods_class.type_id'
				);
				$array['order'] = $param['order'] ? $param['order'] : 'attribute.attr_sort asc, attribute_value.attr_value_sort asc, attribute_value.attr_value_id asc';
				break;
			case 'brand':
				$array['table']		= 'type_brand, brand, goods_class';
				$array['join_type']	= 'INNER JOIN';
				$array['join_on']	= array(
					'type_brand.brand_id=brand.brand_id',
					'type_brand.type_id=goods_class.type_id'
				);
				$array['order'] = $param['order'] ? $param['order'] : 'brand.brand_sort asc';
				break;
		}
		$array['where'] = $condition_str;
		$array['field'] = $field;
		$result = Db::select($array);
		return $result;
	}
	/**
	 * 根据规格值、品牌、属性值查询商品
	 * 
	 * @param array $param 类型相关信息
	 * @param int   $type  参数
	 * @param string $field 
	 */
	public function typeRelatedGroupList($param, $type='') {
		if(!$param){
			return false;
		}
		$condition_str = $this->getCondition($param);
		$array	= array();
		switch ($type){
			case 'spec':
				$array['table']		= 'goods_spec_index';
				$array['field'] 	= 'goods_id, sp_value_id';
				if (isset($param['limit'])){
					$array['limit'] 	= $param['limit'];
				}
				break;
			case 'attr':
				$array['table']		= 'goods_attr_index';
				$array['field']		= 'goods_id, attr_value_id';
				break;
			case 'brand':
				$array['table']		= 'goods';
				$array['field'] 	= 'goods_id, brand_id';
				if (isset($param['limit'])){
					$array['limit'] 	= $param['limit'];
				}				
				break;
		}
		$array['where'] = $condition_str;
		$result = Db::select($array);
		return $result;
	}
	/**
	 * 删除属性相关
	 * 
	 * @param string $table 表名 spec,spec_value
	 * @param array $param 一维数组
	 * @return bool
	 */
	public function delType($table,$param){
		$condition_str = $this->getCondition($param);
		return Db::delete($table, $condition_str);
	}
	/**
	 * 将条件数组组合为SQL语句的条件部分
	 * 
	 * @param	array $condition_array
	 * @return	string
	 */
	private function getCondition($condition_array) {
		$condition_str = '';
		if($condition_array['goods_id'] != ''){
			$condition_str .= " and goods_id ='".$condition_array['goods_id']."'";
		}
		if($condition_array['in_goods_id'] != ''){
			$condition_str .= " and goods_id in (".$condition_array['in_goods_id'].")";
		}
		if($condition_array['gc_id'] != ''){
			$condition_str .= " and gc_id ='".$condition_array['gc_id']."'";
		}
		if($condition_array['in_gc_id'] != ''){
			$condition_str .= " and gc_id in (".$condition_array['in_gc_id'].")";
		}
		if($condition_array['type_id'] != ''){
			$condition_str .= ' and type_id = "'.$condition_array['type_id'].'"';
		}
		if($condition_array['goods_class.type_id'] != ''){
			$condition_str .= ' and goods_class.type_id = "'.$condition_array['goods_class.type_id'].'"';
		}
		if($condition_array['in_type_id'] != ''){
			$condition_str .= ' and type_id in ('.$condition_array['in_type_id'].')';
		}	
		if($condition_array['in_sp_id'] != ''){
			$condition_str .= ' and sp_id in ('.$condition_array['in_sp_id'].')';
		}	
		if($condition_array['attr_id'] != ''){
			$condition_str .= ' and attr_id = "'.$condition_array['attr_id'].'"';
		}
		if($condition_array['in_attr_id'] != ''){
			$condition_str .= ' and attr_id in ('.$condition_array['in_attr_id'].')';
		}
		if($condition_array['brand_id'] != ''){
			$condition_str .= " and brand_id = '".$condition_array['brand_id']."'";
		}
		if($condition_array['sp_value_id'] != ''){
			$condition_str .= " and sp_value_id = '".$condition_array['sp_value_id']."'";
		}
		if($condition_array['attr_value_id'] != ''){
			$condition_str .= " and attr_value_id = '".$condition_array['attr_value_id']."'";
		}
		if ($condition_array['brand_apply'] != ''){
			$condition_str .= " and brand.brand_apply = '". $condition_array['brand_apply'] ."'";
		}
		if ($condition_array['attr_show'] != ''){
			$condition_str .= " and attr_show = '". $condition_array['attr_show'] ."'";
		}
		return $condition_str;
	}
}