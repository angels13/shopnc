<?php
/**
 * 会员店铺
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

class show_storeControl extends BaseStoreControl {
	public function __construct(){
		parent::__construct();
	}
	public function indexOp(){
		$goods_class = Model('goods');

        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];

        $model_goods = Model('goods'); // 字段
        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count";
		//得到最新12个商品列表
        $new_goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, 'goods_id desc', 12);
        Tpl::output('new_goods_list',$new_goods_list);

        $condition['goods_commend'] = 1;
		//得到12个推荐商品列表
        $recommended_goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, 'goods_id desc', 12);
        Tpl::output('recommended_goods_list',$recommended_goods_list);

		//幻灯片图片
        if($this->store_info['store_slide'] != '' && $this->store_info['store_slide'] != ',,,,'){
            Tpl::output('store_slide', explode(',', $this->store_info['store_slide']));
            Tpl::output('store_slide_url', explode(',', $this->store_info['store_slide_url']));
		}
		Tpl::output('page','index');
		Tpl::output('recommended_goods_list',$recommended_goods_list);
		
		Tpl::showpage('index');
	}

    public function show_articleOp() {
		//判断是否为导航页面
        $model_store_navigation = Model('store_navigation');
        $store_navigation_info = $model_store_navigation->getStoreNavigationInfo(array('sn_id' => intval($_GET['sn_id'])));
        if (!empty($store_navigation_info) && is_array($store_navigation_info)){
            Tpl::output('store_navigation_info',$store_navigation_info);
            Tpl::showpage('article');
        }
    }

	/**
	 * 全部商品
	 */
	public function goods_allOp(){

		$condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        if (trim($_GET['keyword']) != '') {
            $condition['goods_name'] = array('like', '%'.trim($_GET['keyword']).'%');
        }

		// 排序
        $order = $_GET['order'] == 1 ? 'asc' : 'desc';
		switch (trim($_GET['key'])){
			case '1':
				$order = 'goods_id '.$order;
				break;
			case '2':
				$order = 'goods_price '.$order;
				break;
			case '3':
				$order = 'goods_salenum '.$order;
				break;
			case '4':
				$order = 'goods_collect '.$order;
				break;
			case '5':
				$order = 'goods_click '.$order;
				break;
			default:
				$order = 'goods_id desc';
				break;
		}

		//查询分类下的子分类
		if (intval($_GET['stc_id']) > 0){
		    $condition['goods_stcids'] = array('like', '%,' . intval($_GET['stc_id']) . ',%');
		}

		$model_goods = Model('goods');
		$fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count";
		
        $recommended_goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order, 24);
        loadfunc('search');
        
		//输出分页
		Tpl::output('show_page',$model_goods->showpage('5'));
		$stc_class = Model('store_goods_class');
		$stc_info = $stc_class->getOneById(intval($_GET['stc_id']));
		Tpl::output('stc_name',$stc_info['stc_name']);
		Tpl::output('page','index');
		Tpl::output('recommended_goods_list',$recommended_goods_list);
		Tpl::showpage('goods_list');
	}

	/**
	 * ajax获取动态数量
	 */
	function ajax_store_trend_countOp(){
		$count = Model('store_sns_tracelog')->getStoreSnsTracelogCount(array('strace_storeid'=>$this->store_info['store_id']));
		echo json_encode(array('count'=>$count));exit;
	}
	/**
	 * ajax 店铺流量统计入库
	 */
	public function ajax_flowstat_recordOp(){
		if($_GET['store_id'] != '' && $_SESSION['store_id'] != $_GET['store_id']){
			//确定统计分表名称
			$flow_tableid = 0;
			$len = strlen(strval(intval($_GET['store_id'])));
			$last_num = substr(strval(intval($_GET['store_id'])), $len-1,1);
			switch ($last_num){
				case 1:
					$flow_tableid = 1;
					break;
				case 2:
					$flow_tableid = 1;
					break;
				case 3:
					$flow_tableid = 2;
					break;
				case 4:
					$flow_tableid = 2;
					break;
				case 5:
					$flow_tableid = 3;
					break;
				case 6:
					$flow_tableid = 3;
					break;
				case 7:
					$flow_tableid = 4;
					break;
				case 8:
					$flow_tableid = 4;
					break;
				case 9:
					$flow_tableid = 5;
					break;
				case 0:
					$flow_tableid = 5;
					break;
			}
			$flow_tablename = 'flowstat_'.$flow_tableid; 
			//判断是否存在当日数据信息
			$date = date('Ymd',time());
			$model = Model();
			$stat_model = Model('statistics');
			if($_GET['act_param'] == 'show_store' && ($_GET['op_param'] == 'index' || $_GET['op_param'] == 'credit' || $_GET['op_param'] == 'store_info')){
				$flow_date_array = $model->table($flow_tablename)->where(array('date'=>$date,'store_id'=>intval($_GET['store_id'])))->find();
			}else if($_GET['act_param'] == 'goods' && $_GET['op_param'] == 'index'){
				$flow_date_array = $model->table($flow_tablename)->where(array('date'=>$date,'goods_id'=>intval($_GET['goods_id'])))->find();
				$flow_date_array_sub = $model->table($flow_tablename)->where(array('date'=>$date,'store_id'=>intval($_GET['store_id'])))->find();
			}
			//向数据库写入访问量数据
			$update_param = array();
			$update_param['table'] = $flow_tablename;
			$update_param['field'] = 'clicknum';
			$update_param['value'] = 1;
			if(is_array($flow_date_array) && !empty($flow_date_array)){//已经存在数据则更新
				if($_GET['act_param'] == 'show_store' && ($_GET['op_param'] == 'index' || $_GET['op_param'] == 'credit' || $_GET['op_param'] == 'store_info')){
					$update_param['where'] = "WHERE date = '".$date."' AND store_id = '".intval($_GET['store_id'])."' AND goods_id = '0'";
					$stat_model->updatestat($update_param);
				}else if($_GET['act_param'] == 'goods' && $_GET['op_param'] == 'index'){
					$update_param['where'] = "WHERE date = '".$date."' AND goods_id = '".intval($_GET['goods_id'])."'";
					$stat_model->updatestat($update_param);
					$update_param['where'] = "WHERE date = '".$date."' AND store_id = '".intval($_GET['store_id'])."' AND goods_id = '0'";
					$stat_model->updatestat($update_param);
				}
			}else{//未存在数据则插入一行访问量数据
				if($_GET['act_param'] == 'show_store' && ($_GET['op_param'] == 'index' || $_GET['op_param'] == 'credit' || $_GET['op_param'] == 'store_info')){
					$model->table($flow_tablename)->insert(array('date'=>$date,'clicknum'=>1,'store_id'=>intval($_GET['store_id']),'type'=>'sum','goods_id'=>0));
				}else if($_GET['act_param'] == 'goods' && $_GET['op_param'] == 'index'){
					if(is_array($flow_date_array_sub) && !empty($flow_date_array_sub)){//已经有店铺数据则只插入一行并更新店铺访问数据
						$model->table($flow_tablename)->insert(array('date'=>$date,'clicknum'=>1,'store_id'=>intval($_GET['store_id']),'type'=>'goods','goods_id'=>intval($_GET['goods_id'])));
						$update_param['where'] = "WHERE date = '".$date."' AND store_id = '".intval($_GET['store_id'])."' AND goods_id = '0'";
						$stat_model->updatestat($update_param);
					}else{//没有店铺访问数据的则建立两行访问数据
						$model->table($flow_tablename)->insert(array('date'=>$date,'clicknum'=>1,'store_id'=>intval($_GET['store_id']),'type'=>'sum','goods_id'=>0));
						$model->table($flow_tablename)->insert(array('date'=>$date,'clicknum'=>1,'store_id'=>intval($_GET['store_id']),'type'=>'goods','goods_id'=>intval($_GET['goods_id'])));
					}
				}
			}
		}
		echo json_encode(array('done'=>true,'msg'=>'done'));
	}
}
?>
