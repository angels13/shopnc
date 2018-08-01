<?php
//require_once("../comm/config.php");
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS.'config.php');

function qq_login($appid, $scope, $callback)
{
    $_SESSION['qqzone']['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . $appid . "&redirect_uri=" . urlencode($callback)
        . "&state=" . $_SESSION['qqzone']['state']
        . "&scope=".$scope;
    header("Location:$login_url");
}

//用户点击qq登录按钮调用此函数
qq_login($_SESSION['qqzone']["appid"], $_SESSION['qqzone']["scope"], $_SESSION['qqzone']["callback"]);
?>
