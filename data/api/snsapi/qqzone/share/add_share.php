<?php
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS.'config.php');
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS.'utils.php');

function add_share($param)
{
    //发布一条动态的接口地址, 不要更改!!
	$url = "https://graph.qq.com/share/add_share?";
	$url .= "access_token=".$_SESSION['qqzone']["access_token"];
    $url .= "&oauth_consumer_key=".$_SESSION['qqzone']["appid"];
    $url .= "&openid=".$_SESSION['qqzone']["openid"];
    $url .= "&format=json";
    $url .= "&title=".urlencode($param['title']);
    $url .= "&url=".urlencode($param['url']);
    $url .= "&comment=".urlencode($param['comment']);
    $url .= "&summary=".urlencode($param['summary']);
    $url .= "&images=".urlencode($param['images']);
    $url .= "&source=".(isset($param['source']) && !empty($param['source'])?$param['source']:1);//分享的场景，对应上文接口说明的5。取值说明：1.通过网页 2.通过手机 3.通过软件 4.通过IPHONE 5.通过 IPAD。 
    $url .= "&type=".(isset($param['type']) && !empty($param['type'])?$param['type']:4);//分享内容的类型。4表示网页；5表示视频（type=5时，必须传入playurl）
    if ($param['type'] == 5){
    	$url .= "&type={$param['type']}";	
    }
    $url .= "&site=".urlencode($data['site_name']);
    $url .= "&nswb=1";
    $ret = get_url_contents($url);
}
?>
