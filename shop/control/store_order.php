<?php
/**
 * 卖家订单管理
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
class store_orderControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 订单列表
	 *
	 */
	public function indexOp() {
        $model_order = Model('order');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if ($_GET['order_sn'] != '') {
            $condition['order_sn'] = $_GET['order_sn'];
        }
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $allow_state_array = array('state_new','state_pay','state_send','state_success','state_cancel');
        if (in_array($_GET['state_type'],$allow_state_array)) {
            $condition['order_state'] = str_replace($allow_state_array,
                    array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS,ORDER_STATE_CANCEL), $_GET['state_type']);
        } else {
            $_GET['state_type'] = 'store_order';
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));
        
        //页面中显示那些操作
        foreach ($order_list as $key => $order_info) {

        	//显示取消订单
        	$order_list[$key]['if_cancel'] = $model_order->getOrderOperateState('store_cancel',$order_info);

        	//显示调整费用
        	$order_list[$key]['if_modify_price'] = $model_order->getOrderOperateState('modify_price',$order_info);
        	
        	//显示发货
        	$order_list[$key]['if_send'] = $model_order->getOrderOperateState('send',$order_info);
        	
        	//显示锁定中
        	$order_list[$key]['if_lock'] = $model_order->getOrderOperateState('lock',$order_info);

        	//显示物流跟踪
        	$order_list[$key]['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info);

        }

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        self::profile_menu('list',$_GET['state_type']);

        Tpl::showpage('store_order.index');
	}

	/**
	 * 卖家订单详情
	 *
	 */
	public function show_orderOp() {
	    $order_id = intval($_GET['order_id']);
	    if ($order_id <= 0) {
	        showMessage(Language::get('wrong_argument'),'','html','error');
	    }
	    $model_order = Model('order');
	    $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];	    
	    $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods','member'));
	    if (empty($order_info)) {
	        showMessage(Language::get('store_order_none_exist'),'','html','error');
	    }
	    Tpl::output('order_info',$order_info);

		//订单处理历史
		$log_list	= $model_order->getOrderLogList(array('order_id'=>$order_id));
		Tpl::output('order_log',$log_list);

		//退款退货信息
		$model_refund = Model('refund_return');
		$condition = array();
		$condition['order_id'] = $order_info['order_id'];
		$condition['seller_state'] = 2;
		$condition['admin_time'] = array('gt',0);
		$return_list = $model_refund->getReturnList($condition);
		Tpl::output('return_list',$return_list);

		//退款信息
		$refund_list = $model_refund->getRefundList($condition);
		Tpl::output('refund_list',$refund_list);

		self::profile_menu('show','show_order');
		Tpl::output('menu_sign','show_order');
		Tpl::output('left_show','order_view');
		Tpl::showpage('store_order.show');	    
	}

	/**
	 * 卖家订单状态操作
	 *
	 */
	public function change_stateOp() {
		$state_type	= $_GET['state_type'];
		$order_id	= intval($_GET['order_id']);

		$model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info	= $model_order->getOrderInfo($condition);
		Tpl::output('order_info',$order_info);
		try {
		
		    $model_order->beginTransaction();

    		if ($state_type == 'order_cancel') {
    		    $this->_change_state_order_cancel($order_info);
    		    $message = Language::get('store_order_cancel_success');
    		} elseif ($state_type == 'modify_price') {
    		    $this->_change_state_modify_price($order_info);
    		    $message = Language::get('store_order_edit_ship_success');
    		}

    		$model_order->commit();
    		showDialog($message,'reload','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');

		} catch (Exception $e) {
		    $model_order->rollback();
		    showDialog($e->getMessage(),'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
		}
	}

	/**
	 * 取消订单
	 * @param unknown $order_info
	 */
	private function _change_state_order_cancel($order_info) {
	    $order_id = $order_info['order_id'];
	    $model_order = Model('order');
	    if(chksubmit()) {
	        $if_allow = $model_order->getOrderOperateState('store_cancel',$order_info);
	        if (!$if_allow) {
	            throw new Exception(L('invalid_request'));
	        }
	        $goods_list = $model_order->getOrderGoodsList(array('order_id'=>$order_id));
	        $model_goods = Model('goods');
	        if(is_array($goods_list) and !empty($goods_list)) {
	            foreach ($goods_list as $goods) {
	                $data = array();
	                $data['goods_storage'] = array('exp','goods_storage+'.$goods['goods_num']);
	                $data['goods_salenum'] = array('exp','goods_salenum-'.$goods['goods_num']);
	                $update = $model_goods->editGoods($data,array('goods_id'=>$goods['goods_id']));
	                if (!$update) {
	                    throw new Exception(L('nc_common_save_fail'));
	                }
	            }
	        }

	        //解冻预存款
            $pd_amount = floatval($order_info['pd_amount']);
            if ($pd_amount > 0) {
                $model_pd = Model('predeposit');
                $data_pd = array();
                $data_pd['member_id'] = $order_info['buyer_id'];
                $data_pd['member_name'] = $order_info['buyer_name'];
                $data_pd['amount'] = $pd_amount;
                $data_pd['order_sn'] = $order_info['order_sn'];
                $model_pd->changePd('order_cancel',$data_pd);
            }

	        //更新订单信息
	        $data = array('order_state'=>ORDER_STATE_CANCEL);
	        $update = $model_order->editOrder($data,array('order_id'=>$order_id));
	        if (!$update) {
	            throw new Exception(L('nc_common_save_fail'));
	        }

	        //记录订单日志
	        $data = array();
	        $data['order_id'] = $order_id;
	        $data['log_role'] = 'seller';
			$data['log_user'] = $_SESSION['member_name'];
	        $data['log_msg'] = L('order_log_cancel');
	        $extend_msg = $_POST['state_info1'] != '' ? $_POST['state_info1'] : $_POST['state_info'];
	        if ($extend_msg) {
	            $data['log_msg'] .= ' ( '.$extend_msg.' )';
	        }
	        $data['log_orderstate'] = ORDER_STATE_CANCEL;
	        $model_order->addOrderLog($data);
	    } else {
	        Tpl::output('order_id',$order_id);
	        Tpl::showpage('store_order.cancel','null_layout');
	        exit();
	    }
	}

	/**
	 * 修改运费
	 * @param unknown $order_info
	 */
	private function _change_state_modify_price($order_info) {
	    $order_id = $order_info['order_id'];
	    $model_order = Model('order');
	    if(chksubmit()) {
	        $if_allow = $model_order->getOrderOperateState('modify_price',$order_info);
	        if (!$if_allow) {
	            throw new Exception(L('invalid_request'));
	        }
	        $data = array();
	        $data['shipping_fee'] = abs(floatval($_POST['shipping_fee']));
	        $data['order_amount'] = array('exp','goods_amount+'.$data['shipping_fee']);
	        $update = $model_order->editOrder($data,array('order_id'=>$order_id));
	        if (!$update) {
	            throw new Exception(L('nc_common_save_fail'));
	        }
	        //记录订单日志
	        $data = array();
	        $data['order_id'] = $order_id;
	        $data['log_role'] = 'seller';
			$data['log_user'] = $_SESSION['member_name'];
	        $data['log_msg'] = L('order_log_edit_ship');
	        $model_order->addOrderLog($data);
	    } else {
	        Tpl::output('order_id',$order_id);
	        Tpl::showpage('store_order.edit_price','null_layout');
	        exit();
	    }
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return 
     */
    private function profile_menu($menu_type='',$menu_key='') {
        Language::read('member_layout');
        switch ($menu_type) {
        	case 'list':
            $menu_array = array(
            array('menu_key'=>'store_order',		'menu_name'=>Language::get('nc_member_path_all_order'),	'menu_url'=>'index.php?act=store_order'),
            array('menu_key'=>'state_new',			'menu_name'=>Language::get('nc_member_path_wait_pay'),	'menu_url'=>'index.php?act=store_order&op=index&state_type=state_new'),
            array('menu_key'=>'state_pay',	        'menu_name'=>Language::get('nc_member_path_wait_send'),	'menu_url'=>'index.php?act=store_order&op=store_order&state_type=state_pay'),
            array('menu_key'=>'state_send',		    'menu_name'=>Language::get('nc_member_path_sent'),	    'menu_url'=>'index.php?act=store_order&op=index&state_type=state_send'),
            array('menu_key'=>'state_success',		'menu_name'=>Language::get('nc_member_path_finished'),	'menu_url'=>'index.php?act=store_order&op=index&state_type=state_success'),
            array('menu_key'=>'state_cancel',		'menu_name'=>Language::get('nc_member_path_canceled'),	'menu_url'=>'index.php?act=store_order&op=index&state_type=state_cancel'),
            );
            break;
            case 'show':
            $menu_array = array(
            array('menu_key'=>'all_order',			'menu_name'=>Language::get('nc_member_path_all_order'),	'menu_url'=>'index.php?act=store_order&op=index'),
            array('menu_key'=>'show_order',				'menu_name'=>Language::get('nc_member_path_show_order'),	'menu_url'=>'')
            );
            break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}   
