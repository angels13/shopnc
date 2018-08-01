<?php 
//require_once("../comm/config.php");
//require_once("../comm/utils.php");
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS.'config.php');
require_once(BASE_DATA_PATH.DS.'api'.DS.'snsapi'.DS.'qqzone'.DS.'comm'.DS.'utils.php');

function qq_callback()
{
    //debug
    if($_REQUEST['state'] == $_SESSION['qqzone']['state']) //csrf
    {
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $_SESSION['qqzone']["appid"]. "&redirect_uri=" . urlencode($_SESSION['qqzone']["callback"])
            . "&client_secret=" . $_SESSION['qqzone']["appkey"]. "&code=" . $_REQUEST["code"];

        $response = get_url_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);

        //debug
        //print_r($params);

        //set access token to session
        $_SESSION['qqzone']["access_token"] = $params["access_token"];
        $_SESSION['qqzone']["expires_in"] = $params["expires_in"];
    }
    else 
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}

function get_openid()
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION['qqzone']['access_token'];

    $str  = get_url_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }

    $user = json_decode($str);
    if (isset($user->error))
    {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }
    //set openid to session
    $_SESSION['qqzone']["openid"] = $user->openid;
}

//QQ登录成功后的回调地址,主要保存access token
qq_callback();

//获取用户标示id
get_openid();
//echo "<script>window.close();</script>";
?>
