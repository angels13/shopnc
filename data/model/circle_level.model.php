<?php
/**
 * Circle Level
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

class circle_levelModel extends Model {
 	public function __construct(){
		parent::__construct();
	}
	/**
	 * insert
	 * @param array $insert
	 * @param bool $replace
	 */
	public function levelInsert($insert, $replace){
		$this->table('circle_ml')->insert($insert, $replace);
		return $this->updateLevelName($insert);
	}
	
	/**
	 * update level name
	 * @param array $insert
	 */
	private function updateLevelName($insert){
		$str = '( case cm_level ';
		for ($i=1; $i<=16; $i++){
			$str .= ' when '.$i.' then "'.$insert['ml_'.$i].'"';
		}
		$str .= ' else cm_levelname end)';
		
		$update = array();
		$update['cm_levelname'] = array('exp',$str);
		
		$where = array();
		$where['circle_id'] = $insert['circle_id'];
		return $this->table('circle_member')->where($where)->update($update);
	}
}
