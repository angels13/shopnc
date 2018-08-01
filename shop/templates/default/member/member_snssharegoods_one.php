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
</style>
<div class="feededitor">
  <form method="post" action="index.php?act=member_snsindex&op=sharegoods" id="sharegoods_form">
    <input type="hidden" name="form_submit" value="ok"/>
    <input type="hidden" id="choosegoodsid" name="choosegoodsid" value="<?php echo intval($output['goods_info']['goods_id']);?>" />
    
    <!-- 商品图片end -->
    <div class="goods">
      <div class="pic"><span class="thumb size90"><i></i> <a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$output['goods_info']['goods_id']));?>"> <img title="<?php echo $output['goods_info']['goods_name']; ?>" src="<?php echo thumb($output['goods_info'],60);?>" /> </a></span></div>
      <dl class="intro">
        <dt><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$output['goods_info']['goods_id']));?>"><?php echo $output['goods_info']['goods_name']; ?></a></dt>
        <dd><?php echo $lang['sns_sharegoods_price'].$lang['nc_colon']; ?><span class="goods-price"><strong><?php echo $output['goods_info']['goods_price']; ?></strong></span></dd>
        <dd><?php echo $lang['sns_sharegoods_freight'].$lang['nc_colon']; ?><span class="goods-price"><i><?php echo $output['goods_info']['goods_freight'];?></i></span></dd>
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
      <textarea placeholder="<?php echo $lang['sns_sharegoods_contenttip2'];?>" name="comment" id="content_sgweibo" resize="none"></textarea>
      <div class="error form-error"></div>
      <!-- 验证码 -->
      <div id="sg_seccode" class="seccode">
        <label for="captcha"><?php echo $lang['nc_checkcode'].$lang['nc_colon']; ?></label>
        <input name="captcha" type="text" class="text" size="4" maxlength="4"/>
        <img src="" title="<?php echo $lang['wrong_checkcode_change'];?>" name="codeimage" onclick="this.src='index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random()" /> <span><?php echo $lang['wrong_seccode'];?></span>
        <input type="hidden" name="nchash" value="<?php echo $output['nchash'];?>"/>
      </div>
      <input type="text" style="display:none;" />
      <!-- 防止点击Enter键提交 -->
      <div class="handle">
        <div id="sgcharcount" class="fl"></div>
        <input name="<?php echo $lang['nc_snsshare'];?>" type="button" class="button" value="<?php echo $lang['nc_snsshare'];?>" id="weibobtn_goods" />
        <div nc_type="formprivacydiv" class="privacy-module fr w100 mr10 mt5"><span class="privacybtn" style="width:55px;" nc_type="formprivacybtn"><?php echo $lang['sns_weiboprivacy_default'];?><i></i></span>
          <div class="privacytab" nc_type="formprivacytab" style="display:none;">
            <ul class="menu-bd">
              <li nc_type="formprivacyoption" data-param='{"v":"0","hiddenid":"gprivacy"}'><span class="selected"><?php echo $lang['sns_weiboprivacy_all'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"1","hiddenid":"gprivacy"}'><span><?php echo $lang['sns_weiboprivacy_friend'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"2","hiddenid":"gprivacy"}'><span><?php echo $lang['sns_weiboprivacy_self'];?></span></li>
            </ul>
          </div>
        </div>
      </div>
      <input type="hidden" name="gprivacy" id="gprivacy" value="0"/>
    </div>
  </form>
</div>
<textarea id="bindtooltip_module" style="display:none;">
<div class="eject_con"><dl><dt style="width:25%"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/member/shareicon/shareicon_@apikey.png" width="40" height="40" class="mt5 mr20"></dt><dd style="width:75%"><p><?php echo $lang['sharebind_list_popup_tip1'];?><strong class="ml5 mr5">@apiname</strong><?php echo $lang['sharebind_list_popup_tip2'];?></p><p class="red"><?php echo $lang['sharebind_list_popup_tip3'];?>@apiname<?php echo $lang['sharebind_list_popup_tip4'];?></p></dd></dl>
<dl class="bottom"><dt style="width:25%">&nbsp;</dt>
        <dd style="width:75%"><a href="javascript:void(0);" nc_type="finishbtn" data-param='{"apikey":"@apikey"}' class="ncu-btn2 mr10"><?php echo $lang['sharebind_list_finishbind'];?></a><span><?php echo $lang['sharebind_list_unfinishedbind'];?><a target="_blank" href="<?php echo SHOP_SITE_URL;?>/api.php?act=sharebind&type=@apikey" class="ml5"><?php echo $lang['sharebind_list_againbind'];?></a></span></dd>
      </dl>
</div>
</textarea>
<script type="text/javascript">
var max_recordnum = '<?php echo $output['max_recordnum'];?>';
$(function(){
	$.getScript("<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js", function(){
    	//分享商品评论字符个数计算
    	$("#content_sgweibo").charCount({
    		allowed: 140,
    		warning: 10,
    		counterContainerID:'sgcharcount',
    		firstCounterText:'<?php echo $lang['sns_charcount_tip1'];?>',
    		endCounterText:'<?php echo $lang['sns_charcount_tip2'];?>',
    		errorCounterText:'<?php echo $lang['sns_charcount_tip3'];?>'
    	});
	});
	//分享商品表单验证
	$('#sharegoods_form').validate({
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
	$("[nc_type='bindbtn']").bind('click',function(){
		var data_str = $(this).attr('data-param');
	    eval( "data_str = "+data_str);
	    //判断是否已经绑定
	    var isbind = $(this).attr('attr_isbind');
	    if(isbind == '1'){//已经绑定
		    if($("#checkapp_"+data_str.apikey).val() == '1'){
			    $("[nc_type='appitem_"+data_str.apikey+"']").removeClass('checked');
		    	$("[nc_type='appitem_"+data_str.apikey+"']").addClass('disable');
            	$("#checkapp_"+data_str.apikey).val('0');
			}else{
				$("[nc_type='appitem_"+data_str.apikey+"']").removeClass('disable');
            	$("[nc_type='appitem_"+data_str.apikey+"']").addClass('checked');
            	$("#checkapp_"+data_str.apikey).val('1');
			}
		}else{
			var html = $("#bindtooltip_module").text();
		    //替换关键字
		    html = html.replace(/@apikey/g,data_str.apikey);
		    html = html.replace(/@apiname/g,data_str.apiname);
		    html_form("bindtooltip", "<?php echo $lang['sharebind_list_accountconnect'];?>", html, 360, 0);	    
		    window.open('api.php?act=sharebind&type='+data_str.apikey);
		}
	});
	$("[nc_type='finishbtn']").live('click',function(){
		var data_str = $(this).attr('data-param');
	    eval( "data_str = "+data_str);
		//验证是否绑定成功
		var url = 'index.php?act=member_sharemanage&op=checkbind';
        $.getJSON(url, {'k':data_str.apikey}, function(data){
        	DialogManager.close('bindtooltip');
            if (data.done)
            {
            	$("[nc_type='appitem_"+data_str.apikey+"']").addClass('check');
            	$("[nc_type='appitem_"+data_str.apikey+"']").removeClass('disable');
            	$('#checkapp_'+data_str.apikey).val('1');
            	$("[nc_type='appitem_"+data_str.apikey+"']").find('i').attr('attr_isbind','1');
            }
            else
            {
            	showDialog(data.msg, 'notice');
            }
        });
	});
});
</script>