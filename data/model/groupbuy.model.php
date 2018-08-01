<?php
/**
 * 团购活动模型 
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
class groupbuyModel extends Model{

    const GROUPBUY_STATE_REVIEW = 10;
    const GROUPBUY_STATE_NORMAL = 20;
    const GROUPBUY_STATE_REVIEW_FAIL = 30;
    const GROUPBUY_STATE_CANCEL = 31;
    const GROUPBUY_STATE_CLOSE = 32;

    private $groupbuy_state_array = array(
        0 => '全部',
        self::GROUPBUY_STATE_REVIEW => '审核中',
        self::GROUPBUY_STATE_NORMAL => '正常',
        self::GROUPBUY_STATE_CLOSE => '已结束',
        self::GROUPBUY_STATE_REVIEW_FAIL => '审核失败',
        self::GROUPBUY_STATE_CANCEL => '管理员关闭',
    );

    public function __construct() {
        parent::__construct('groupbuy');
    }

	/**
     * 读取团购列表
	 * @param array $condition 查询条件
	 * @param int $page 分页数
	 * @param string $order 排序
	 * @param string $field 所需字段
     * @return array 团购列表
	 *
	 */
	public function getGroupbuyList($condition, $page = null, $order = 'state asc', $field = '*', $limit = 0) {
        $groupbuy_list = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        if(!empty($groupbuy_list)) {
            for($i =0, $j = count($groupbuy_list); $i < $j; $i++) {
                $groupbuy_list[$i] = $this->getGroupbuyExtendInfo($groupbuy_list[$i]);
            }
        }
        return $groupbuy_list;
	}

    /**
     * 读取可用团购列表
     */
    public function getGroupbuyAvailableList($condition) {
        $condition['state'] = array('in', array(self::GROUPBUY_STATE_REVIEW, self::GROUPBUY_STATE_NORMAL));
        return $this->getGroupbuyList($condition);
    }
	
	/**
	 * 查询团购数量
	 * @param array $condition
	 * @return int
	 */
	public function getGroupbuyCount($condition) {
	    return $this->where($condition)->count();
	}

    /**
     * 读取当前可用的团购列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 团购列表
     *
     */
    public function getGroupbuyOnlineList($condition, $page = null, $order = 'state asc', $field = '*') {
        $condition['state'] = self::GROUPBUY_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP); 
        $condition['end_time'] = array('gt', TIMESTAMP); 
        return $this->getGroupbuyList($condition, $page, $order, $field);
    }

    /**
     * 读取即将开始的团购列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 团购列表
     *
     */
    public function getGroupbuySoonList($condition, $page = null, $order = 'state asc', $field = '*') {
        $condition['state'] = self::GROUPBUY_STATE_NORMAL;
        $condition['start_time'] = array('gt', TIMESTAMP); 
        return $this->getGroupbuyList($condition, $page, $order, $field);
    }

    /**
     * 读取已经结束的团购列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 团购列表
     *
     */
    public function getGroupbuyHistoryList($condition, $page = null, $order = 'state asc', $field = '*') {
        $condition['state'] = self::GROUPBUY_STATE_CLOSE;
        return $this->getGroupbuyList($condition, $page, $order, $field);
    }

    /**
     * 读取推荐团购列表
     * @param int $limit 要读取的数量
     */
    public function getGroupbuyCommendedList($limit = 4) {
        $condition = array();
        $condition['state'] = self::GROUPBUY_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP); 
        $condition['end_time'] = array('gt', TIMESTAMP); 
        return $this->getGroupbuyList($condition, null, 'recommended desc', '*', $limit);
    }

    /**
     * 根据条件读取团购信息
     * @param array $condition 查询条件
     * @return array 团购信息
     *
	 */
    public function getGroupbuyInfo($condition) {
        $groupbuy_info = $this->where($condition)->find();
        $groupbuy_info = $this->getGroupbuyExtendInfo($groupbuy_info);
        return $groupbuy_info;
    }

    /**
	 * 根据团购编号读取团购信息
	 * @param array $groupbuy_id 团购活动编号
	 * @param int $store_id 如果提供店铺编号，判断是否为该店铺活动，如果不是返回null
     * @return array 团购信息
	 *
	 */
    public function getGroupbuyInfoByID($groupbuy_id, $store_id = 0) {
        if(intval($groupbuy_id) <= 0) {
            return null;
        }

        $condition = array();
        $condition['groupbuy_id'] = $groupbuy_id;
        $groupbuy_info = $this->getGroupbuyInfo($condition);

        if($store_id > 0 && $groupbuy_info['store_id'] != $store_id) {
            return null;
        } else {
            return $groupbuy_info;
        }
    }

    /**
     * 根据商品编号查询是否有可用团购活动，如果有返回团购信息，没有返回null
     * @param int $goods_id
     * @return array $groupbuy_info
     *
     */
    public function getGroupbuyInfoByGoodsCommonID($goods_commonid) {
        $groupbuy_list = $this->_getGroupbuyListByGoodsCommon($goods_commonid);
        return $groupbuy_list[0];
    }

    /**
     * 根据商品编号查询是否有可用团购活动，如果有返回团购活动，没有返回null
     * @param string $goods_string 商品编号字符串，例：'1,22,33'
     * @return array $groupbuy_list
     *
     */
    public function getGroupbuyListByGoodsCommonIDString($goods_commonid_string) {
        $groupbuy_list = $this->_getGroupbuyListByGoodsCommon($goods_commonid_string);
        $groupbuy_list = array_under_reset($groupbuy_list, 'goods_commonid');
        return $groupbuy_list;
    }

    /**
     * 根据商品编号查询是否有可用团购活动，如果有返回团购活动，没有返回null
     * @param string $goods_id_string
     * @return array $groupbuy_list
     *
     */
    private function _getGroupbuyListByGoodsCommon($goods_commonid_string) {
        $condition = array();
        $condition['state'] = self::GROUPBUY_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['goods_commonid'] = array('in', $goods_commonid_string);
        $xianshi_goods_list = $this->getGroupbuyList($condition, null, 'groupbuy_id desc', '*');
        return $xianshi_goods_list;
    }

    /**
     * 团购状态数组
     */
    public function getGroupbuyStateArray() {
        return $this->groupbuy_state_array;
    }


	/*
	 * 增加 
	 * @param array $param
	 * @return bool
     *
	 */
    public function addGroupbuy($param){
        // 发布团购锁定商品
        $this->_lockGoods($param['goods_commonid']);

        $param['state'] = self::GROUPBUY_STATE_REVIEW;
        $param['recommended'] = 0;
        return $this->insert($param);	
    }

    /**
     * 锁定商品
     */
    private function _lockGoods($goods_commonid) {
        $condition = array();
        $condition['goods_commonid'] = $goods_commonid;

        $model_goods = Model('goods');
        $model_goods->editGoodsCommonLock($condition);
    }

    /**
     * 解锁商品
     */
    private function _unlockGoods($goods_commonid) {
        $condition = array();
        $condition['goods_commonid'] = $goods_commonid;
        $condition['end_time'] = array('gt', TIMESTAMP);
        $condition['state'] = array('in', array(self::GROUPBUY_STATE_REVIEW, self::GROUPBUY_STATE_NORMAL));
        $groupbuy_list = $this->getGroupbuyList($condition);

        if(!empty($groupbuy_list)) {
            $model_goods = Model('goods');
            $model_goods->editGoodsCommonUnlock(array('goods_commonid' => $goods_commonid));
        }
    }

    /*
	 * 更新
	 * @param array $update
	 * @param array $condition
	 * @return bool
     *
	 */
    public function editGroupbuy($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /*
	 * 审核成功
	 * @param int $groupbuy_id
	 * @return bool
     *
	 */
    public function reviewPassGroupbuy($groupbuy_id) {
        $condition = array();
        $condition['groupbuy_id'] = $groupbuy_id;

        $update = array();
        $update['state'] = self::GROUPBUY_STATE_NORMAL;

        return $this->editGroupbuy($update, $condition);
    }

    /*
	 * 审核失败 
	 * @param int $groupbuy_id
	 * @return bool
     *
	 */
    public function reviewFailGroupbuy($groupbuy_id) {
        // 商品解锁
        $groupbuy_info = $this->getGroupbuyInfoByID($groupbuy_id);
        $this->_unlockGoods($groupbuy_info['goods_commonid']);

        $condition = array();
        $condition['groupbuy_id'] = $groupbuy_id;

        $update = array();
        $update['state'] = self::GROUPBUY_STATE_REVIEW_FAIL;

        return $this->editGroupbuy($update, $condition);
    }

    /*
     * 取消 
     * @param int $groupbuy_id
     * @return bool
     *
     */
    public function cancelGroupbuy($groupbuy_id) {
        // 商品解锁
        $groupbuy_info = $this->getGroupbuyInfoByID($groupbuy_id);
        $this->_unlockGoods($groupbuy_info['goods_commonid']);

        $condition = array();
        $condition['groupbuy_id'] = $groupbuy_id;

        $update = array();
        $update['state'] = self::GROUPBUY_STATE_CANCEL;

        return $this->editGroupbuy($update, $condition);
    }

    /**
     * 过期团购修改状态，解锁对应商品
     */
    public function editExpireGroupbuy() {
        $condition = array();
        $condition['end_time'] = array('lt', TIMESTAMP);
        $condition['state'] = array('in', array(self::GROUPBUY_STATE_REVIEW, self::GROUPBUY_STATE_NORMAL));

        $expire_groupbuy_list = $this->getGroupbuyList($condition, null);
        $groupbuy_id_string = '';
        if(!empty($expire_groupbuy_list)) {
            foreach ($expire_groupbuy_list as $value) {
                $groupbuy_id_string .= $value['groupbuy_id'].',';
                $this->_unlockGoods($value['goods_commonid']);
            }
        }

        if($groupbuy_id_string != '') {
            $updata = array();
            $update['state'] = self::GROUPBUY_STATE_CLOSE;
            $condition = array();
            $condition['groupbuy_id'] = array('in', rtrim($groupbuy_id_string, ','));
            $this->editGroupbuy($update, $condition);
        }
    }

	/*
	 * 删除团购活动
	 * @param array $condition
	 * @return bool
     *
	 */
    public function delGroupbuy($condition){
        $groupbuy_list = $this->getGroupbuyList($condition);

        if(!empty($groupbuy_list)) {
            foreach ($groupbuy_list as $value) {
                // 商品解锁
                $this->_unlockGoods($value['goods_commonid']);

                list($base_name, $ext) = explode('.', $value['groupbuy_image']);
                list($store_id) = explode('_', $base_name);
                $path = BASE_UPLOAD_PATH.DS.ATTACH_GROUPBUY.DS.$store_id.DS;
                @unlink($path.$base_name.'.'.$ext);
                @unlink($path.$base_name.'_small.'.$ext);
                @unlink($path.$base_name.'_mid.'.$ext);
                @unlink($path.$base_name.'_max.'.$ext);

                if(!empty($value['groupbuy_image1'])) {
                    list($base_name, $ext) = explode('.', $value['groupbuy_image1']);
                    @unlink($path.$base_name.'.'.$ext);
                    @unlink($path.$base_name.'_small.'.$ext);
                    @unlink($path.$base_name.'_mid.'.$ext);
                    @unlink($path.$base_name.'_max.'.$ext);
                }
            }
        }
        return $this->where($condition)->delete();
    }

    /**
     * 获取团购扩展信息
     */
    public function getGroupbuyExtendInfo($groupbuy_info) {
        $groupbuy_info['groupbuy_url'] = urlShop('show_groupbuy', 'groupbuy_detail', array('group_id' => $groupbuy_info['groupbuy_id']));
        $groupbuy_info['goods_url'] = urlShop('goods', 'index', array('goods_id' => $groupbuy_info['goods_id']));
        $groupbuy_info['start_time_text'] = date('Y-m-d H:i', $groupbuy_info['start_time']);
        $groupbuy_info['end_time_text'] = date('Y-m-d H:i', $groupbuy_info['end_time']);
        if(empty($groupbuy_info['groupbuy_image1'])) {
            $groupbuy_info['groupbuy_image1'] = $groupbuy_info['groupbuy_image'];
        }
        if($groupbuy_info['start_time'] > TIMESTAMP && $groupbuy_info['state'] == self::GROUPBUY_STATE_NORMAL) {
            $groupbuy_info['groupbuy_state_text'] = '正常(未开始)';
        } elseif ($groupbuy_info['end_time'] < TIMESTAMP && $groupbuy_info['state'] == self::GROUPBUY_STATE_NORMAL) {
            $groupbuy_info['groupbuy_state_text'] = '已结束';
        } else {
            $groupbuy_info['groupbuy_state_text'] = $this->groupbuy_state_array[$groupbuy_info['state']];
        } 

        if($groupbuy_info['state'] == self::GROUPBUY_STATE_REVIEW) {
            $groupbuy_info['reviewable'] = true;
        } else {
            $groupbuy_info['reviewable'] = false;
        }

        if($groupbuy_info['state'] == self::GROUPBUY_STATE_NORMAL) {
            $groupbuy_info['cancelable'] = true;
        } else {
            $groupbuy_info['cancelable'] = false;
        }

        switch ($groupbuy_info['state']) {
            case self::GROUPBUY_STATE_REVIEW:
                $groupbuy_info['state_flag'] = 'not-verify';
                $groupbuy_info['button_text'] = '未审核';
                break;
            case self::GROUPBUY_STATE_REVIEW_FAIL:
            case self::GROUPBUY_STATE_CANCEL:
            case self::GROUPBUY_STATE_CLOSE:
                $groupbuy_info['state_flag'] = 'close';
                $groupbuy_info['button_text'] = '已结束';
                break;
            case self::GROUPBUY_STATE_NORMAL:
                if($groupbuy_info['start_time'] > TIMESTAMP) {
                    $groupbuy_info['state_flag'] = 'not-start';
                    $groupbuy_info['button_text'] = '未开始';
                    $groupbuy_info['count_down_text'] = '距团购开始';
                    $groupbuy_info['count_down'] = $groupbuy_info['start_time'] - TIMESTAMP;
                } elseif ($groupbuy_info['end_time'] < TIMESTAMP) {
                    $groupbuy_info['state_flag'] = 'close';
                    $groupbuy_info['button_text'] = '已结束';
                } else {
                    $groupbuy_info['state_flag'] = 'buy-now';
                    $groupbuy_info['button_text'] = '我要团';
                    $groupbuy_info['count_down_text'] = '距团购结束';
                    $groupbuy_info['count_down'] = $groupbuy_info['end_time'] - TIMESTAMP;
                }
                break;
        }
        return $groupbuy_info;
    }

}
