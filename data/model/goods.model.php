<?php
/**
 * 商品管理
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

class goodsModel extends Model{
    public function __construct(){
        parent::__construct('goods');
    }
    
    const STATE1 = 1;       // 出售中
    const STATE0 = 0;       // 下架
    const STATE10 = 10;     // 违规
    const VERIFY1 = 1;      // 审核通过
    const VERIFY0 = 0;      // 审核失败
    const VERIFY10 = 10;    // 等待审核
    
    /**
     * 新增商品数据
     * 
     * @param array $insert 数据
     * @param string $table 表名
     */
    public function addGoods($insert, $table = "goods") {
        return $this->table($table)->insert($insert);
    }

    /**
     * 新增多条商品数据
     * 
     * @param unknown $insert
     */
    public function addGoodsAll($insert, $table = 'goods') {
        return $this->table($table)->insertAll($insert);
    }
    
    /**
     * 商品SKU列表
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @param boolean $lock 是否锁定
     * @return array 二维数组
     */
    public function getGoodsList($condition, $field = '*', $group = '',$order = '', $limit = 0, $page = 0, $lock = false, $count = 0) {
        $condition = $this->_getRecursiveClass($condition);
        return $this->table('goods')->field($field)->where($condition)->group($group)->order($order)->limit($limit)->page($page, $count)->lock($lock)->select();
    }

    /**
     * 出售中的商品SKU列表（只显示不同颜色的商品，前台商品索引，店铺也商品列表等使用）
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param number $page
     * @return array
     */
    public function getGoodsListByColorDistinct($condition, $field = '*', $order = 'goods_id asc', $page = 0) {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        $condition = $this->_getRecursiveClass($condition);
        $field = "CONCAT(goods_commonid,',',color_id) as nc_distinct ," . $field;
        $count = $this->getGoodsOnlineCount($condition,"distinct CONCAT(goods_commonid,',',color_id)");
        $goods_list = array();
        if ($count != 0) {
            $goods_list = $this->getGoodsOnlineList($condition, $field, $page, $order, 0, 'nc_distinct', false, $count);
        }
        return $goods_list;
    }

    /**
     * 在售商品SKU列表
     * 
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @param boolean $lock 是否锁定
     * @return array
     */
    public function getGoodsOnlineList($condition, $field = '*', $page = 0, $order = 'goods_id desc', $limit = 0, $group = '', $lock = false, $count = 0) {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsList($condition, $field, $group, $order, $limit, $page, $lock, $count);
    }
    
    /**
     * 商品SUK列表 goods_show = 1 为出售中，goods_show = 0为未出售（仓库中，违规，等待审核）
     * 
     * @param unknown $condition
     * @param string $field
     * @return multitype:
     */
    public function getGoodsAsGoodsShowList($condition, $field = '*') {
        $field = $this->_asGoodsShow($field);
        return $this->getGoodsList($condition, $field);
    }

    /**
     * 商品列表 卖家中心使用
     * 
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $page 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsCommonList($condition, $field = '*', $page = 10, $order = 'goods_commonid desc') {
        $condition = $this->_getRecursiveClass($condition);
        return $this->table('goods_common')->field($field)->where($condition)->order($order)->page($page)->select();
    }
    
    /**
     * 出售中的商品列表 卖家中心使用
     * 
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $page 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsCommonOnlineList($condition, $field = '*', $page = 10, $order = "goods_commonid desc") {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonList($condition, $field, $page, $order);
    }
    
    /**
     * 仓库中的商品列表 卖家中心使用
     * 
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $page 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsCommonOfflineList($condition, $field = '*', $page = 10, $order = "goods_commonid desc") {
        $condition['goods_state']   = self::STATE0;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonList($condition, $field, $page, $order);
    }
    
    /**
     * 违规的商品列表 卖家中心使用
     * 
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $page 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsCommonLockUpList($condition, $field = '*', $page = 10, $order = "goods_commonid desc") {
        $condition['goods_state']   = self::STATE10;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonList($condition, $field, $page, $order);
    }
    
    /**
     * 等待审核或审核失败的商品列表 卖家中心使用
     * 
     * @param array $condition 条件
     * @param array $field 字段
     * @param string $page 分页
     * @param string $order 排序
     * @return array
     */
    public function getGoodsCommonWaitVerifyList($condition, $field = '*', $page = 10, $order = "goods_commonid desc") {
        if (!isset($condition['goods_verify'])) {
            $condition['goods_verify']  = array('neq', self::VERIFY1);
        }
        return $this->getGoodsCommonList($condition, $field, $page, $order);
    }
    
    /**
     * 公共商品列表，goods_show = 1 为出售中，goods_show = 0为未出售（仓库中，违规，等待审核）
     */
    public function getGoodsCommonAsGoodsShowList($condition, $field = '*') {
        return $this->getGoodsCommonList($condition, $field);
    }
    
    /**
     * 查询商品SUK及其店铺信息
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsStoreList($condition, $field = '*') {
        $condition = $this->_getRecursiveClass($condition);
        return $this->table('goods,store')->field($field)->join('inner')->on('goods.store_id = store.store_id')->where($condition)->select();
    }
    
    /**
     * 计算商品库存
     * 
     * @param array $goods_list
     * @return array|boolean
     */
    public function calculateStorage($goods_list, $storage_alarm = 0) {
        // 计算库存
        if (!empty($goods_list)) {
            $goodsid_array = array();
            foreach ($goods_list as $value) {
                $goodscommonid_array[] = $value['goods_commonid'];
            }
            $goods_storage = $this->getGoodsList(array('goods_commonid' => array('in', $goodscommonid_array)), 'goods_storage,goods_commonid,goods_id');
            $storage_array = array();
            foreach ($goods_storage as $val) {
                if ($storage_alarm != 0 && $val['goods_storage'] <= $storage_alarm) {
                    $storage_array[$val['goods_commonid']]['alarm'] = true;
                }
                $storage_array[$val['goods_commonid']]['sum'] += $val['goods_storage'];
                $storage_array[$val['goods_commonid']]['goods_id'] = $val['goods_id'];
            }
            return $storage_array;
        } else {
            return false;
        }
    }
    
    /**
     * 更新商品SUK数据
     * 
     * @param array $update 更新数据
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoods($update, $condition) {
        return $this->table('goods')->where($condition)->update($update);
    }

    
    /**
     * 更新商品数据
     * @param array $update 更新数据
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoodsCommon($update, $condition) {
        return $this->table('goods_common')->where($condition)->update($update);
    }
    
    /**
     * 更新商品数据
     * @param array $update 更新数据
     * @param array $condition 条件
     * @return boolean
     */
    public function editGoodsCommonNoLock($update, $condition) {
        $condition['goods_lock'] = 0;
        return $this->table('goods_common')->where($condition)->update($update);
    }
    
    /**
     * 锁定商品
     * @param unknown $condition
     * @return boolean
     */
    public function editGoodsCommonLock($condition) {
        $update = array('goods_lock' => 1);
        return $this->table('goods_common')->where($condition)->update($update);
    }

     /**
     * 解锁商品
     * @param unknown $condition
     * @return boolean
     */
    public function editGoodsCommonUnlock($condition) {
        $update = array('goods_lock' => 0);
        return $this->table('goods_common')->where($condition)->update($update);
    }
   
    /**
     * 更新商品信息
     * 
     * @param array $condition
     * @param array $update1
     * @param array $update2
     * @return boolean
     */
    public function editProduces($condition, $update1, $update2 = array()) {
        $update2 = empty($update2) ? $update1 : $update2;
        $return1 = $this->editGoodsCommon($update1, $condition);
        $return2 = $this->editGoods($update2, $condition);
        if ($return1 && $return2) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 更新未锁定商品信息
     * 
     * @param array $condition
     * @param array $update1
     * @param array $update2
     * @return boolean
     */
    public function editProducesNoLock($condition, $update1, $update2 = array()) {
        $update2 = empty($update2) ? $update1 : $update2;
        $condition['goods_lock'] = 0;
        $common_array = $this->getGoodsCommonList($condition);
        $common_array = array_under_reset($common_array, 'goods_commonid');
        $commonid_array = array_keys($common_array);
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $return1 = $this->editGoodsCommon($update1, $where);
        $return2 = $this->editGoods($update2, $where);
        if ($return1 && $return2) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 商品下架
     * @param array $condition 条件
     * @return boolean
     */
    public function editProducesOffline($condition){
        $update = array('goods_state' => self::STATE0);
        return $this->editProducesNoLock($condition, $update);
    }

    /**
     * 商品上架
     * @param array $condition 条件
     * @return boolean
     */
    public function editProducesOnline($condition){
        $update = array('goods_state' => self::STATE1);
        // 禁售商品、审核失败商品不能上架。
        $condition['goods_state'] = self::STATE0;
        $condition['goods_verify'] = array('neq', self::VERIFY0);
        return $this->editProduces($condition, $update);
    }
    
    /**
     * 违规下架
     * 
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editProducesLockUp($update, $condition) {
        $update_param['goods_state'] = self::STATE10;
        $update = array_merge($update, $update_param);
        return $this->editProduces($condition, $update, $update_param);
    }
    
    /**
     * 获取单条商品SKU信息
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsInfo($condition, $field = '*') {
        return $this->table('goods')->field($field)->where($condition)->find();
    }
    
    /**
     * 获取单条商品SKU信息
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsOnlineInfo($condition, $field = '*') {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->table('goods')->field($field)->where($condition)->find();
    }

    /**
     * 获取单条商品SKU信息，goods_show = 1 为出售中，goods_show = 0为未出售（仓库中，违规，等待审核）
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsAsGoodsShowInfo($condition, $field = '*') {
        $field = $this->_asGoodsShow($field);
        return $this->getGoodsInfo($condition, $field);
    }

    /**
     * 获取单条商品信息
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodeCommonInfo($condition, $field = '*') {
        return $this->table('goods_common')->field($field)->where($condition)->find();
    }

    /**
     * 获取单条商品信息，goods_show = 1 为出售中，goods_show = 0为未出售（仓库中，违规，等待审核）
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodeCommonAsGoodsShowInfo($condition, $field = '*') {
        $field = $this->_asGoodsShow($field);
        return $this->getGoodeCommonInfo($condition, $field);
    }
    
    /**
     * 获取单条商品信息
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsDetail($goods_id) {
        if($goods_id <= 0) {
            return null;
        }
        $result1 = $this->getGoodsAsGoodsShowInfo(array('goods_id' => $goods_id));
        if (empty($result1)) {
            return null;
        }
        $result2 = $this->getGoodeCommonAsGoodsShowInfo(array('goods_commonid' => $result1['goods_commonid']));
        $goods_info = array_merge($result2, $result1);

        $goods_info['spec_value'] = unserialize($goods_info['spec_value']);
        $goods_info['spec_name'] = unserialize($goods_info['spec_name']);
        $goods_info['goods_spec'] = unserialize($goods_info['goods_spec']);
        $goods_info['goods_attr'] = unserialize($goods_info['goods_attr']);

        // 查询所有规格商品
        $spec_array = $this->getGoodsList(array('goods_commonid' => $goods_info['goods_commonid']), 'goods_spec,goods_id,store_id,goods_image,color_id');
        $spec_list = array();       // 各规格商品地址，js使用
        $spec_list_mobile = array();       // 各规格商品地址，js使用
        $spec_image = array();      // 各规格商品主图，规格颜色图片使用
        foreach ($spec_array as $key => $value) {
            $s_array = unserialize($value['goods_spec']);
            $tmp_array = array();
            if (!empty($s_array) && is_array($s_array)) {
                foreach ($s_array as $k => $v) {
                    $tmp_array[] = $k;
                }
            }
            sort($tmp_array);
            $spec_sign = implode('|', $tmp_array);
            $tpl_spec = array();
            $tpl_spec['sign'] = $spec_sign;
            $tpl_spec['url'] = urlShop('goods', 'index', array('goods_id' => $value['goods_id']));
            $spec_list[] = $tpl_spec;
            $spec_list_mobile[$spec_sign] = $value['goods_id'];
            $spec_image[$value['color_id']] = thumb($value, 60);
        }
        $spec_list = json_encode($spec_list);

        // 商品多图
        $image_more = $this->getGoodsImageList(array('goods_commonid' => $goods_info['goods_commonid'], 'color_id' => $goods_info['color_id']), 'goods_image');
        $goods_image = array();
        $goods_image_mobile = array();
        if (!empty($image_more)) {
            foreach ($image_more as $val) {
                $goods_image[] = "{ title : '', levelA : '".cthumb($val['goods_image'], 60, $goods_info['store_id'])."', levelB : '".cthumb($val['goods_image'], 360, $goods_info['store_id'])."', levelC : '".cthumb($val['goods_image'], 360, $goods_info['store_id'])."', levelD : '".cthumb($val['goods_image'], 1280, $goods_info['store_id'])."'}";
                $goods_image_mobile[] = cthumb($val['goods_image'], 360, $goods_info['store_id']);
            }
        } else {
            $goods_image[] = "{ title : '', levelA : '".thumb($goods_info, 60)."', levelB : '".thumb($goods_info, 360)."', levelC : '".thumb($goods_info, 360)."', levelD : '".thumb($goods_info, 1280)."'}";
            $goods_image_mobile[] = thumb($goods_info, 360);
        }

        //团购
        if (C('groupbuy_allow')) {
            $groupbuy_info = Model('groupbuy')->getGroupbuyInfoByGoodsCommonID($goods_info['goods_commonid']);
            if (!empty($groupbuy_info)) {
                $goods_info['promotion_type'] = 'groupbuy';
                $goods_info['remark'] = $groupbuy_info['remark'];
                $goods_info['promotion_price'] = $groupbuy_info['groupbuy_price'];
                $goods_info['down_price'] = ncPriceFormat($goods_info['goods_price'] - $groupbuy_info['groupbuy_price']);
                $goods_info['upper_limit'] = $groupbuy_info['upper_limit'];
            }
        }

        //限时折扣
        if (C('promotion_allow') && empty($groupbuy_info)) {
            $xianshi_info = Model('p_xianshi_goods')->getXianshiGoodsInfoByGoodsID($goods_id);
            if (!empty($xianshi_info)) {
                $goods_info['promotion_type'] = 'xianshi';
                $goods_info['remark'] = $xianshi_info['xianshi_title'];
                $goods_info['promotion_price'] = $xianshi_info['xianshi_price'];     
                $goods_info['down_price'] = ncPriceFormat($goods_info['goods_price'] - $xianshi_info['xianshi_price']);
                $goods_info['lower_limit'] = $xianshi_info['lower_limit'];
            }
        }

        //满即送
        $mansong_info = Model('p_mansong')->getMansongInfoByStoreID($goods_info['store_id']);

        // 商品受关注次数加1
        $_times = cookie('tm_visit_product');
        if (empty($_times)) {
            $this->editGoods(array('goods_click' => array('exp', 'goods_click + 1')), array('goods_id' => $goods_id));
            setNcCookie('tm_visit_product', 1);
            $goods_info['goods_click'] = intval($goods_info['goods_click']) + 1;
        }
 
        $result = array();
        $result['goods_info'] = $goods_info;
        $result['spec_list'] = $spec_list;
        $result['spec_list_mobile'] = $spec_list_mobile;
        $result['spec_image'] = $spec_image;
        $result['goods_image'] = $goods_image;
        $result['goods_image_mobile'] = $goods_image_mobile;
        $result['groupbuy_info'] = $groupbuy_info;
        $result['xianshi_info'] = $xianshi_info;
        $result['mansong_info'] = $mansong_info;
        return $result;
    }
    
    /**
     * 获得商品SKU某字段的和
     * 
     * @param array $condition
     * @param string $field
     * @return boolean
     */
    public function getGoodsSum($condition, $field) {
        return $this->table('goods')->where($condition)->sum($field);
    }
    
    /**
     * 获得商品SKU数量
     * 
     * @param array $condition
     * @param string $field
     * @return int
     */
    public function getGoodsCount($condition) {
        return $this->table('goods')->where($condition)->count();
    }

    /**
     * 获得出售中商品SKU数量
     *
     * @param array $condition
     * @param string $field
     * @return int
     */
    public function getGoodsOnlineCount($condition, $field = '*', $group = '') {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->table('goods')->where($condition)->group($group)->count1($field);
    }
    /**
     * 获得商品数量
     *
     * @param array $condition
     * @param string $field
     * @return int
     */
    public function getGoodsCommonCount($condition) {
        return $this->table('goods_common')->where($condition)->count();
    }
    
    /**
     * 出售中的商品数量
     *
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonOnlineCount($condition) {
        $condition['goods_state']   = self::STATE1;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonCount($condition);
    }

    /**
     * 仓库中的商品数量
     *
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonOfflineCount($condition) {
        $condition['goods_state']   = self::STATE0;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonCount($condition);
    }
     
    /**
     * 等待审核的商品数量
     * 
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonWaitVerifyCount($condition) {
        $condition['goods_verify']  = self::VERIFY10;
        return $this->getGoodsCommonCount($condition);
    }
    
    /**
     * 审核是被的商品数量
     * 
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonVerifyFailCount($condition) {
        $condition['goods_verify']  = self::VERIFY0;
        return $this->getGoodsCommonCount($condition);
    }
    
    /**
     * 违规下架的商品数量
     * 
     * @param array $condition
     * @return int
     */
    public function getGoodsCommonLockUpCount($condition) {
        $condition['goods_state']   = self::STATE10;
        $condition['goods_verify']  = self::VERIFY1;
        return $this->getGoodsCommonCount($condition);
    }
    
    /**
     * 商品图片列表
     * 
     * @param array $condition
     * @param array $order
     * @param string $field
     * @return array
     */
    public function getGoodsImageList($condition, $field = '*', $order = 'is_default desc,goods_image_sort asc') {
        $this->cls();
        return $this->table('goods_images')->field($field)->where($condition)->order($order)->select();
    }
    
    /**
     * 浏览过的商品
     * 
     * @return array
     */
    public function getViewedGoodsList() {
        //取浏览过产品的cookie(最大四组)
        $viewed_goods = array();
        $cookie_i = 0;
        
        if(cookie('viewed_goods')){
            $string_viewed_goods = decrypt(cookie('viewed_goods'),MD5_KEY);
            if (get_magic_quotes_gpc()) $string_viewed_goods = stripslashes($string_viewed_goods);//去除斜杠
            $cookie_array = array_reverse(unserialize($string_viewed_goods));
            $goodsid_array = array();
            foreach ((array)$cookie_array as $k=>$v){
                $info = explode("-", $v);
                if (is_numeric($info[0])){
                    $goodsid_array[] = intval($info[0]);
                }
            }
            $viewed_list    = $this->getGoodsList(array('goods_id' => array('in', $goodsid_array)), 'goods_id, goods_name, goods_price, goods_image, store_id');
            foreach ((array)$viewed_list as $val){
                $viewed_goods[] = array(
                        "goods_id"      => $val['goods_id'],
                        "goods_name"    => $val['goods_name'],
                        "goods_image"   => $val['goods_image'],
                        "goods_price"   => $val['goods_price'],
                        "store_id"      => $val['store_id']
                );
            }
        }
        return $viewed_goods;
    }

    /**
     * 删除商品SKU信息
     *
     * @param array $condition
     * @return boolean
     */
    public function delGoods($condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id,store_id');
        if (!empty($goods_list)) {
            foreach ($goods_list as $val) {
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$goods_list['store_id'].DS.$goods_list['goods_id'].'.png');
            }
        }
        return $this->table('goods')->where($condition)->delete();
    }
    
    /**
     * 删除商品图片表信息
     * 
     * @param array $condition
     * @return boolean
     */
    public function delGoodsImages($condition) {
        return $this->table('goods_images')->where($condition)->delete();
    }
    
    /**
     * 商品删除及相关信息
     *
     * @param   array $condition 列表条件
     * @return boolean
     */
    public function delGoodsAll($condition) {
        $goods_list = $this->getGoodsList($condition, 'goods_id,goods_commonid,store_id');
        if (empty($goods_list)) {
            return false;
        }
        $goodsid_array = array();
        $commonid_array = array();
        foreach ($goods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
            $commonid_array[] = $val['goods_commonid'];
            // 删除二维码
            unlink(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$val['store_id'].DS.$val['goods_id'].'.png');
        }
        $commonid_array = array_unique($commonid_array);
        
        // 删除商品表数据
        $this->delGoods(array('goods_id' => array('in', $goodsid_array)));
        // 删除商品公共表数据
        $this->table('goods_common')->where(array('goods_commonid' => array('in', $commonid_array)))->delete();
        // 删除商品图片表数据
        $this->delGoodsImages(array('goods_commonid' => array('in', $commonid_array)));
        // 删除属性关联表数据
        $this->table('goods_attr_index')->where(array('goods_id' => array('in', $goodsid_array)))->delete();
        // 删除买家收藏表数据
        $this->table('favorites')->where(array('fav_id' => array('in', $goodsid_array), 'fav_type' => 'goods'))->delete();
        // 删除优惠套装商品
        Model('p_bundling')->delBundlingGoods(array('goods_id' => array('in', $goodsid_array)));
        // 优惠套餐活动下架
        Model('p_bundling')->editBundlingCloseByGoodsIds(array('goods_id' => array('in', $goodsid_array)));
        // 推荐展位商品
        Model('p_booth')->delBoothGoods(array('goods_id' => array('in', $goodsid_array)));

        return true;
    }
    
    /**
     * 删除未锁定商品
     * @param unknown $condition
     */
    public function delGoodsNoLock($condition) {
        $condition['goods_lock'] = 0;
        $common_array = $this->getGoodsCommonList($condition, 'goods_commonid');
        $common_array = array_under_reset($common_array, 'goods_commonid');
        $commonid_array = array_keys($common_array);
        return $this->delGoodsAll(array('goods_commonid' => array('in', $commonid_array)));
    }
    
    /**
     * goods_show = 1 为出售中，goods_show = 0为未出售（仓库中，违规，等待审核）
     * 
     * @param string $field
     * @return string
     */
    private function _asGoodsShow($field) {
        return $field.',(goods_state=' . self::STATE1 . ' && goods_verify=' . self::VERIFY1 . ') as goods_show';
    }

     /**
      * 获得商品子分类的ID
      * @param array $condition
      * @return array 
      */
    private function _getRecursiveClass($condition){
        if (isset($condition['gc_id']) && !is_array($condition['gc_id'])) {
            $gc_list = H('goods_class') ? H('goods_class') : H('goods_class', true);
            if (!empty($gc_list[$condition['gc_id']])) {
                $gc_id[] = $condition['gc_id'];
                $gcchild_id = empty($gc_list[$condition['gc_id']]['child']) ? array() : explode(',', $gc_list[$condition['gc_id']]['child']);
                $gcchildchild_id = empty($gc_list[$condition['gc_id']]['childchild']) ? array() : explode(',', $gc_list[$condition['gc_id']]['childchild']);
                $gc_id = array_merge($gc_id, $gcchild_id, $gcchildchild_id);
                $condition['gc_id'] = array('in', $gc_id);
            }
        }
        return $condition;
    }
}
