<?php
/**
 * 支付回调
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

class paymentControl extends mobileHomeControl{

	public function __construct() {
		parent::__construct();
	}

    /**
     * 支付回调
     */
    public function returnOp() {
        $verify_result = $this->_verify_result('return');

        if($verify_result) {
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //支付接口代码
            $payment_code = 'alipay';

            //验证成功		
            $model_order = Model('order');
            $model_payment = Model('payment');

    		$order_list = $model_order->getOrderList(array('pay_sn'=>$out_trade_no,'order_state'=>ORDER_STATE_NEW));

            $result = $model_payment->updateProductBuy($out_trade_no, $payment_code, $order_list, $trade_no);
            if(empty($result['error'])) {
                Tpl::output('result', 'success');
                Tpl::output('message', '支付成功');
			}else{
                Tpl::output('result', 'fail');
                Tpl::output('message', '支付失败');
			}
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
            Tpl::output('result', 'fail');
            Tpl::output('message', '支付失败');
		}
        Tpl::showpage('payment_message');

    }

    /**
     * 支付提醒
     */
    public function notifyOp() {
        $verify_result = $this->_verify_result('notify');

		if($verify_result) {//验证成功
			$notify_data = $_POST['notify_data'];
			//解析notify_data
			//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
			$doc = new DOMDocument();
			$doc->loadXML($notify_data);
			
			if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
				//商户订单号
				$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
                //支付接口代码
                $payment_code = 'alipay';

                $model_order = Model('order');
                $model_payment = Model('payment');
				
				if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                    $order_list = $model_order->getOrderList(array('pay_sn'=>$out_trade_no,'order_state'=>ORDER_STATE_NEW));

                    $result = $model_payment->updateProductBuy($out_trade_no, $payment_code, $order_list, $trade_no);
                    if(empty($result['error'])) {
						echo "success";		//请不要修改或删除
					}else{
						echo "fail";
					}
				}
			}
		}
		else {
		    //验证失败
		    echo "fail";
		}

    }

    private function _verify_result($type) {
		unset($_GET['act']);	//将系统的控制参数置空，防止因为加密验证出错
		unset($_GET['op']);	//将系统的控制参数置空，防止因为加密验证出错

        $model_payment = Model('payment');

        //读取接口配置信息
        $condition = array();
        $condition['payment_code'] = 'alipay';
        $payment_info = $model_payment->getPaymentOpenInfo($condition);
    	$alipay_config = unserialize($payment_info['payment_config']);
		
		$alipay_config = array(
			'partner' => $alipay_config['alipay_partner'],
			'key' => $alipay_config['alipay_key'],
			'private_key_path' => 'key/rsa_private_key.pem',
			'ali_public_key_path' => 'key/alipay_public_key.pem',
			'sign_type' => 'MD5',
			'input_charset' => 'utf-8',
			'cacert' => getcwd().'\\cacert.pem',
			'transport' => 'http'
		);

        require_once(BASE_PATH.DS.'api/payment/alipay/lib/alipay_notify.class.php');

		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);

        switch ($type) {
            case 'notify':
                $verify_result = $alipayNotify->verifyNotify();
                break;
            case 'return':
                $verify_result = $alipayNotify->verifyReturn();
                break;
            default:
                $verify_result = false;
                break;
        }

        return $verify_result;
    }
}
