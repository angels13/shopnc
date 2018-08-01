<?php
/**
 * 商品评价模型
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class evaluate_goodsModel extends Model {

    public function __construct(){
        parent::__construct('evaluate_goods');
    }

	/**
	 * 查询评价列表
     *
	 * @param array $condition 查询条件
	 * @param int $page 分页数
	 * @param string $order 排序
	 * @param string $field 字段
     * @return array
	 */
    public function getEvaluateGoodsList($condition, $page = null, $order = 'geval_id desc', $field = '*') {
        $list = $this->field($field)->where($condition)->page($page)->order($order)->select();
        return $list;
    }

    /**
     * 根据编号查询商品评价 
     */
    public function getEvaluateGoodsInfoByID($geval_id, $store_id = 0) {
        if(intval($geval_id) <= 0) {
            return null;
        }

        $info = $this->where(array('geval_id' => $geval_id))->find();

        if($store_id > 0 && intval($info['geval_storeid']) !== $store_id) {
            return null;
        } else {
            return $info;
        }
    }

    /**
     * 根据商品编号查询商品评价信息 
     */
    public function getEvaluateGoodsInfoByGoodsID($goods_id) {
        $prefix = 'evaluation_goods_info';
        $info = rcache($goods_id, $prefix);
        if(empty($info)) {
            $info = array();
            $good = $this->field('count(*) as count')->where(array('geval_goodsid'=>$goods_id,'geval_scores' => array('in', '4,5')))->find();
            $info['good'] = $good['count'];
            $normal = $this->field('count(*) as count')->where(array('geval_goodsid'=>$goods_id,'geval_scores' => array('in', '2,3')))->find();
            $info['normal'] = $normal['count']; 
            $bad = $this->field('count(*) as count')->where(array('geval_goodsid'=>$goods_id,'geval_scores' => array('in', '1')))->find();
            $info['bad'] = $bad['count']; 
            $info['all'] = $info['good'] + $info['normal'] + $info['bad']; 
            if(intval($info['all']) > 0) {
                $info['good_percent'] = intval($info['good'] / $info['all'] * 100);
                $info['normal_percent'] = intval($info['normal'] / $info['all'] * 100);
                $info['bad_percent'] = intval($info['bad'] / $info['all'] * 100);
                $info['good_star'] = ceil($info['good'] / $info['all'] * 5);
            } else {
                $info['good_percent'] = 100;
                $info['normal_percent'] = 0;
                $info['bad_percent'] = 0;
                $info['good_star'] = 5;
            }

            //更新商品表好评星级和评论数
            $model_goods = Model('goods');
            $update = array();
            $update['evaluation_good_star'] = $info['good_star'];
            $update['evaluation_count'] = $info['all'];
            $model_goods->editGoods($update, array('goods_id' => $goods_id));
            wcache($goods_id, $info, $prefix);
        }
        return $info;
    }

    /**
     * 批量添加商品评价
     */
    public function addEvaluateGoodsArray($param) {
        return $this->insertAll($param);	
    }

    /**
     * 更新商品评价
     */
    public function editEvaluateGoods($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /**
     * 删除商品评价
     */
    public function delEvaluateGoods($condition) {
        return $this->where($condition)->delete();
    }
}
