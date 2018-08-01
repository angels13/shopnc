<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.jcarousel-skin-tango .jcarousel-clip-horizontal { width: 400px !important; height: 92px !important;}
.jcarousel-skin-tango .jcarousel-item { height: 92px !important;}
.jcarousel-skin-tango .jcarousel-container-horizontal { width: 400px !important;}
</style>
<style>
.share-widget { margin: 10px 10px 0 10px; padding: 6px; text-align:left; border-top: dashed 1px #E7E7E7;}
.share-widget .title { display: inline-block; line-height:24px; padding: 0 5px; color: #777;}
.share-widget .s-app { display: inline-block; }
.share-widget .s-app i { background: url("<?php echo SHOP_TEMPLATES_URL;?>/images/member/shareicon/shareicons.gif") no-repeat; vertical-align: middle; display: inline-block; width: 24px; height: 24px; cursor: pointer;}
.share-widget .s-app .i-sinaweibo { background-position: 0 -144px;}
.share-widget .s-app .disable .i-sinaweibo { background-position: 0 -168px;}
.share-widget .s-app .i-renren { background-position: 0 -96px;}
.share-widget .s-app .disable .i-renren { background-position: 0 -120px;}
.share-widget .s-app .i-qqzone { background-position: 0 -0px;}
.share-widget .s-app .disable .i-qqzone { background-position: 0 -24px;}
.share-widget .s-app .i-qqweibo { background-position: 0 -48px;}
.share-widget .s-app .disable .i-qqweibo { background-position: 0 -72px;}
.share-widget .s-app a { line-height:22px; color:#777; display:inline-block; padding: 0 8px;  border: solid 1px #E7E7E7; border-radius: 4px; background:#F7F7F7; margin: 1px 0 0 8px;}
</style>
<div class="feededitor">			
  <form method="post" action="index.php?act=member_snsindex&op=sharegoods<?php if($_GET['irefresh'] == 1){?>&irefresh=1<?php }?>" id="sharegoods_form">
    <input type="hidden" name="form_submit" value="ok"/>
    <!-- 商品图片start -->
    <?php if (!empty($output['goods_list'])){?>
    <ul id="mycarousel-s" class="snsgoodsimglist jcarousel-skin-tango">
      <?php foreach ($output['goods_list'] as $v) {?>
      <li style="float: left;"><a href="javascript:void(0);" value="<?php echo $v['goods_id'] ?>"> <span class="thumb size90"><?php if($v['order']){?><i class="b"></i><?php }?><?php if($v['favorites']){?><i class="f"></i><?php }?> <img title="<?php echo $v['goods_name']?>" src="<?php echo thumb($v, 240);?>" onload="javascript:DrawImage(this,90,90);"/>
        <p class="extra"><?php echo $lang['sns_selected'];?></p>
        </span> </a></li>
      <?php }?>
    </ul>
    <?php } else{?>
    <div class="sns-norecord"><?php echo $lang['sns_sharegoods_notbuygoods'];?></div>
    <?php }?>
    <input type="hidden" id="choosegoodsid" name="choosegoodsid" value="<?php echo intval($output['goods_list'][0]['goods_id']);?>" />
    <div class="error"></div>
        
    <!-- 站外分享 -->
    <?php if (C('share_isuse') == 1){?>
    <div class="share-widget"> <span class="title">同时分享到：</span> <span class="s-app">
      <?php if (!empty($output['app_arr'])){?>
      <?php foreach ($output['app_arr'] as $k=>$v){?>
      <label nc_type="appitem_<?php echo $k;?>" title="<?php echo $v['name'];?>" class="<?php echo $v['isbind']?'checked':'disable';?>"> <i class="i-<?php echo $k;?>" nc_type="bindbtn" data-param='{"apikey":"<?php echo $k;?>","apiname":"<?php echo $v['name'];?>"}' attr_isbind="<?php echo $v['isbind']?'1':'0';?>"></i>
        <input type="hidden" id="checkapp_<?php echo $k;?>" name="checkapp_<?php echo $k;?>" value="<?php echo $v['isbind']?'1':'0';?>" />
      </label>
      <?php }?>
      <?php }?>
      <a target="_blank" href="index.php?act=member_sharemanage">同步设置</a> </span> </div>
    <?php }?>
    
    <!-- 商品图片end -->
    <div class="p10">
      <textarea placeholder="<?php echo $lang['sns_sharegoods_contenttip'];?>" name="comment" id="content_sgweibo" resize="none"></textarea>
      <div class="error form-error"></div>
      <!-- 验证码 -->
      <div id="sg_seccode" class="seccode">
        <label for="captcha"><?php echo $lang['nc_checkcode'].$lang['nc_colon']; ?></label>
        <input name="captcha" type="text" class="text" size="4" maxlength="4"/>
        <img src="" title="<?php echo $lang['wrong_checkcode_change'];?>" name="codeimage" border="0" onclick="this.src='index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random()"/>
        <span><?php echo $lang['wrong_seccode'];?></span>
        <input type="hidden" name="nchash" value="<?php echo $output['nchash'];?>"/>
      </div>
      <input type="text" style="display:none;" />
      <!-- 防止点击Enter键提交 -->
      <div class="handle">
        <div id="sgcharcount" class="fl"></div>
        <input name="<?php echo $lang['nc_snsshare'];?>" type="button" class="button" value="<?php echo $lang['nc_snsshare'];?>" id="weibobtn_goods" />
        <div nc_type="formprivacydiv" class="privacy-module w100 fr mr10 mt5"><span class="privacybtn" style="width:55px;" nc_type="formprivacybtn"><i></i><?php echo $lang['sns_weiboprivacy_default'];?></span>
          <div class="privacytab" nc_type="formprivacytab" style="display:none;">
            <ul class="menu-bd">
              <li nc_type="formprivacyoption" data-param='{"v":"0","hiddenid":"gprivacy"}'><span class="selected"><?php echo $lang['sns_weiboprivacy_all'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"1","hiddenid":"gprivacy"}'><span><?php echo $lang['sns_weiboprivacy_friend'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"2","hiddenid":"gprivacy"}'><span><?php echo $lang['sns_weiboprivacy_self'];?></span></li>
            </ul>
          </div>
        </div>
      </div>
     
    </div> <input type="hidden" name="gprivacy" id="gprivacy" value="0"/>
  </form>
</div>
<script type="text/javascript">
$(function(){
	//图片轮换
	$.getScript("<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/jquery.jcarousel.min.js", function(){
	    $('#mycarousel-s').jcarousel({visible: 4,itemFallbackDimension: 300});
	});
	//初始化选择的商品
	$(".snsgoodsimglist").find('a').eq(0).addClass("selected");
	//商品列表绑定事件
	$(".snsgoodsimglist").find('a').bind("click",function(){
		$(".snsgoodsimglist").find('a').removeClass('selected');
		$(this).addClass("selected");
		var gid = $(this).attr('value');
		$("#choosegoodsid").val(gid);
	});
	//分享商品评论字符个数计算
	$("#content_sgweibo").charCount({
		allowed: 140,
		warning: 10,
		counterContainerID:'sgcharcount',
		firstCounterText:'<?php echo $lang['sns_charcount_tip1'];?>',
		endCounterText:'<?php echo $lang['sns_charcount_tip2'];?>',
		errorCounterText:'<?php echo $lang['sns_charcount_tip3'];?>'
	});
	//分享商品表单验证
	$('#sharegoods_form').validate({
		errorPlacement: function(error, element){
			element.next('.error').append(error);
	    },      
	    rules : {
	    	choosegoodsid:{
	    		min:1
	    	},
	    	comment : {
	            maxlength : 140
	        }
	    },
	    messages : {
	    	choosegoodsid:{
	    		min:'<?php echo $lang['sns_sharegoods_choose'];?>'
	    	},
	    	comment : {
	            maxlength: '<?php echo $lang['sns_content_beyond'];?>'
	        }
	    }
	});
});
</script>
