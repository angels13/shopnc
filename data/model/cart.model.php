<?php
/**
 * 购物车管理
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
class cartModel extends Model {

    /**
     * 购物车商品总金额
     */
    private $cart_all_price = 0;

    /**
     * 购物车商品总数
     */
    private $cart_goods_num = 0;

    public function __construct() {
       parent::__construct('cart'); 
    }

    /**
     * 取属性值魔术方法
     *
     * @param string $name
     */
    public function __get($name) {
        return $this->$name;
    }

	/**
	 * 检查购物车内商品是否存在
	 *
	 * @param
	 */
	public function checkCart($condition = array()) {
	    return $this->where($condition)->find();
	}
	
	/**
	 * 取得 单条购物车信息
	 * @param unknown $condition
	 * @param string $field
	 */
	public function getCartInfo($condition = array(), $field = '*') {
	   return $this->field($field)->where($condition)->find();    
	}

	/**
	 * 将商品添加到购物车中
	 *
	 * @param array	$data	商品数据信息
	 * @param string $save_type 保存类型，可选值 db,cookie,cache
	 * @param int $quantity 购物数量
	 */	
	public function addCart($data = array(), $save_type = '', $quantity = null) {
        $method = '_addCart'.ucfirst($save_type);
	    $insert = $this->$method($data,$quantity);
	    //更改购物车总商品数和总金额，传递数组参数只是给DB使用
	    $this->getCartNum($save_type,array('buyer_id'=>$data['buyer_id']));
	    return $insert;
	}

	/**
	 * 添加数据库购物车
	 *
	 * @param unknown_type $goods_info
	 * @param unknown_type $quantity
	 * @return unknown
	 */
	private function _addCartDb($goods_info = array(),$quantity) {
	    //验证购物车商品是否已经存在
	    $condition = array();
	    $condition['goods_id'] = $goods_info['goods_id'];
	    $condition['buyer_id'] = $goods_info['buyer_id'];
	    if (isset($goods_info['bl_id'])) {
	        $condition['bl_id'] = $goods_info['bl_id'];   
	    } else {
	        $condition['bl_id'] = 0;
	    }
    	$check_cart	= $this->checkCart($condition);
    	if (!empty($check_cart)) return true;     

		$array    = array();
		$array['buyer_id']	= $goods_info['buyer_id'];
		$array['store_id']	= $goods_info['store_id'];
		$array['goods_id']	= $goods_info['goods_id'];
		$array['goods_name'] = $goods_info['goods_name'];
		$array['goods_price'] = $goods_info['goods_price'];
		$array['goods_num']   = $quantity;
		$array['goods_image'] = $goods_info['goods_image'];
		$array['store_name'] = $goods_info['store_name'];
		$array['bl_id'] = isset($goods_info['bl_id']) ? $goods_info['bl_id'] : 0;
		return $this->insert($array);
	}

	/**
	 * 添加到缓存购物车
	 *
	 * @param unknown_type $goods_info
	 * @param unknown_type $quantity
	 * @return unknown
	 */
	private function _addCartCache($goods_info = array(), $quantity = null) {
        $obj_cache = Cache::getInstance(C('cache.type'));
        $cart_array = $obj_cache->get($_COOKIE['PHPSESSID'],'cart_');
        $cart_array = @unserialize($cart_array);
    	$cart_array = !is_array($cart_array) ? array() : $cart_array;
    	if (count($cart_array) >= 5) return true;
        if (in_array($goods_info['goods_id'],array_keys($cart_array))) return true;
		$cart_array[$goods_info['goods_id']] = array(
		  'store_id' => $goods_info['store_id'],
		  'goods_id' => $goods_info['goods_id'],
		  'goods_name' => $goods_info['goods_name'],
		  'goods_price' => $goods_info['goods_price'],
		  'goods_image' => $goods_info['goods_image'],
		  'goods_num' => $quantity
		);
        $obj_cache->set($_COOKIE['PHPSESSID'], serialize($cart_array), 'cart_', 24*3600);
        return true;
	}

	/**
	 * 添加到cookie购物车,最多保存5个商品
	 *
	 * @param unknown_type $goods_info
	 * @param unknown_type $quantity
	 * @return unknown
	 */
	private function _addCartCookie($goods_info = array(), $quantity = null) {
    	//去除斜杠
    	$cart_str = get_magic_quotes_gpc() ? stripslashes(cookie('cart')) : cookie('cart');
    	$cart_str = base64_decode(decrypt($cart_str));
    	$cart_array = @unserialize($cart_str);
    	$cart_array = !is_array($cart_array) ? array() : $cart_array;
    	if (count($cart_array) >= 5) return false;

    	if (in_array($goods_info['goods_id'],array_keys($cart_array))) return true;
		$cart_array[$goods_info['goods_id']] = array(
		  'store_id' => $goods_info['store_id'],
		  'goods_id' => $goods_info['goods_id'],
		  'goods_name' => $goods_info['goods_name'],
		  'goods_price' => $goods_info['goods_price'],
		  'goods_image' => $goods_info['goods_image'],
		  'goods_num' => $quantity
		);
		setNcCookie('cart',encrypt(base64_encode(serialize($cart_array))),24*3600);
		return true;
	}

	/**
	 * 更新购物车 
	 *
	 * @param	array	$param 商品信息
	 */	
	public function editCart($data,$condition) {
		$result	= $this->where($condition)->update($data);
		if ($result) {
		    $this->getCartNum('db',array('buyer_id'=>$condition['buyer_id']));
		}
		return $result;
	}

	/**
	 * 购物车列表 
	 *
	 * @param string $type 存储类型 db,cache,cookie
	 * @param unknown_type $condition
	 */	
	public function listCart($type, $condition = array()) {
        if ($type == 'db') {
    		$cart_list = $this->where($condition)->select(array('cache'=>false));
        } elseif ($type == 'cache') {
            $obj_cache = Cache::getInstance(C('cache.type'));
            $cart_list = $obj_cache->get($_COOKIE['PHPSESSID'],'cart_');
            $cart_list = @unserialize($cart_list);
        } elseif ($type == 'cookie') {
        	//去除斜杠
        	$cart_str = get_magic_quotes_gpc() ? stripslashes(cookie('cart')) : cookie('cart');
        	$cart_str = base64_decode(decrypt($cart_str));
        	$cart_list = @unserialize($cart_str);
        }
        $cart_list = is_array($cart_list) ? $cart_list : array();
        //顺便设置购物车商品数和总金额
		$this->cart_goods_num =  count($cart_list);
	    $cart_all_price = 0;
		if(is_array($cart_list)) {
			foreach ($cart_list as $val) {
				$cart_all_price	+= $val['goods_price'] * $val['goods_num'];
			}
		}
        $this->cart_all_price = ncPriceFormat($cart_all_price);
		return !is_array($cart_list) ? array() : $cart_list;
	}

	/**
	 * 删除购物车商品
	 * 
	 * @param string $type 存储类型 db,cache,cookie
	 * @param unknown_type $condition
	 */
	public function delCart($type, $condition = array()) {
	    if ($type == 'db') {
    		$result =  $this->where($condition)->delete();
	    } elseif ($type == 'cache') {
	        $obj_cache = Cache::getInstance(C('cache.type'));
	        $cart_array = $obj_cache->get($_COOKIE['PHPSESSID'],'cart_');
	        $cart_array = @unserialize($cart_array);
	        if (!is_array($cart_array)) return true;
	        if (key_exists($condition['goods_id'],$cart_array)) {
	            unset($cart_array[$condition['goods_id']]);
                $obj_cache = Cache::getInstance(C('cache.type'));
                $obj_cache->set($_COOKIE['PHPSESSID'], serialize($cart_array), 'cart_', 24*3600);
	            $result = true;
	        }
	    } elseif ($type == 'cookie') {        	
        	$cart_str = get_magic_quotes_gpc() ? stripslashes(cookie('cart')) : cookie('cart');
        	$cart_str = base64_decode(decrypt($cart_str));
        	$cart_array = @unserialize($cart_str);
            if (key_exists($condition['goods_id'],(array)$cart_array)) {
                unset($cart_array[$condition['goods_id']]);
            }
            setNcCookie('cart',encrypt(base64_encode(serialize($cart_array))),24*3600);
            $result = true;
	    }
	    //重新计算购物车商品数和总金额
		if ($result) {
		    $this->getCartNum($type,array('buyer_id'=>$condition['buyer_id']));
		}
		return $result;
	}

	/**
	 * 清空购物车
	 *
	 * @param string $type 存储类型 db,cache,cookie
	 * @param unknown_type $condition
	 */
	public function clearCart($type, $condition = array()) {
	    if ($type == 'cache') {
            $obj_cache = Cache::getInstance(C('cache.type'));
            $obj_cache->rm($_COOKIE['PHPSESSID'],'cart_');
	    } elseif ($type == 'cookie') {
            setNcCookie('cart','',-3600);
	    } else if ($type == 'db') {
	        //数据库暂无浅清空操作
	    }
	}

	/**
	 * 计算购物车总商品数和总金额 
	 * @param string $type 购物车信息保存类型 db,cookie,cache
	 * @param array $condition 只有登录后操作购物车表时才会用到该参数
	 */		
	public function getCartNum($type, $condition = array()) {
	    if ($type == 'db') {
    	    $cart_all_price = 0;
    		$cart_goods	= $this->listCart('db',$condition);
    		$this->cart_goods_num = count($cart_goods);
    		if(!empty($cart_goods) && is_array($cart_goods)) {
    			foreach ($cart_goods as $val) {
    				$cart_all_price	+= $val['goods_price'] * $val['goods_num'];
    			}
    		}
		  $this->cart_all_price = ncPriceFormat($cart_all_price);
	        
	    } elseif ($type == 'cache') {
            $obj_cache = Cache::getInstance(C('cache.type'));
            $cart_array = $obj_cache->get($_COOKIE['PHPSESSID'],'cart_');
            $cart_array = @unserialize($cart_array);
        	$cart_array = !is_array($cart_array) ? array() : $cart_array;
    		$this->cart_goods_num = count($cart_array);
    		$cart_all_price = 0;
    		if (!empty($cart_array)){
    			foreach ($cart_array as $v){
    				$cart_all_price += floatval($v['goods_price'])*intval($v['goods_num']);
    			}
    		}
    		$this->cart_all_price = $cart_all_price;

	    } elseif ($type == 'cookie') {
        	$cart_str = get_magic_quotes_gpc() ? stripslashes(cookie('cart')) : cookie('cart');
        	$cart_str = base64_decode(decrypt($cart_str));
        	$cart_array = @unserialize($cart_str);
        	$cart_array = !is_array($cart_array) ? array() : $cart_array;
    		$this->cart_goods_num = count($cart_array);
    		$cart_all_price = 0;
    		foreach ($cart_array as $v){
    			$cart_all_price += floatval($v['goods_price'])*intval($v['goods_num']);
    		}
    		$this->cart_all_price = $cart_all_price;
	    }
	    setNcCookie('cart_goods_num',$this->cart_goods_num,2*3600);
	    return $this->cart_goods_num;
	}

	/**
	 * 直接购买/加入购物车时，判断商品是不是限时折扣中，如果购买数量若>=规定的下限，按折扣价格计算,否则按原价计算
	 * @param unknown $buy_goods_list
	 * @param number $quantity 购买数量
	 * @return array,如果该商品未正在进行限时折扣，返回空数组
	 */
	public function getXianshiInfo($buy_goods_info, $quantity) {
	    if (!C('promotion_allow') || empty($buy_goods_info) || !is_array($buy_goods_info)) return $buy_goods_info;
	    //定义返回数组
	    $xianshi_info = Model('p_xianshi_goods')->getXianshiGoodsInfoByGoodsID($buy_goods_info['goods_id']);
	    if (!empty($xianshi_info)) {
	        if ($quantity >= $xianshi_info['lower_limit']) {
	            $buy_goods_info['goods_price'] = $xianshi_info['xianshi_price'];
	            $buy_goods_info['promotions_id'] = $xianshi_info['xianshi_id'];
	            $buy_goods_info['ifxianshi'] = true;
	        }
	    }
	    return $buy_goods_info;
	}

	/**
	 * 直接购买时，判断商品是不是正在团购中，如果是，按团购价格计算，购买数量若超过团购规定的上限，则按团购上限计算
	 * @param unknown $buy_goods_info
	 * @return array,如果该商品未正在进行团购，返回空数组
	 */
	public function getGroupbuyInfo($buy_goods_info = array()) {
	    if (!C('groupbuy_allow') || empty($buy_goods_info) || !is_array($buy_goods_info)) return $buy_goods_info;
	    $groupbuy_info = Model('groupbuy')->getGroupbuyInfoByGoodsCommonID($buy_goods_info['goods_commonid']);
	    if (!empty($groupbuy_info)) {
	        $buy_goods_info['goods_price'] = $groupbuy_info['groupbuy_price'];
	        if ($groupbuy_info['upper_limit'] && $buy_goods_info['goods_num'] > $groupbuy_info['upper_limit']) {
	            $buy_goods_info['goods_num'] = $groupbuy_info['upper_limit'];
	        }
	        $buy_goods_info['promotions_id'] = $buy_goods_info['groupbuy_id'] = $groupbuy_info['groupbuy_id'];
	        $buy_goods_info['ifgroupbuy'] = true;
	    }
	    return $buy_goods_info;
	}

	/**
	 * 直接购买时返回最新的在售商品信息（需要在售）
	 *
	 * @param int $goods_id 所购商品ID
	 * @param int $quantity 购买数量
	 * @return array
	 */
	public function getGoodsOnlineInfo($goods_id,$quantity) {
	    //取目前在售商品
	    $goods_info = Model('goods')->getGoodsOnlineInfo(array('goods_id'=>$goods_id));
	    if(empty($goods_info)){
            return null;
	    }
	    $new_array = array();
	    $new_array['goods_num'] = $quantity;
	    $new_array['goods_id'] = $goods_id;
	    $new_array['goods_commonid'] = $goods_info['goods_commonid'];
	    $new_array['gc_id'] = $goods_info['gc_id'];
	    $new_array['store_id'] = $goods_info['store_id'];
	    $new_array['goods_name'] = $goods_info['goods_name'];
	    $new_array['goods_price'] = $goods_info['goods_price'];
	    $new_array['store_name'] = $goods_info['store_name'];
	    $new_array['goods_image'] = $goods_info['goods_image'];
	    $new_array['transport_id'] = $goods_info['transport_id'];
	    $new_array['goods_freight'] = $goods_info['goods_freight'];
	    $new_array['goods_vat'] = $goods_info['goods_vat'];
	    $new_array['goods_storage'] = $goods_info['goods_storage'];
	    $new_array['state'] = true;
	    $new_array['storage_state'] = intval($goods_info['goods_storage']) < intval($quantity) ? false : true;

	    //填充必要下标，方便后面统一使用购物车方法与模板
	    //cart_id=goods_id,优惠套装目前只能进购物车,不能立即购买
	    $new_array['cart_id'] = $goods_id;
	    $new_array['bl_id'] = 0;
	    return $new_array;
	}

	/**
	 * 取商品最新的在售信息
	 * @param unknown $cart_list
	 * @return array
	 */
	public function getOnlineCartList($cart_list) {
	    if (empty($cart_list) || !is_array($cart_list)) return $cart_list;
	    //验证商品是否有效
	    $goods_id_array = array();
	    foreach ($cart_list as $key => $cart_info) {
	        if (!intval($cart_info['bl_id'])) {
	            $goods_id_array[] = $cart_info['goods_id'];
	        }
	    }
	    $model_goods = Model('goods');
	    $goods_online_list = $model_goods->getGoodsOnlineList(array('goods_id'=>array(in,$goods_id_array)));
	    $goods_online_array = array();
	    foreach ($goods_online_list as $goods) {
	        $goods_online_array[$goods['goods_id']] = $goods;
	    }
	    foreach ((array)$cart_list as $key => $cart_info) {
	        if (intval($cart_info['bl_id'])) continue;
	        $cart_list[$key]['state'] = true;
	        $cart_list[$key]['storage_state'] = true;
	        if (in_array($cart_info['goods_id'],array_keys($goods_online_array))) {
                $goods_online_info = $goods_online_array[$cart_info['goods_id']];
                $cart_list[$key]['goods_name'] = $goods_online_info['goods_name'];
                $cart_list[$key]['gc_id'] = $goods_online_info['gc_id'];
                $cart_list[$key]['goods_image'] = $goods_online_info['goods_image'];
                $cart_list[$key]['goods_price'] = $goods_online_info['goods_price'];
                $cart_list[$key]['transport_id'] = $goods_online_info['transport_id'];
                $cart_list[$key]['goods_freight'] = $goods_online_info['goods_freight'];
                $cart_list[$key]['goods_vat'] = $goods_online_info['goods_vat'];
                $cart_list[$key]['goods_storage'] = $goods_online_info['goods_storage'];
                if ($cart_info['goods_num'] > $goods_online_info['goods_storage']) {
                    $cart_list[$key]['storage_state'] = false;
                }
	        } else {
	            //如果商品下架
	            $cart_list[$key]['state'] = false;
	            $cart_list[$key]['storage_state'] = false;
	        }
	    }
	    return $cart_list;
	}

	/**
	 * 批量判断购物车内的商品是不是限时折扣中，如果购买数量若>=规定的下限，按折扣价格计算,否则按原价计算
	 * 并标识该商品为限时商品
	 * @param unknown $cart_list
	 * @return array
	 */
	public function getXianshiCartList($cart_list) {
	    if (!C('promotion_allow') || empty($cart_list) || !is_array($cart_list)) return $cart_list;
	    $model_xianshi = Model('p_xianshi_goods');
	    $model_goods = Model('goods');
        foreach ($cart_list as $key => $cart_info) {
            if (intval($cart_info['bl_id'])) continue;
            $xianshi_info = $model_xianshi->getXianshiGoodsInfoByGoodsID($cart_info['goods_id']);
            if (!empty($xianshi_info)) {
                if ($cart_info['goods_num'] >= $xianshi_info['lower_limit']) {
                    $cart_list[$key]['goods_price'] = $xianshi_info['xianshi_price'];
                    $cart_list[$key]['promotions_id'] = $xianshi_info['xianshi_id'];
                    $cart_list[$key]['ifxianshi'] = true;
                }
                $cart_list[$key]['xianshi_info']['lower_limit'] = $xianshi_info['lower_limit'];
                $cart_list[$key]['xianshi_info']['xianshi_price'] = $xianshi_info['xianshi_price'];
                $cart_list[$key]['xianshi_info']['down_price'] = ncPriceFormat($cart_info['goods_price'] - $xianshi_info['xianshi_price']);
            }
        }
	    return $cart_list;
	}

	/**
	 * 取得购买车内组合销售信息以及包含的商品及有效状态
	 * @param unknown $cart_list
	 * @return array
	 */
	public function getBundlingCartList($cart_list) {
	    if (!C('promotion_allow') || empty($cart_list) || !is_array($cart_list)) return $cart_list;
	    $model_bl = Model('p_bundling');
	    $model_goods = Model('goods');
        foreach ($cart_list as $key => $cart_info) {
            if (!intval($cart_info['bl_id'])) continue;
            $cart_list[$key]['state'] = true;
            $cart_list[$key]['storage_state'] = true;
            $bl_info = $model_bl->getBundlingInfo(array('bl_id'=>$cart_info['bl_id']));

            //标志优惠套装是否处于有效状态
            if (empty($bl_info) || !intval($bl_info['bl_state'])) {
                $cart_list[$key]['state'] = false;
            }

            //取得优惠套装商品列表
            $cart_list[$key]['bl_goods_list'] = $model_bl->getBundlingGoodsList(array('bl_id'=>$cart_info['bl_id']));

            //取最新在售商品信息
            $goods_id_array = array();
            foreach ($cart_list[$key]['bl_goods_list'] as $goods_info) {
                $goods_id_array[] = $goods_info['goods_id'];
            }
            $goods_list = $model_goods->getGoodsOnlineList(array('goods_id'=>array(in,$goods_id_array)));
            $goods_online_list = array();
            foreach ($goods_list as $goods_info) {
                $goods_online_list[$goods_info['goods_id']] = $goods_info;
            }
            unset($goods_list);

            //使用最新的商品名称、图片,如果一旦有商品下架，则整个套装置置为无效状态
            foreach ($cart_list[$key]['bl_goods_list'] as $k => $goods_info) {
                if (array_key_exists($goods_info['goods_id'],$goods_online_list)) {
                    $goods_online_info = $goods_online_list[$goods_info['goods_id']];
                    //如果库存不足，标识false
                    if ($cart_info['goods_num'] > $goods_online_info['goods_storage']) {
                        $cart_list[$key]['storage_state'] = false;
                    }
                    $cart_list[$key]['bl_goods_list'][$k]['goods_name'] = $goods_online_info['goods_name'];
                    $cart_list[$key]['bl_goods_list'][$k]['goods_image'] = $goods_online_info['goods_image'];
                    $cart_list[$key]['bl_goods_list'][$k]['goods_storage'] = $goods_online_info['goods_storage'];
                } else {
                    //商品已经下架
                    $cart_list[$key]['state'] = false;
                    $cart_list[$key]['storage_state'] = false;
                }
            }
        }
	    return $cart_list;
	}

	/**
	 * 从购物车数组中得到商品列表
	 * @param unknown $cart_list
	 */
	public function getGoodsList($cart_list) {
	    if (empty($cart_list) || !is_array($cart_list)) return $cart_list;
	    $goods_list = array();
	    $i = 0;
	    foreach ($cart_list as $key => $cart) {
	        if (!$cart['state'] || !$cart['storage_state']) continue;
	        //购买数量
	        $quantity = $cart['goods_num'];
	        if (!intval($cart['bl_id'])) {
	            //如果是普通商品
	            $goods_list[$i]['goods_num'] = $quantity;
	            $goods_list[$i]['goods_id'] = $cart['goods_id'];
	            $goods_list[$i]['store_id'] = $cart['store_id'];
	            $goods_list[$i]['gc_id'] = $cart['gc_id'];
	            $goods_list[$i]['goods_name'] = $cart['goods_name'];
	            $goods_list[$i]['goods_price'] = $cart['goods_price'];
	            $goods_list[$i]['store_name'] = $cart['store_name'];
	            $goods_list[$i]['goods_image'] = $cart['goods_image'];
	            $goods_list[$i]['transport_id'] = $cart['transport_id'];
	            $goods_list[$i]['goods_freight'] = $cart['goods_freight'];
	            $goods_list[$i]['goods_vat'] = $cart['goods_vat'];
	            $goods_list[$i]['bl_id'] = 0;
	            $i++;
	        } else {
	            //如果是优惠套装商品
	            foreach ($cart['bl_goods_list'] as $bl_goods) {
	                $goods_list[$i]['goods_num'] = $quantity;
	                $goods_list[$i]['goods_id'] = $bl_goods['goods_id'];
	                $goods_list[$i]['store_id'] = $cart['store_id'];
	                $goods_list[$i]['gc_id'] = $bl_goods['gc_id'];
	                $goods_list[$i]['goods_name'] = $bl_goods['goods_name'];
	                $goods_list[$i]['goods_price'] = $bl_goods['goods_price'];
	                $goods_list[$i]['store_name'] = $bl_goods['store_name'];
	                $goods_list[$i]['goods_image'] = $bl_goods['goods_image'];
	                $goods_list[$i]['transport_id'] = $bl_goods['transport_id'];
	                $goods_list[$i]['goods_freight'] = $bl_goods['goods_freight'];
	                $goods_list[$i]['goods_vat'] = $bl_goods['goods_vat'];
	                $goods_list[$i]['bl_id'] = $cart['bl_id'];
	                $i++;
	            }
	        }
	    }
	    return $goods_list;
	}

	/**
	 * 将下单商品列表转换为以店铺ID为下标的数组
	 *
	 * @param array $cart_list
	 * @return array
	 */
	public function getStoreCartList($cart_list) {
	    if (empty($cart_list) || !is_array($cart_list)) return $cart_list;
	    $new_array = array();
	    foreach ($cart_list as $cart) {
	        $new_array[$cart['store_id']][] = $cart;
	    }
	    return $new_array;
	}

	/**
	 * 商品金额计算(分别对每个商品/优惠套装小计、每个店铺小计)
	 * @param unknown $store_cart_list 以店铺ID分组的购物车商品信息
	 * @return array
	 */
	public function calcCartList($store_cart_list) {
	    if (empty($store_cart_list) || !is_array($store_cart_list)) return array($store_cart_list,array(),0);
	
	    //存放每个店铺的商品总金额
	    $store_goods_total = array();
	    //存放本次下单所有店铺商品总金额
	    $order_goods_total = 0;
	
	    foreach ($store_cart_list as $store_id => $store_cart) {
	        $tmp_amount = 0;
	        foreach ($store_cart as $key => $cart_info) {
	            $store_cart[$key]['goods_total'] = ncPriceFormat($cart_info['goods_price'] * $cart_info['goods_num']);
	            $store_cart[$key]['goods_image_url'] = cthumb($store_cart[$key]['goods_image']);
	            $tmp_amount += $store_cart[$key]['goods_total'];
	        }
	        $store_cart_list[$store_id] = $store_cart;
	        $store_goods_total[$store_id] = ncPriceFormat($tmp_amount);
	    }
	    return array($store_cart_list,$store_goods_total);
	}

	/**
	 * 取得店铺级活动 - 每个店铺可用的满即送活动规则列表
	 * @param unknown $store_id_array 店铺ID数组
	 */
	public function getMansongRuleList($store_id_array) {
	    if (!C('promotion_allow') || empty($store_id_array) || !is_array($store_id_array)) return array();
        $model_mansong = Model('p_mansong');
        $mansong_rule_list = array();
        foreach ($store_id_array as $store_id) {
            $store_mansong_rule = $model_mansong->getMansongInfoByStoreID($store_id);
            if (!empty($store_mansong_rule['rules']) && is_array($store_mansong_rule['rules'])) {
                foreach ($store_mansong_rule['rules'] as $rule_info) {
                    //如果减金额 或 有赠品(在售且有库存)
                    if (!empty($rule_info['discount']) || (!empty($rule_info['mansong_goods_name']) && !empty($rule_info['goods_storage']))) {
                        $mansong_rule_list[$store_id][] = $this->_parseMansongRuleDesc($rule_info);
                    }
                }
            }
        }
        return $mansong_rule_list;
    }

	/**
	 * 取得店铺级优惠 - 跟据商品金额返回每个店铺当前符合的一条活动规则，如果有赠品，则自动追加到购买列表，价格为0
	 * @param unknown $store_goods_total 每个店铺的商品金额小计，以店铺ID为下标
	 * @return array($premiums_list,$mansong_rule_list) 分别为赠品列表[下标自增]，店铺满送规则列表[店铺ID为下标]
	 */
	public function getMansongRuleCartListByTotal($store_goods_total) {
	    if (!C('promotion_allow') || empty($store_goods_total) || !is_array($store_goods_total)) return array(array(),array());

        $model_mansong = Model('p_mansong');
        $model_goods = Model('goods');

        //定义赠品数组，下标为店铺ID
        $premiums_list = array();
        //定义满送活动数组，下标为店铺ID
        $mansong_rule_list = array();

        foreach ($store_goods_total as $store_id => $goods_total) {
            $rule_info = $model_mansong->getMansongRuleByStoreID($store_id,$goods_total);
            if (is_array($rule_info) && !empty($rule_info)) {
                //即不减金额，也找不到促销商品时(已下架),此规则无效
                if (empty($rule_info['discount']) && empty($rule_info['mansong_goods_name'])) {
                    continue;
                }
                $rule_info['desc'] = $this->_parseMansongRuleDesc($rule_info);
                $rule_info['discount'] = ncPriceFormat($rule_info['discount']);
                $mansong_rule_list[$store_id] = $rule_info;
                //如果赠品在售,有库存,则追加到购买列表
                if (!empty($rule_info['mansong_goods_name']) && !empty($rule_info['goods_storage'])) {
                    $data = array();
                    $data['goods_id'] = $rule_info['goods_id'];
                    $data['goods_name'] = $rule_info['mansong_goods_name'];
                    $data['goods_num'] = 1;
                    $data['goods_price'] = 0.00;
                    $data['goods_image'] = $rule_info['goods_image'];
                    $data['goods_image_url'] = cthumb($rule_info['goods_image']);
                    $data['goods_storage'] = $rule_info['goods_storage'];
                    $premiums_list[$store_id][] = $data;
                }
            }
        }
        return array($premiums_list,$mansong_rule_list);
	}

	/**
	 * 拼装单条满即送规则页面描述信息
	 * @param array $rule_info 满即送单条规则信息 
	 * @return string
	 */
	private function _parseMansongRuleDesc($rule_info) {
	    if (empty($rule_info) || !is_array($rule_info)) return;
	    $discount_desc = !empty($rule_info['discount']) ? '减'.$rule_info['discount'] : '';
	    $goods_desc = (!empty($rule_info['mansong_goods_name']) && !empty($rule_info['goods_storage'])) ?
	    " 送<a href='".urlShop('goods','index',array('goods_id'=>$rule_info['goods_id']))."' title='{$rule_info['mansong_goods_name']}' target='_blank'>[赠品]</a>" : '';
	     return sprintf('满%s%s%s',$rule_info['price'],$discount_desc,$goods_desc);	    
	}

	/**
	 * 重新计算每个店铺最终商品总金额(最初计算金额减去各种优惠/加运费)
	 * @param array $store_goods_total 店铺商品总金额
	 * @param array $preferential_array 店铺优惠活动内容
	 * @param string $preferential_type 优惠类型，目前只有一个 'mansong'
	 * @return array 返回扣除优惠后的店铺商品总金额
	 */
	public function reCalcGoodsTotal($store_goods_total, $preferential_array, $preferential_type) {
	   $deny = empty($store_goods_total) || !is_array($store_goods_total) || empty($preferential_array) || !is_array($preferential_array);
	   if ($deny) return $store_goods_total;

        switch ($preferential_type) {
            case 'mansong':
                if (!C('promotion_allow')) return $store_goods_total;
                foreach ($preferential_array as $store_id => $rule_info) {
                    if (is_array($rule_info) && $rule_info['discount'] > 0) {
                        $store_goods_total[$store_id] -= $rule_info['discount'];
                    }
                }
                break;

            case 'voucher':
                if (!C('voucher_allow')) return $store_goods_total;
                foreach ($preferential_array as $store_id => $voucher_info) {
                    $store_goods_total[$store_id] -= $voucher_info['voucher_price'];
                }
                break;

            case 'freight':
                foreach ($preferential_array as $store_id => $freight_total) {
                    $store_goods_total[$store_id] += $freight_total;
                }
                break;
            }
	    return $store_goods_total;
	}

	/**
	 * 取得哪些店铺有满免运费活动
	 * @param array $store_id_array 店铺ID数组
	 * @return array
	 */
	public function getFreeFreightActiveList($store_id_array) {
	    if (empty($store_id_array) || !is_array($store_id_array)) return array();

	    //定义返回数组
	    $store_free_freight_active = array();

	    //如果商品金额未达到免运费设置下线，则需要计算运费
	    $condition = array('store_id' => array('in',$store_id_array));
	    $store_list = Model('store')->getStoreOnlineList($condition,null,'','store_id,store_free_price');
	    foreach ($store_list as $store_info) {
	        $limit_price = floatval($store_info['store_free_price']);
	        if ($limit_price > 0) {
	            $store_free_freight_active[$store_info['store_id']] = sprintf('满%s免运费',$limit_price);
	        }
	    }
	    return $store_free_freight_active;
	}

	/**
	 * 验证传过来的代金券是否可用有效，如果无效，直接删除
	 * @param array $input_voucher_list 代金券列表
	 * @param array $store_goods_total (店铺ID=>商品总金额)
	 * @return array
	 */
	public function reParseVoucherList($input_voucher_list = array(), $store_goods_total = array(), $member_id) {
	    if (empty($input_voucher_list) || !is_array($input_voucher_list)) return array();
        $store_voucher_list = $this->getStoreAvailableVoucherList($store_goods_total, $member_id);
        foreach ($input_voucher_list as $store_id => $voucher) {
            $tmp = $store_voucher_list[$store_id];
            if (is_array($tmp) && isset($tmp[$voucher['voucher_t_id']])) {
                $input_voucher_list[$store_id]['voucher_id'] = $tmp[$voucher['voucher_t_id']]['voucher_id'];
                $input_voucher_list[$store_id]['voucher_code'] = $tmp[$voucher['voucher_t_id']]['voucher_code'];
            } else {
                unset($input_voucher_list[$store_id]);
            }
        }
        return $input_voucher_list;
	}

	/**
	 * 取得店铺可用的代金券
	 * @param array $store_goods_total array(店铺ID=>商品总金额)
	 * @return array
	 */
    public function getStoreAvailableVoucherList($store_goods_total, $member_id) {
        if (!C('voucher_allow')) return $store_goods_total;
        $voucher_list = array();
        $model_voucher = Model('voucher');
        foreach ($store_goods_total as $store_id => $goods_total) {
            $condition = array();
            $condition['voucher_store_id'] = $store_id;
            $condition['voucher_owner_id'] = $member_id;
            $voucher_list[$store_id] = $model_voucher->getCurrentAvailableVoucher($condition,$goods_total);
        }
        return $voucher_list;
    }
}
