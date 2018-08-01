<?php
/**
 * PHP SDK for QQ登录 OpenAPI
 *
 * @version 1.2
 * @author connect@qq.com
 * @copyright © 2011, Tencent Corporation. All rights reserved.
 */
//包含配置信息
$data = require(BASE_DATA_PATH.DS.'cache'.DS.'setting.php');
//判读站外分析是否开启
if($data['share_isuse'] != 1 || $data['share_qqzone_isuse'] != 1){
	echo "<script>alert('系统未开启该功能');</script>";
	echo "<script>window.close();</script>";
	exit;
}
/**
 * 正式运营环境请关闭错误信息
 * ini_set("error_reporting", E_ALL);
 * ini_set("display_errors", TRUE);
 * QQDEBUG = true  开启错误提示
 * QQDEBUG = false 禁止错误提示
 * 默认禁止错误信息
 */
define("QQDEBUG", false);
if (defined("QQDEBUG") && QQDEBUG)
{
    @ini_set("error_reporting", E_ALL);
    @ini_set("display_errors", TRUE);
}

/**
 * session
 */
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS."session.php");


/**
 * 在你运行本demo之前请到 http://connect.opensns.qq.com/申请appid, appkey, 并注册callback地址
 */
//申请到的appid
$_SESSION['qqzone']["appid"]    = trim($data['share_qqzone_appid']); 

//申请到的appkey 
$_SESSION['qqzone']["appkey"]   = trim($data['share_qqzone_appkey']); 

//QQ登录成功后跳转的地址,请确保地址真实可用，否则会导致登录失败。 
//$_SESSION["callback"] = "http://redfox.oauth.com/oauth/qq_callback.php";
$_SESSION['qqzone']["callback"] = SHOP_SITE_URL."/index.php?act=member_sharemanage&op=share_qqzone";
//QQ授权api接口.按需调用
$_SESSION['qqzone']["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
?>
