<?php defined('InShopNC') or exit('Access Invalid!');?>
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
/* 首页分享商品和分享店铺弹出框体 */
.feededitor { width:480px; margin: 0 auto;}
.feededitor ul li { display: block; position:relative; width: 92px; height:92px;}
.feededitor ul li a { width: 90px; height:90px; border:solid 1px #E7E7E7; display:block;}
.feededitor ul li a:hover, .feededitor ul li a.selected  { border-color:#80B8D2;}
.feededitor ul li .extra { display: none;}
.feededitor ul li a.selected .extra { font-size:12px; line-height:20px; color:#fff ;background-color: rgba(128,184,210,0.85)!important; background-color: #80B8D2\9/* IE6-8 */; text-align: center; display:block; width: 90px; height: 20px; border-top: solid 1px #80B8D2; position: absolute; z-index: 1; left: 1px; bottom: 1px; visibility: inherit;}
.feededitor { width:480px; margin: 0 auto;}
.feededitor .goods { margin: 10px !important; overflow:hidden;}
.feededitor .goods .pic { width: 90px; height:90px; float:left; padding:1px; border: solid 1px #E7E7E7;}
.feededitor .goods .intro { float:left; margin-left:20px; width: 340px;}
.feededitor .goods .intro dt { font-size:14px; line-height: 20px; font-weight:700; height:40px; margin-bottom:5px;}
.feededitor .goods .intro dd { font-size:12px; color: #777; line-height: 20px; height:20px; margin-bottom:5px;}
.feededitor .handle { line-height: 32px; height: 32px; margin-top: 10px;}
.feededitor .handle input.button{ font: 700 14px/32px "microsoft yahei"; color:#FFF; background-color:#80B8D2; width:60px; height:32px;  border:none; border-radius: 4px; float:right; display:block; cursor:pointer; border-radius: 4px;}
.feededitor textarea { width: 450px; height: 60px;}
.feededitor label.error { font-size:12px; color: #F00; line-height:24px; }
.seccode { font-size:12px !important; line-height:26px; background:#FFFFBF; border: solid 1px #DDD; display: none; height:26px; padding:8px; margin: 5px 0; }
.seccode label { color: #555; float:left; }
.seccode input.text { height: 20px !important; line-height:20px; float:left; width:50px;}
.seccode img { float:left; margin:0 6px; _margin:0 3px; cursor: pointer;}
.seccode span { color: #F60;  float:left;}


.privacy-module { line-height:22px; font-size:12px; display:block; height:22px;  position: relative; z-index:99; cursor: pointer;}
.privacy-module:hover { }
.privacy-module .privacybtn { background-color:#FFF; color: #7FB8D2;  height: 22px; padding-left: 8px; margin: 1px; position: absolute; z-index: 99; top: 0; right:0;}
.privacy-module:hover .privacybtn { margin:0; border: solid 1px #80b8D2; border-bottom-color:#FFF;}
.privacybtn i { font-size: 0; line-height: 0; display: block; width: 0; height: 0; border-width: 4px; border-color: #80B8D2 transparent transparent transparent; border-style: solid dashed dashed dashed ; overflow: hidden; float:right; margin: 8px 4px; _margin: 8px 2px;}
.privacytab { background-color: #FFF; width: 102px; position: absolute; z-index: 1; top: 23px; right: 0px;}
.privacytab .menu-bd { background: none repeat scroll 0 0 white; text-align: left; width: 100px; max-height: 120px; border: 1px solid #80B8D2; overflow: hidden;}
.privacytab .menu-bd li { line-height:20px; color: #666666; background: none repea0t scroll 0 0 white; height:20px; padding: 6px 25px 4px 5px; margin: 0px;}
.privacytab .menu-bd li .selected{ background: url(<?php echo SHOP_TEMPLATES_URL;?>/images/right.gif) no-repeat scroll 0 0 transparent; }
.privacytab .menu-bd li:hover { background-color:#f0f0f0}
.privacytab .menu-bd li span { vertical-align: top; text-overflow: ellipsis; white-space: nowrap; display: inline-block; max-width: 80px; width: 80px; padding-left:20px; overflow: hidden; cursor: pointer;}
.privacytab .menu-bd li span a { font-size: 12px !important; color: #666666; text-decoration: none; font-weight: normal !important;}

</style>
<div class="feededitor">
  <form method="post" action="index.php?act=member_snsindex&op=sharestore" id="sharestore_form">
    <input type="hidden" name="form_submit" value="ok"/>
    <input type="hidden" id="choosestoreid" name="choosestoreid" value="<?php echo intval($output['store_info']['store_id']);?>" />
    <div class="goods">
        <div class="pic"><span class="thumb size90"><i></i><a target="_blank" href="<?php echo $output['store_info']['store_url'];?>"><img title="<?php echo $output['store_info']['store_name']; ?>" src="<?php echo getStoreLogo($output['store_info']['store_label']);?>" onload="javascript:DrawImage(this,90,90);" /></a></span></div>
      <dl class="intro">
        <dt><a target="_blank" href="<?php echo $output['store_info']['store_url'];?>"><?php echo $output['store_info']['store_name']; ?></a></dt>
        <dd><?php echo $lang['sns_sharestore_shopkeeper'].$lang['nc_colon']; ?><?php echo $output['store_info']['member_name']; ?></dd>
        <dd><?php echo $lang['sns_sharestore_location'].$lang['nc_colon']; ?><?php echo $output['store_info']['area_info']; ?></dd>
      </dl>
    </div>
    <!-- 站外分享 -->
    <?php if (C('share_isuse') == 1){?>
    <div class="share-widget"> <span class="title"><?php echo $lang['sharebind_alsoshareto'];?></span> <span class="s-app">
      <?php if (!empty($output['app_arr'])){?>
      <?php foreach ($output['app_arr'] as $k=>$v){?>
      <label nc_type="appitem_<?php echo $k;?>" title="<?php echo $v['name'];?>" class="<?php echo $v['isbind']?'checked':'disable';?>"> <i class="i-<?php echo $k;?>" nc_type="bindbtn" data-param='{"apikey":"<?php echo $k;?>","apiname":"<?php echo $v['name'];?>"}' attr_isbind="<?php echo $v['isbind']?'1':'0';?>"></i>
        <input type="hidden" id="checkapp_<?php echo $k;?>" name="checkapp_<?php echo $k;?>" value="<?php echo $v['isbind']?'1':'0';?>" />
      </label>
      <?php }?>
      <?php }?>
      <a target="_blank" href="index.php?act=member_sharemanage"><?php echo $lang['sharebind_alsosharesetting'];?></a> </span> </div>
    <?php }?>
    <div class="p10">
      <textarea placeholder="<?php echo $lang['sns_sharestore_contenttip'];?>" name="comment" id="content_ssweibo" resize="none" ></textarea>
      <div class="error form-error"></div>
      <!-- 验证码 -->
        <div id="ss_seccode" class="seccode">
        <label for="captcha"><?php echo $lang['nc_checkcode'].$lang['nc_colon']; ?></label>
          <input name="captcha" class="text" type="text" size="4" maxlength="4"/>
          <img src="" title="<?php echo $lang['wrong_checkcode_change'];?>" name="codeimage" onclick="this.src='index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random()"/>
          <span><?php echo $lang['wrong_seccode'];?></span>
          <input type="hidden" name="nchash" value="<?php echo $output['nchash'];?>"/>
        </div>
        <input type="text" style="display:none;" />
        <!-- 防止点击Enter键提交 -->
      <div class="handle">
        <div id="sscharcount" class="fl"></div>
        
        <input name="<?php echo $lang['nc_snsshare'];?>" type="button" class="button" value="<?php echo $lang['nc_snsshare'];?>" id="weibobtn_store" />
        <div nc_type="formprivacydiv" class="privacy-module fr w100 mr10 mt5"> <span class="privacybtn" nc_type="formprivacybtn"><?php echo $lang['sns_weiboprivacy_default'];?><i></i></span>
          <div class="privacytab" nc_type="formprivacytab" style="display:none;">
            <ul class="menu-bd">
              <li nc_type="formprivacyoption" data-param='{"v":"0","hiddenid":"sprivacy"}'><span class="selected"><?php echo $lang['sns_weiboprivacy_all'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"1","hiddenid":"sprivacy"}'><span><?php echo $lang['sns_weiboprivacy_friend'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"2","hiddenid":"sprivacy"}'><span><?php echo $lang['sns_weiboprivacy_self'];?></span></li>
            </ul>
          </div>
          <input type="hidden" name="sprivacy" id="sprivacy" value="0"/>
        </div>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" charset="utf-8"></script>
<script>
var max_recordnum = '<?php echo $output['max_recordnum'];?>';
$(function(){
    $.getScript('<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js', function(){
        //分享店铺评论字符个数计算
        $("#content_ssweibo").charCount({
            allowed: 140,
            warning: 10,
            counterContainerID:'sscharcount',
            firstCounterText:'<?php echo $lang['sns_charcount_tip1'];?>',
            endCounterText:'<?php echo $lang['sns_charcount_tip2'];?>',
            errorCounterText:'<?php echo $lang['sns_charcount_tip3'];?>'
        });
    });
	//分享店铺表单验证
	$('#sharestore_form').validate({
		errorPlacement: function(error, element){
			element.next('.error').append(error);
	    },      
	    rules : {
	    	comment : {
	            maxlength : 140
	        }
	    },
	    messages : {
	    	comment : {
	            maxlength: '<?php echo $lang['sns_content_beyond'];?>'
	        }
	    }
	});

	//提交分享店铺表单
	$("#weibobtn_store").click(function(){
		if($("#sharestore_form").valid()){
			var cookienum = $.cookie(COOKIE_PRE+'weibonum');
			cookienum = parseInt(cookienum);
			if(cookienum >= max_recordnum && $("#ss_seccode").css('display') == 'none'){
				//显示验证码
				$("#ss_seccode").show();
				var nchash = $("#ss_seccode").find("[name='nchash']").val();
				$("#ss_seccode").find("[name='codeimage']").attr('src','index.php?act=seccode&op=makecode&nchash='+nchash+'&t=' + Math.random());
			}else if(cookienum >= max_recordnum && $("#ss_seccode").find("[name='captcha']").val() == ''){
				showDialog('<?php echo $lang['wrong_null'];?>');
			}else{
				ajaxpost('sharestore_form', '', '', 'onerror');
				//隐藏验证码
				$("#ss_seccode").hide();
				$("#ss_seccode").find("[name='codeimage']").attr('src','');
				$("#ss_seccode").find("[name='captcha']").val('');
			}
		}
		return false;
	});
});
</script>
