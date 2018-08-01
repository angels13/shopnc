<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html>
<head>
<meta content="IE=9" http-equiv="X-UA-Compatible">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo $output['html_title'];?></title>
<meta name="keywords" content="<?php echo $output['seo_keywords']; ?>" />
<meta name="description" content="<?php echo $output['seo_description']; ?>" />
<meta name="author" content="ShopNC">
<meta name="copyright" content="ShopNC Inc. All Rights Reserved">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/shop.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/store/style/<?php echo $output['store_info']['store_theme'];?>/style.css" rel="stylesheet" type="text/css">
<!--[if IE 6]><style type="text/css">
body {_behavior: url(<?php echo SHOP_TEMPLATES_URL;?>/css/csshover.htc);}
</style>
<![endif]-->
<script>
COOKIE_PRE = '<?php echo COOKIE_PRE;?>';_CHARSET = '<?php echo strtolower(CHARSET);?>';SITEURL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
</script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/shop.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.js"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL;?>/js/respond.min.js"></script>
<![endif]-->
<!--[if IE 6]>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/IE6_PNG.js"></script>
<script>
DD_belatedPNG.fix('.pngFix,.pngFix:hover');
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
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/IE6_MAXMIX.js"></script>
<![endif]-->
</head>
<body>
<?php require_once template('layout/layout_top');?>
<header id="header">
  <div class="layout">
    <h1 id="shop-logo">
      <a class="mall-home" href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['store_info']['store_id']));?>" title="<?php echo $GLOBALS['setting_config']['site_name']; ?>"><img src="<?php echo getStoreLogo($output['store_info']['store_label']);?>"  alt="<?php echo $output['store_info']['store_name'];?>" title="<?php echo $output['store_info']['store_name'];?>" /></a>
    </h1>
    <div class="shop-head-info" id="jsddm">
      
      <div class="search" id="shop-info">
        <form method="get" action="<?php echo SHOP_SITE_URL.'/';?>index.php" name="formSearch" id="formSearch">
          <input name="act" id="search_act" value="search" type="hidden" />
          <input name="keyword" id="keyword" type="text" class="ncs-search-input-text" value="<?php echo $_GET['keyword'];?>" x-webkit-speech lang="zh-CN" onwebkitspeechchange="foo()" x-webkit-grammar="builtin:search" placeholder="<?php echo $lang['nc_what_goods'];?>" />
          <a href="javascript:void(0)" class="ncs-search-btn-mall" nctype="search_in_shop"><span><?php echo $lang['nc_search_in_website'];?></span></a><a href="javascript:void(0)" class="ncs-search-btn-shop" nctype="search_in_store"><span><?php echo $lang['nc_search_in_store'];?></span></a>
        </form>
      </div>
      <div class="service"><i></i><?php echo $lang['nc_service'];?><em></em>
        <div class="arrow"></div>
        <div class="sub">
          <?php include template('store/callcenter');?>
        </div>
      </div>
      <div class="favorites"><i></i><?php echo $lang['nc_collect'];?><em></em>
        <div class="arrow"></div>
        <div class="sub">
          <div class="title-bar">
            <h3><?php echo $lang['nc_shop_space'];?></h3>
          </div>
          <ul>
            <li><a href="javascript:collect_store('<?php echo $output['store_info']['store_id'];?>','count','store_collect')" class="btn"><i></i><?php echo $lang['nc_collect'];?></a></li>
            <li><a href="javascript:void(0);" nctype="store_collect" class="no-url"><?php echo $output['store_info']['store_collect'];?></a><span><?php echo $lang['nc_collection_popularity'];?></span></li>
            <li><a href="index.php?act=store_snshome&sid=<?php echo $output['store_info']['store_id'];?>" target="_blank">0</a><span><?php echo $lang['nc_store_the_dynamic'];?></span></li>
            <li><a href="javascript:void(0);" class="share" nctype="share_store"></a><span><?php echo $lang['nc_share'];?></span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</header>
<div class="background clearfix">
<div class="ncsl-nav">
  <div class="banner"><a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['store_info']['store_id']));?>" class="img">
    <?php if(!empty($output['store_info']['store_banner'])){?>
    <img src="<?php echo getStoreLogo($output['store_info']['store_banner']);?>" alt="<?php echo $output['store_info']['store_name']; ?>" title="<?php echo $output['store_info']['store_name']; ?>" class="pngFix">
    <?php }else{?>
    <div class="ncs-default-banner pngFix"></div>
    <?php }?>
    </a></div>
  <nav id="nav" class="pngFix">
    <ul class="pngFix">
      <li class="<?php if($output['page'] == 'index'){?>active<?php }else{?>normal<?php }?>"><a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['store_info']['store_id']));?>"><span><?php echo $lang['nc_store_index'];?><i></i></span></a></li>
      <?php if($output['page'] == 'goods'){?>
      <li class="active"><a href="JavaScript:void(0);"><span><?php echo $lang['nc_goods_info'];?><i></i></span></a></li>
      <?php }?>
      <?php if($output['page'] == 'bundling'){?>
      <li class="active"><a href="JavaScript:void(0);"><span><?php echo $lang['nc_bundling'];?><i></i></span></a></li>
      <?php }?>
      <?php if(!empty($output['store_navigation_list'])){
      		foreach($output['store_navigation_list'] as $value){
                if($value['sn_if_show']) {
      			if($value['sn_url'] != ''){?>
      			<li class="<?php if($output['page'] == $value['sn_id']){?>active<?php }else{?>normal<?php }?>"><a href="<?php echo $value['sn_url']; ?>" <?php if($value['sn_new_open']){?>target="_blank"<?php }?>><span><?php echo $value['sn_title'];?><i></i></span></a></li>
      			<?php }else{ ?>
                <li class="<?php if($output['page'] == $value['sn_id']){?>active<?php }else{?>normal<?php }?>"><a href="<?php echo urlShop('show_store', 'show_article', array('store_id' => $output['store_info']['store_id'], 'sn_id' => $value['sn_id']));?>"><span><?php echo $value['sn_title'];?><i></i></span></a></li>
      <?php }}}} ?>
    </ul>
  </nav>
</div>
<?php require_once($tpl_file); ?>
</div>
<?php include template('footer');?>
<script type="text/javascript">
$(function(){
	$('a[nctype="search_in_store"]').click(function(){
		if ($('#keyword').val() == '') {
			return false;
		}
		$('#search_act').val('show_store');
		$('<input type="hidden" value="<?php echo $output['store_info']['store_id'];?>" name="store_id" /> <input type="hidden" name="op" value="goods_all" />').appendTo("#formSearch");
		$('#formSearch').submit();
	});
	$('a[nctype="search_in_shop"]').click(function(){
		if ($('#keyword').val() == '') {
			return false;
		}
		$('#formSearch').submit();
	});
	$('#keyword').css("color","#999999");

	var storeTrends	= true;
	$('.favorites').mouseover(function(){
		var $this = $(this);
		if(storeTrends){
			$.getJSON('index.php?act=show_store&op=ajax_store_trend_count&store_id=<?php echo $output['store_info']['store_id'];?>', function(data){
				$this.find('li:eq(2)').find('a').html(data.count);
				storeTrends = false;
			});
		}
	});

	$('a[nctype="share_store"]').click(function(){
		<?php if ($_SESSION['is_login'] !== '1'){?>
		login_dialog();
		<?php } else {?>
		ajax_form('sharestore', '分享店铺', 'index.php?act=member_snsindex&op=sharestore_one&inajax=1&sid=<?php echo $output['store_info']['store_id'];?>');
		<?php }?>
	});
});
</script>
</body>
</html>
