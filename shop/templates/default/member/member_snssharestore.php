<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.jcarousel-skin-tango .jcarousel-clip-horizontal { width: 400px !important; height: 92px !important;}
.jcarousel-skin-tango .jcarousel-item { height: 92px !important;}
.jcarousel-skin-tango .jcarousel-container-horizontal { width: 400px !important;}
</style>
<div class="feededitor">
  <form method="post" action="index.php?act=member_snsindex&op=sharestore&<?php if($_GET['irefresh'] == 1){?>irefresh=1<?php }?>" id="sharestore_form">
    <input type="hidden" name="form_submit" value="ok"/>
    <!-- 店铺列表 start -->
    <?php if (!empty($output['store_list'])){?>
    <ul id="mycarousel-g" class="snsstorelist jcarousel-skin-tango">
      <?php foreach ($output['store_list'] as $v) {?>
      <li><a href="javascript:void(0);" value="<?php echo $v['store_id'] ?>"><span class="thumb size90"><i></i><img title="<?php echo $v['store_name']?>" src="<?php echo getStoreLogo($v['store_label']);?>" onload="javascript:DrawImage(this,90,90);" />
        <p class="extra"><?php echo $lang['sns_selected'];?></p>
        </span></a></li>
      <?php }?>
    </ul>
    <?php } else{?>
    <div class="sns-norecord"><?php echo $lang['sns_sharestore_nothavecollect'];?></div>
    <?php }?>
    <input type="hidden" id="choosestoreid" name="choosestoreid" value="<?php echo intval($output['store_list'][0]['store_id']);?>" />
    <div class="error"></div>
    <!-- 店铺列表 end -->
    <div class="p10">
      <textarea placeholder="<?php echo $lang['sns_sharestore_contenttip'];?>" name="comment" id="content_ssweibo" resize="none" ></textarea>
      <div class="error form-error"></div>
      <!-- 验证码 -->
      <div id="ss_seccode" class="seccode">
      <label for="captcha" class="fl"><?php echo $lang['nc_checkcode'].$lang['nc_colon']; ?></label>
        <input name="captcha" class="fl w40 text"  type="text" size="4" maxlength="4"/>
        <img src="" title="<?php echo $lang['wrong_checkcode_change'];?>" class="ml5 fl mr5" name="codeimage" onclick="this.src='index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random()"/>
        <span class="fl"><?php echo $lang['wrong_seccode'];?></span>
        <input type="hidden" name="nchash" value="<?php echo $output['nchash'];?>"/>
      </div>
      <input type="text" style="display:none;" />
      <!-- 防止点击Enter键提交 -->
      <div class="handle">
        <div id="sscharcount" class="fl"></div>        
        <input name="<?php echo $lang['nc_snsshare'];?>" type="button" class="button" value="<?php echo $lang['nc_snsshare'];?>" id="weibobtn_store" />
        <div nc_type="formprivacydiv" class="privacy-module fr w100 mr10 mt5"> <span class="privacybtn" style="width:55px;" nc_type="formprivacybtn"><?php echo $lang['sns_weiboprivacy_default'];?><i></i></span>
          <div class="privacytab" nc_type="formprivacytab" style="display:none;">
            <ul class="menu-bd">
              <li nc_type="formprivacyoption" data-param='{"v":"0","hiddenid":"sprivacy"}'><span class="selected"><?php echo $lang['sns_weiboprivacy_all'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"1","hiddenid":"sprivacy"}'><span><?php echo $lang['sns_weiboprivacy_friend'];?></span></li>
              <li nc_type="formprivacyoption" data-param='{"v":"2","hiddenid":"sprivacy"}'><span><?php echo $lang['sns_weiboprivacy_self'];?></span></li>
            </ul>
          </div>
        </div>
      </div>
     
    </div> <input type="hidden" name="sprivacy" id="sprivacy" value="0"/>
  </form>
</div> 
<script>
$(function(){
	//图片轮换
	$.getScript("<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/jquery.jcarousel.min.js", function(){
    	$('#mycarousel-g').jcarousel({visible: 4,itemFallbackDimension: 300});
	});
	//初始化选择的店铺
	$(".snsstorelist").find('a').eq(0).addClass("selected");
	//商品列表绑定事件
	$(".snsstorelist").find('a').bind("click",function(){
		$(".snsstorelist").find('a').removeClass('selected');
		$(this).addClass("selected");
		var sid = $(this).attr('value');
		$("#choosestoreid").val(sid);
	});
	//分享店铺评论字符个数计算
	$("#content_ssweibo").charCount({
		allowed: 140,
		warning: 10,
		counterContainerID:'sscharcount',
		firstCounterText:'<?php echo $lang['sns_charcount_tip1'];?>',
		endCounterText:'<?php echo $lang['sns_charcount_tip2'];?>',
		errorCounterText:'<?php echo $lang['sns_charcount_tip3'];?>'
	});
	//分享店铺表单验证
	$('#sharestore_form').validate({
		errorPlacement: function(error, element){
			element.next('.error').append(error);
	    },      
	    rules : {
	    	choosestoreid:{
	    		min:1
	    	},
	    	comment : {
	            maxlength : 140
	        }
	    },
	    messages : {
	    	choosestoreid:{
	    		min:'<?php echo $lang['sns_sharestore_choose'];?>'
	    	},
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
