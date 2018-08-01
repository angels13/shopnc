<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo ($lang['nc_member_path_'.$output['menu_sign']]==''?'':$lang['nc_member_path_'.$output['menu_sign']].'_').$output['html_title'];?></title>
<meta name="keywords" content="<?php echo C('site_keywords'); ?>" />
<meta name="description" content="<?php echo C('site_description'); ?>" />
<meta name="author" content="ShopNC">
<meta name="copyright" content="ShopNC Inc. All Rights Reserved">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/member.css" rel="stylesheet" type="text/css">
<!--[if IE 6]><style type="text/css">
body {_behavior: url(<?php echo SHOP_TEMPLATES_URL;?>/css/csshover.htc);}
</style>
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
</script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/nc-sideMenu.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.js"></script>
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
<link href="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css">
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
          <?php }?>
          </a></li>
        <li><a <?php if($output['header_menu_sign'] == 'setting'){ echo "class='active'";}else{ echo "class='normal'";}?> href="index.php?act=home&op=member" title="<?php echo $lang['nc_member_path_setting'];?>"><?php echo $lang['nc_member_path_setting'];?></a></li>
      </ul>
      <div class="search-box">
        <form method="get" action="index.php" id="formSearch">
          <input name="act" id="search_act" value="search" type="hidden">
          <input name="keyword" id="keyword" type="text" class="text" placeholder="<?php echo $_GET['keyword'];?>" maxlength="200"/>
          <input name="" type="submit" value="" class="submit pngFix">
        </form>
      </div>
    </nav>
  </div>
</header>
<div id="container" class="wrapper">
  <div class="layout">
    <?php if($output['left_show'] != 'order_view') { ?>
    <div class="sidebar">
      <dl class="ncu-user">
        <dt class="username"><a href="index.php?act=home&op=member" title="<?php echo $lang['nc_edituserinfo'];?>"><?php echo empty($output['member_info']['member_truename'])? $output['member_info']['member_name']:$output['member_info']['member_truename'];?></a></dt>
        <dd class="userface">
          <div class="pic"><span class="thumb size100"><i></i><img src="<?php echo getMemberAvatar($output['member_info']['member_avatar']); ?>" onload="javascript:DrawImage(this,100,100);" alt="<?php echo $output['member_info']['member_name']; ?>" /></span>
            <p><a href="index.php?act=home&op=avatar" title="<?php echo $lang['nc_updateavatar'];?>"><?php echo $lang['nc_updateavatar'];?></a><i></i></p>
          </div>
        </dd>
        <dd class="info">
          <ul>
            <?php if (C('points_isuse') == 1){ ?>
            <li><?php echo $lang['nc_pointsnum'].$lang['nc_colon'];?><?php echo $output['member_info']['member_points'];?></li>
            <?php }?>
            <li><?php echo $lang['nc_predepositnum'].$lang['nc_colon'];?><span class="price"><?php echo $output['member_info']['available_predeposit'];?></span><?php echo $lang['currency_zh'];?></li>
          </ul>
        </dd>
      </dl>
      <div class="business-intro">
        <h3><?php echo $lang['nc_tradeinfo'];?></h3>
        <ul>
          <li><span><a href="index.php?act=member_order&state_type=state_new"><?php echo $lang['nc_order_waitpay'];?></a></span> <i <?php if($output['member_info']['order_nopay'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><?php echo $output['member_info']['order_nopay'];?></i></li>
          <li><span><a href="index.php?act=member_order&state_type=state_send"><?php echo $lang['nc_order_receiving'];?></a></span> <i <?php if($output['member_info']['order_noreceiving'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><?php echo $output['member_info']['order_noreceiving'];?></i></li>
          <li><span><a href="index.php?act=member_order&state_type=state_noeval"><?php echo $lang['nc_order_waitevaluate'];?></a></span> <i <?php if($output['member_info']['order_noeval'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><?php echo $output['member_info']['order_noeval'];?></i></li>
        </ul>
      </div>
      <script type="text/javascript">
	// <![CDATA[
	var myMenu;
	window.onload = function() {
		myMenu = new SDMenu("my_menu");
		myMenu.init();
	};
	// ]]>
	</script>
      <div class="business-handle" id="my_menu">
        <h3><?php echo $lang['nc_member_order_manage'];?></h3>
        <div class="normal"><em class="i1"></em><a href="<?php echo urlShop('cart', 'index');?>">我的购物车</a></div>
        <div <?php if($output['menu_sign'] == 'myorder'){ echo "class='active'";}else{ echo "class='normal'";}?>><em class="i2"></em><a href="index.php?act=member_order"><?php echo $lang['nc_member_myorder'];?></a></div>
        <dl class="collapsed">
          <dt><em class="i4"></em><a href="javascript:void(0)"><?php echo $lang['nc_member_path_favorites'];?></a><i></i></dt>
          <dd <?php if($output['menu_sign'] == 'collect_list'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_favorites&op=fglist"><?php echo $lang['nc_member_path_collect_list'];?></a></dd>
          <dd <?php if($output['menu_sign'] == 'collect_store'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_favorites&op=fslist"><?php echo $lang['nc_member_path_collect_store'];?></a></dd>
        </dl>
        <?php if (C('points_isuse') == 1){ ?>
        <dl class="collapsed">
          <dt><em class="i5"></em><a href="javascript:void(0)"><?php echo $lang['nc_member_points_manage'];?></a><i></i></dt>
          <dd <?php if($output['menu_sign'] == 'points'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_points"><?php echo $lang['nc_member_path_points'];?></a></dd>
          <?php if (C('points_isuse') == 1 && C('pointprod_isuse') == 1){ ?>
          <dd <?php if($output['menu_sign'] == 'pointorder'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_pointorder"><?php echo $lang['nc_member_path_pointorder'];?></a></dd>
          
          <?php }?>
        </dl>
        <?php }?>
        <?php if (C('voucher_allow') == 1){?>
        <div <?php if($output['menu_sign'] == 'myvoucher'){ echo "class='active'";}else{ echo "class='normal'";}?>><em class="i6"></em><a href="index.php?act=member_voucher"><?php echo $lang['nc_member_path_myvoucher'];?></a>
        </div>
        <?php } ?>
        <div <?php if($output['menu_sign'] == 'evaluatemanage'){ echo "class='active'";}else{ echo "class='normal'";}?>><em class="i7"></em><a href="index.php?act=member_evaluate&op=list"><?php echo $lang['nc_member_path_evalmanage'];?></a></div>
        <dl class="collapsed">
          <dt><em class="i8"></em><a href="javascript:void(0)"><?php echo $lang['nc_member_consult_complain'];?></a><i></i></dt>
          <dd <?php if($output['menu_sign'] == 'myinform'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_inform"><?php echo $lang['nc_member_path_myinform'];?></a></dd>
          <dd <?php if($output['menu_sign'] == 'consult'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_consult&op=my_consult"><?php echo $lang['nc_member_path_consult'];?></a></dd>
          <dd <?php if($output['menu_sign'] == 'complain'){ echo "class='active'";}else{ echo "class='normal'";}?>><a href="index.php?act=member_complain"><?php echo $lang['nc_member_path_complain'];?></a></dd>
        </dl>
      </div>
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
    <?php
} else {
	require_once($tpl_file);
}
?>
  </div>
</div>
<?php
require_once template('footer');
?>
</body>
</html>
