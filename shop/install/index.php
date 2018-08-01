<?php
/**
 * 
 * ShopNC system instller
 * 
 * 
 */
define('InShopNC',true);

// 设置最大执行时间
set_time_limit(0);

error_reporting(E_ALL & ~E_NOTICE);
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('DBCHARSET','UTF8');//编码 分为 UTF8 GBK
input($_GET);input($_POST);
if(function_exists('date_default_timezone_set')){
    date_default_timezone_set('Asia/Shanghai');
}
function input(&$data){
	foreach ((array)$data as $key => $value) {
		if (is_string($value)) {
			if (!get_magic_quotes_gpc()) {
				$value = htmlentities($value, ENT_NOQUOTES);
				$value = addslashes(trim($value));
			}			
		}else{
			$data[$key] = input($value);
		}
	}
}

//Detection identifies the file exists, if it exists, then stop the installation
if (file_exists('lock') && $_GET['step'] != 5){
	@header("Content-type: text/html; charset=UTF-8");
	echo "系统已经安装过了，如果要重新安装，那么请删除install目录下的lock文件";
	exit;
}

//define variable

$html_title = 'ShopNC【B2B2C】 - 安装向导';

$html_header = <<<EOF
<div class="header">
  <div class="layout">
    <div class="title">
      <h5>ShopNC【B2B2C】电商系统</h5>
      <h2>系统安装向导</h2>
    </div>
    <div class="version">版本: 2014.01.16.2490</div>
  </div>
</div>

EOF;

$html_footer = <<<EOF
<div class="footer">
  <h5>Powered by <font class="blue">Shop</font><font class="orange">NC</font><font class="black">【B2B2C】</font></h5>
  <h6>版权所有 2007-2014 &copy; <a href="http://www.shopnc.net" target="_blank">天津市网城天创科技有限责任公司</a></h6>
  <h6>软件注册登记编号：软著登字第0650314号</h6>
</div>
EOF;


require('./include/function.php');
if (!in_array($_GET['step'],array(1,2,3,4,5))) $_GET['step'] = 0;

switch ($_GET['step']) {
	case 1:
		require('./include/var.php');
		env_check($env_items);
		dirfile_check($dirfile_items);
		function_check($func_items);
	break;
	case 3:
		$install_error = '';
		$install_recover = '';
		$demo_data =  file_exists('./data/utf8_add.sql') ? true : false;
		step3($install_error,$install_recover);
		break;
	case 4:
		step4();
		break;
	case 5:
		$sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
		$sitepath = str_replace('/shop/install',"",$sitepath);
		$auto_site_url = strtolower('http://'.$_SERVER['HTTP_HOST'].$sitepath);
		break;	
	default:
	break;
}

function step3(&$install_error,&$install_recover){
    global $html_title,$html_header,$html_footer;
	if ($_POST['submitform'] != 'submit') return;
	$db_host = $_POST['db_host'];
	$db_port = $_POST['db_port'];
	$db_user = $_POST['db_user'];
	$db_pwd = $_POST['db_pwd'];
	$db_name = $_POST['db_name'];
	$db_prefix = $_POST['db_prefix'];
	$admin = $_POST['admin'];
	$password = $_POST['password'];
	if (!$db_host || !$db_port || !$db_user || !$db_pwd || !$db_name || !$db_prefix || !$admin || !$password){
		$install_error = '输入不完整，请检查';
	}
	if(strpos($db_prefix, '.') !== false) {
		$install_error .= '数据表前缀为空，或者格式错误，请检查';
	}

	if(strlen($admin) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $admin)) {
		$install_error .= '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字';
	}
	if ($install_error != '') reutrn;
	if(!@mysql_connect($db_host.":".$db_port, $db_user, $db_pwd)) {
		$install_error = '数据库连接失败';return;
	}

	if(mysql_get_server_info() > '5.0') {
		mysql_query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET ".DBCHARSET);
	} else {
		$install_error = '数据库必须为MySQL5.0版本以上';return;
	}
	if(mysql_errno()) {
		$install_error = mysql_error();return ;
	}
	if($_POST['install_recover'] != 'yes' && ($query = mysql_query("SHOW TABLES FROM $db_name"))) {
		while($row = mysql_fetch_row($query)) {
			if(preg_match("/^$db_prefix/", $row[0])) {
				$install_error = '数据表已存在，继续安装将会覆盖已有数据';
				$install_recover = 'yes';
				return;
			}
		}
	}

	require ('step_4.php');
	$sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
	$sitepath = str_replace('/shop/install',"",$sitepath);
	$auto_site_url = strtolower('http://'.$_SERVER['HTTP_HOST'].$sitepath);	
	
	write_config($auto_site_url);

	//execute sql
	$db = new db;
	$db->connect($db_host.":".$db_port, $db_user, $db_pwd, $db_name, DBCHARSET);
	if (strtoupper(DBCHARSET) == 'GBK'){
		$_charset = 'gbk';
	}else{
		$_charset = 'utf8';
	}

	$sql = file_get_contents("data/{$_charset}.sql");
	$sql .= "\r\n".file_get_contents("data/{$_charset}_cms.sql");
	$sql .= "\r\n".file_get_contents("data/{$_charset}_circle.sql");
	$sql .= "\r\n".file_get_contents("data/{$_charset}_microshop.sql");

	if ($_POST['demo_data'] == '1'){
		$sql .= file_get_contents("data/{$_charset}_add.sql");
	}
    $sql = str_replace("\r\n", "\n", $sql);
    runquery($sql,$db_prefix,$db);
	showjsmessage('初始化数据 ... 成功 ');
	
	/**
	 * 转码
	 */
	if (strtoupper(DBCHARSET) == 'GBK'){
		$sitename = iconv('UTF-8','GBK',$_POST['site_name']);
		$username = iconv('UTF-8','GBK',$_POST['admin']);
		$password = iconv('UTF-8','GBK',$_POST['password']);
	}else{
		$sitename = $_POST['site_name'];
		$username = $_POST['admin'];
		$password = $_POST['password'];		
	}
	/**
	 * 产生随机的md5_key，来替换系统默认的md5_key值
	 */
	$md5_key = md5(random(4).substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'].$db_host.$db_user.$db_pwd.$db_name.substr(time(), 0, 6)), 8, 6).random(10));
	$db->query("UPDATE {$db_prefix}setting SET value='".$sitename."' WHERE name='site_name'");
	$db->query("UPDATE {$db_prefix}setting SET value='".$md5_key."' WHERE name='md5_key'");
	//管理员帐号密码
	$db->query("INSERT INTO {$db_prefix}admin (`admin_id`,`admin_name`,`admin_password`,`admin_login_time`,`admin_login_num`,`admin_is_super`) VALUES ('1','$username','". md5($password) ."', '".time()."' ,'0',1);");
	$db->query("UPDATE `{$db_prefix}navigation` SET nav_url=CONCAT('{$auto_site_url}/',nav_url)");
	$db->query("UPDATE `{$db_prefix}cms_navigation` SET navigation_link=CONCAT('{$auto_site_url}/',navigation_link)");
	//新增一个标识文件，用来屏蔽重新安装
	$fp = @fopen('lock','wb+');
	@fclose($fp);
	exit("<script type=\"text/javascript\">document.getElementById('install_process').innerHTML = '安装完成，下一步...';document.getElementById('install_process').href='index.php?step=5&sitename={$sitename}&username={$username}&password={$password}';</script>");
	exit();
}
include ("step_{$_GET['step']}.php");

