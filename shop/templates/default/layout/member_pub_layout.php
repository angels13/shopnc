<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo ($lang['nc_member_path_'.$output['menu_sign']]==''?'':$lang['nc_member_path_'.$output['menu_sign']].'_').$output['html_title'];?></title>
<meta name="keywords" content="<?php echo C('site_keywords'); ?>" />
<meta name="description" content="<?php echo C('site_description'); ?>" />
<meta name="author" content="ShopNC">
<meta name="copyright" content="ShopNC Inc. All Rights Reserved">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/member.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">
<!--[if IE 6]><style type="text/css">
body {_behavior: url(<?php echo SHOP_TEMPLATES_URL;?>/css/csshover.htc);}
</style>
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
</script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js" charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript">
$(function(){
    $('#formSearch').find('input[type="submit"]').click(function(){
    	if ($('#keyword').val() == '') {
    		return false;
    	}
    	$('#formSearch').submit();
    });
});
</script>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/respond.min.js"></script>
<![endif]-->
<!--[if IE 6]>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/IE6_PNG.js"></script>
<script>
DD_belatedPNG.fix('.pngFix');
</script>
<script> 
// <![CDATA[ 
if((window.navigator.appName.toUpperCase().indexOf("MICROSOFT")>=0)&&(document.execCommand)) 
try{ 
document.execCommand("BackgroundImageCache", false, true); 
   } 
catch(e){} 
// ]]> 
</script> 
<![endif]-->

</head>
<body>
<?php require_once template('layout/layout_top');?>
<header id="header" class="pngFix">
  <div class="wrapper">
    <h1 id="logo" title="<?php echo C('site_name'); ?>"><a href="<?php echo SHOP_SITE_URL;?>"><img src="<?php echo C('member_logo') == ''?UPLOAD_SITE_URL.DS.ATTACH_COMMON.DS.C('site_logo'):UPLOAD_SITE_URL.DS.ATTACH_COMMON.DS.C('member_logo'); ?>" alt="<?php echo C('site_name'); ?>" class="pngFix"></a></h1>
    <nav>
      <ul>
        <li class="frist"><a <?php if($output['header_menu_sign'] == 'snsindex'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=member_snsindex" title="<?php echo $lang['nc_member_path_buyerindex'];?>"><?php echo $lang['nc_member_path_buyerindex'];?></a></li>
        <li><a <?php if($output['header_menu_sign'] == 'snshome'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=member_snshome" title="<?php echo $lang['nc_member_path_myspace'];?>"><?php echo $lang['nc_member_path_myspace'];?></a></li>
        <li><a <?php if($output['header_menu_sign'] == 'friend'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=member_snsfriend&op=find" title="<?php echo $lang['nc_member_path_friend'];?>"><?php echo $lang['nc_member_path_friend'];?></a></li>
        <li><a <?php if($output['header_menu_sign'] == 'message'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=home&op=message" title="<?php echo $lang['nc_member_path_message'];?>"><?php echo $lang['nc_member_path_message'];?>
        	<?php if (intval($output['message_num']) > 0){ ?>
        	<i class="new-message"><?php echo intval($output['message_num']); ?></i>
        	<?php }?></a></li>
        <li><a <?php if($output['header_menu_sign'] == 'setting'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=home&op=member" title="<?php echo $lang['nc_member_path_setting'];?>"><?php echo $lang['nc_member_path_setting'];?></a></li>
      </ul>
      <div class="search-box">
        <form method="get" action="index.php" id="formSearch">
          <input name="act" id="search_act" value="search" type="hidden">
          <input name="keyword" id="keyword" type="text" class="text"  placeholder="<?php echo $_GET['keyword'];?>" maxlength="200"/>
          <input name="" type="submit" value="" class="submit pngFix">
        </form>
      </div>
    </nav>
  </div>
</header>
<div id="container" class="wrapper">
  <div class="layout">
    <div class="sidebar">
    <div class="member-handle"><h3><?php echo $lang['nc_member_path_accountsettings'];?></h3>
      <ul>
        <li <?php if($output['menu_sign'] == 'profile'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=home&op=member"><?php echo $lang['nc_member_path_profile'];?></a></li>
        <li <?php if($output['menu_sign'] == 'address'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member&op=address"><?php echo $lang['nc_member_path_address'];?></a></li>
        <?php if (C('qq_isuse') == 1){?>
        <li <?php if($output['menu_sign'] == 'qq_bind'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_qqconnect&op=qqbind"><?php echo $lang['nc_member_path_qq_bind'];?></a></li>
        <?php }?>
        <?php if (C('sina_isuse') == 1){?>
        <li <?php if($output['menu_sign'] == 'sina_bind'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_sconnect&op=sinabind"><?php echo $lang['nc_member_path_sina_bind'];?></a></li>
        <?php }?>
        <li <?php if($output['menu_sign'] == 'predepositrecharge'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=predeposit"><?php echo $lang['nc_member_path_predepositrecharge'];?></a></li>
        <li <?php if($output['menu_sign'] == 'predepositcash'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=predeposit&op=pd_cash_list"><?php echo $lang['nc_member_path_predepositcash'];?></a></li>
        <li <?php if($output['menu_sign'] == 'pd_log_list'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=predeposit&op=pd_log_list"><?php echo $lang['nc_member_path_predepositlog'];?></a></li>
        <?php if (C('share_isuse') == 1){ ?>
        <li <?php if($output['menu_sign'] == 'sharemanage'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_sharemanage"><?php echo $lang['nc_member_path_sharemanage'];?></a></li>
        <?php }?>
      </ul></div>
    </div>
    <div class="right-content">
      <div class="path">
        <div><a href="index.php?act=member_snsindex"><?php echo $lang['nc_user_center'];?></a><span>&raquo;</span>
          <?php if($output['menu_sign_url'] != '' and $lang['nc_member_path_'.$output['menu_sign1']] != ''){?>
          <a href="<?php echo $output['menu_sign_url'];?>"/>
          <?php }?>
          <?php echo $lang['nc_member_path_'.$output['menu_sign']];?>
          <?php if($output['menu_sign_url'] != '' and $lang['nc_member_path_'.$output['menu_sign1']] != ''){?>
          </a><span>&raquo;</span><?php echo $lang['nc_member_path_'.$output['menu_sign1']];?>
          <?php }?>
        </div>
      </div>
      <div class="main">
        <?php
		require_once($tpl_file);
		?>
      </div>
    </div>
  </div>
</div>
<?php require_once template('footer'); ?>
</body>
</html>