//execute sql
function runquery($sql, $db_prefix, $db) {
// 	global $lang, $tablepre, $db;

	if(!isset($sql) || empty($sql)) return;

	$sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
		}
		$num++;
	}
	unset($sql);
	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$line = explode('`',$query);
				$data_name = $line[1];
				showjsmessage('数据表  '.$data_name.' ... 创建成功');
				$db->query(droptable($data_name));
				/**
				 * 转码
				 */
				if (strtoupper(DBCHARSET) == 'GBK'){
					$query = iconv('GBK','UTF-8',$query);
				}
				$db->query($query);
				unset($line,$data_name);
			} else {
				$db->query($query);
			}
		}
	}
}
//抛出JS信息
function showjsmessage($message) {
	echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
	flush();
	ob_flush();
}



//写入config文件
function write_config($url) {
	extract($GLOBALS, EXTR_SKIP);
	$config = 'data/config.ini.php';

	$configfile = @file_get_contents($config);
	$configfile = trim($configfile);
	$configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;

	if (strtoupper(DBCHARSET) == 'GBK'){
		$charset = 'GBK';
	}else {
		$charset = 'UTF-8';
	}

	$db_host = $_POST['db_host'];
	$db_port = $_POST['db_port'];
	$db_user = $_POST['db_user'];
	$db_pwd = $_POST['db_pwd'];
	$db_name = $_POST['db_name'];
	$db_prefix = $_POST['db_prefix'];
	$admin = $_POST['admin'];
	$password = $_POST['password'];
	$db_type = (extension_loaded('mysqli')) ? 'mysqli' : 'mysql';

	$cookie_pre = strtoupper(substr(md5(random(6).substr($_SERVER['HTTP_USER_AGENT'].md5($_SERVER['SERVER_ADDR'].$db_host.$db_user.$db_pwd.$db_name.substr(time(), 0, 6)), 8, 6).random(5)),0,4)).'_';
	$configfile = str_replace("===url===",			$url, $configfile);
	$configfile = str_replace("===db_prefix===",	$db_prefix, $configfile);
	$configfile = str_replace("===db_driver===",	$db_type, $configfile);
	$configfile = str_replace("===db_charset===",	$charset, $configfile);
	$configfile = str_replace("===db_host===",		$db_host, $configfile);
	$configfile = str_replace("===db_user===",		$db_user, $configfile);
	$configfile = str_replace("===db_pwd===",		$db_pwd, $configfile);
	$configfile = str_replace("===db_name===",		$db_name, $configfile);
	$configfile = str_replace("===db_port===",		$db_port, $configfile);
	$configfile = str_replace("===setup_date===",	date("Y-m-d H:i:s",time()), $configfile);
	$configfile = str_replace("===cookie_pre===",	$cookie_pre, $configfile);
	if ($_POST['demo_data'] == '1') {
	   $configfile = str_replace("===default_store_id===",	1, $configfile);
	} else {
	   $configfile = str_replace("===default_store_id===",	0, $configfile);
	}
	@file_put_contents('../../data/config/config.ini.php', $configfile);
}

?>