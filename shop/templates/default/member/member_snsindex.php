<?php defined('InShopNC') or exit('Access Invalid!');?>
<link type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/skins/tango/skin.css" rel="stylesheet" >
<style type="text/css">
.path {
	display: none;
}
.fd-media .goodsinfo dt {
	width: 300px;
}
</style>
<div class="wrap">
  <div class="layout-l">
    <div class="member-intro">
      <dl>
        <dt class="nc-member-name"><a href="index.php?act=home&op=member" title="<?php echo $lang['nc_edituserinfo'];?>"><?php echo empty($output['member_info']['member_truename'])? $output['member_info']['member_name']:$output['member_info']['member_truename'];?></a>&nbsp;(<?php echo $output['member_info']['member_name']; ?>)</dt>
        <?php if (C('points_isuse') == 1){ ?>
        <dd><?php echo $lang['nc_pointsnum'];?>&nbsp;<strong><?php echo $output['member_info']['member_points'];?></strong></dd>
        <?php }?>
        <dd class="predeposit"><a href="javascript:void(0)"><?php echo $lang['nc_predepositnum'];?><span class="price ml5 mr5"><?php echo $output['member_info']['available_predeposit'];?></span><?php echo $lang['currency_zh'];?><i></i></a>
          <div class="down-menu">
            <p><a href="index.php?act=predeposit"><?php echo $lang['nc_member_path_predepositrecharge'];?></a></p>
            <p><a href="index.php?act=predeposit&op=pd_cash_list"><?php echo $lang['nc_member_path_predepositcash'];?></a></p>
            <p><a href="index.php?act=predeposit&op=pd_log_list"><?php echo $lang['nc_member_path_predepositlog'];?></a></p>
          </div>
        </dd>
      </dl>
      <ul>
        <li <?php if($output['member_info']['order_nopay'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><a href="index.php?act=member_order&state_type=state_new"><?php echo $lang['nc_order_waitpay'];?>&nbsp;(<strong><?php echo $output['member_info']['order_nopay'];?></strong>)</a></li>
        <li <?php if($output['member_info']['order_noreceiving'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><a href="index.php?act=member_order&state_type=state_send"><?php echo $lang['nc_order_receiving'];?>&nbsp;(<strong><?php echo $output['member_info']['order_noreceiving'];?></strong>)</a></li>
        <li <?php if($output['member_info']['order_noeval'] > 0){ echo "class='yes'";}else{ echo "class='no'";}?>><a href="index.php?act=member_order&state_type=state_noeval"><?php echo $lang['nc_order_waitevaluate'];?>&nbsp;(<strong><?php echo $output['member_info']['order_noeval'];?></strong>)</a></li>
      </ul>
    </div>
    <!-- 分享心情和宝贝 -->
    <ul class="release-tab">
      <li class="sharemood"><em></em><a href="javascript:void(0)"><?php echo $lang['sns_sharemood']; ?></a><i></i></li>
      <li class="sharegoods" id="snssharegoods"><em></em><a href="javascript:void(0)"><?php echo $lang['sns_sharegoods']; ?></a><i></i></li>
      <li class="sharestore" id="snssharestore"><em></em><a href="javascript:void(0)"><?php echo $lang['sns_sharestore']; ?></a></li>
    </ul>
    <div class="release-content"><span class="arrow"></span>
      <form id="weiboform" method="post" action="index.php?act=member_snsindex&op=addtrace">
        <textarea name="content" id="content_weibo" nc_type="contenttxt" class="textarea"resize="none"></textarea>
        <span class="error"></span>
        <div class="smile"><em></em><a href="javascript:void(0)" nc_type="smiliesbtn" data-param='{"txtid":"weibo"}'><?php echo $lang['sns_smiles'];?></a></div>
        <div id="weibocharcount" class="weibocharcount"></div>
        <div id="weiboseccode" class="weiboseccode">
          <label for="captcha" class="ml5 fl"><strong><?php echo $lang['nc_checkcode'].$lang['nc_colon'];?></strong></label>
          <input name="captcha" class="w40 fl text" type="text" id="captcha" size="4" maxlength="4"/>
          <a href="javascript:void(0)" class="ml5 fl"><img src="" title="<?php echo $lang['wrong_checkcode_change'];?>" name="codeimage" border="0" id="codeimage" onclick="this.src='index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random()" /></a>
          <input type="hidden" name="nchash" value="<?php echo $output['nchash'];?>"/>
        </div>
        <div class="handle">
          <div nc_type="formprivacydiv" class="privacy-module"><span class="privacybtn" style="width:55px;" nc_type="formprivacybtn"><i></i><?php echo $lang['sns_weiboprivacy_default'];?></span>
            <div class="privacytab" nc_type="formprivacytab" style="display:none;">
              <ul class="menu-bd">
                <li nc_type="formprivacyoption" data-param='{"v":"0"}'><span class="selected"><?php echo $lang['sns_weiboprivacy_all'];?></span></li>
                <li nc_type="formprivacyoption" data-param='{"v":"1"}'><span><?php echo $lang['sns_weiboprivacy_friend'];?></span></li>
                <li nc_type="formprivacyoption" data-param='{"v":"2"}'><span><?php echo $lang['sns_weiboprivacy_self'];?></span></li>
              </ul>
            </div>
          </div>
          <input type="hidden" name="privacy" id="privacy" value="0"/>
        </div>
        <input type="text" class="text" style="display:none;" />
        <!-- 防止点击Enter键提交 -->
        <input name="<?php echo $lang['nc_snsshare'];?>" type="button" class="button" value="<?php echo $lang['nc_snsshare'];?>" id="weibobtn" />
      </form>
      <!-- 表情弹出层 -->
      <div id="smilies_div" class="smilies-module"></div>
    </div>

    <!-- 动态列表 -->
    <div class="tabmenu" style="z-index:0;">
      <ul class="tab pngFix">
        <li class="active" nctype="friendtrace"><a id="tabGoodsIntro" href="javascript:void(0)" ><span><?php echo $lang['sns_friendtrace'];?></span></a></li>
        <li class="normal" nctype="storetrace"><a href="javascript:void(0)" ><span><?php echo $lang['nc_member_path_store_sns'];?></span></a></li>
      </ul>
    </div>
    <div id="friendtrace" class="mt20"></div>
    <div id="storetrace" class="mt20" style="display:none;"></div>
  </div>
  <div class="layout-r">
    <div class="visitors pngFix">
      <h4><span class="active" nc_type="visitmodule" data-param='{"name":"visit_me"}'><?php echo $lang['sns_visit_me']; ?></span><span class="line">|</span><span class="normal" nc_type="visitmodule" data-param='{"name":"visit_other"}'><?php echo $lang['sns_visit_other']; ?></span></h4>
      <ul id="visit_me" nc_type="visitlist">
        <?php if (!empty($output['visitme_list'])){?>
        <?php foreach ($output['visitme_list'] as $k=>$v){?>
        <li>
          <div class="visitor-pic"><span class="thumb size50"><i></i><a href="index.php?act=member_snshome&mid=<?php echo $v['v_mid'];?>" target="_blank" data-param="{'id':<?php echo $v['v_mid'];?>}" nctype="mcard"> <img src="<?php echo getMemberAvatar($v['v_mavatar']);?>" onload="javascript:DrawImage(this,50,50);"> </a></span></div>
          <p class="visitor-name"><a href="index.php?act=member_snshome&mid=<?php echo $v['v_mid'];?>" target="_blank" data-param="{'id':<?php echo $v['v_mid'];?>}" nctype="mcard"><?php echo $v['v_mname'];?></a></p>
          <p class="visitor-time"><?php echo $v['adddate_text'];?></p>
        </li>
        <?php }?>
        <?php }else {?>
        <?php echo $lang['sns_visitme_tip_1'];?><a href="index.php?act=member_snsfriend&op=find"><?php echo $lang['sns_visitme_tip_2'];?></a><?php echo $lang['sns_visitme_tip_3'];?>
        <?php }?>
      </ul>
      <ul id="visit_other" nc_type="visitlist" style="display: none;">
        <?php if (!empty($output['visitother_list'])){?>
        <?php foreach ($output['visitother_list'] as $k=>$v){?>
        <li>
          <div class="visitor-pic"><span class="thumb size50"><i></i><a href="index.php?act=member_snshome&mid=<?php echo $v['v_ownermid'];?>" target="_blank" data-param="{'id':<?php echo $v['v_ownermid'];?>}" nctype="mcard"> <img src="<?php echo getMemberAvatar($v['v_ownermavatar']);?>" onload="javascript:DrawImage(this,50,50);"> </a></span></div>
          <p class="visitor-name"><a href="index.php?act=member_snshome&mid=<?php echo $v['v_ownermid'];?>" target="_blank" data-param="{'id':<?php echo $v['v_ownermid'];?>}" nctype="mcard"><?php echo $v['v_ownermname'];?></a></p>
          <p class="visitor-time"><?php echo $v['adddate_text'];?> <?php echo $v['addtime_text'];?></p>
        </li>
        <?php }?>
        <?php }else {?>
        <?php echo $lang['sns_visitother_tip_1'];?><a href="index.php?act=member_snsfriend&op=follow"><?php echo $lang['sns_visitother_tip_2'];?></a><?php echo $lang['sns_visitother_tip_3'];?>
        <?php }?>
      </ul>
    </div>
    <?php echo loadadv(372,'html');?> </div>
  <div class="clear"></div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/smilies/smilies_data.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/smilies/smilies.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.caretInsert.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/jquery.jcarousel.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxdatalazy.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js" charset="utf-8"></script>
<script type="text/javascript">
var max_recordnum = '<?php echo $output['max_recordnum'];?>';
	document.onclick = function(){ $("#smilies_div").html(''); $("#smilies_div").hide();};
	$(function(){
		//提交分享心情表单
		$("#weibobtn").bind('click',function(){
			if($("#weiboform").valid()){
				var cookienum = $.cookie(COOKIE_PRE+'weibonum');
				cookienum = parseInt(cookienum);
				if(cookienum >= max_recordnum && $("#weiboseccode").css('display') == 'none'){
					//显示验证码
					$("#weiboseccode").show();
					$("#weiboseccode").find("#codeimage").attr('src','index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash'];?>&t=' + Math.random());
				}else if(cookienum >= max_recordnum && $("#captcha").val() == ''){
					showDialog('<?php echo $lang['wrong_null'];?>');
				}else{
					ajaxpost('weiboform', '', '', 'onerror');
					//隐藏验证码
					$("#weiboseccode").hide();
					$("#weiboseccode").find("#codeimage").attr('src','');
					$("#captcha").val('');
				}
			}
			return false;
		});
		$('#weiboform').validate({
			errorPlacement: function(error, element){
				element.next('.error').append(error);
		    },
		    rules : {
		    	content : {
		            required : true,
		            maxlength : 140
		        }
		    },
		    messages : {
		    	content : {
		            required : '<?php echo $lang['sns_sharemood_content_null'];?>',
		            maxlength: '<?php echo $lang['sns_content_beyond'];?>'
		        }
		    }
		});
		//显示分享商品页面
		$('#snssharegoods').click(function(){
		    ajax_form("sharegoods", '<?php echo $lang['sns_share_purchasedgoods'];?>', '<?php echo SHOP_SITE_URL.DS;?>index.php?act=member_snsindex&op=sharegoods&irefresh=1', 500);
		    return false;
		});
		//显示分享店铺页面
		$('#snssharestore').click(function(){
		    ajax_form("sharestore", '<?php echo $lang['sns_sharestore'];?>', '<?php echo SHOP_SITE_URL.DS;?>index.php?act=member_snsindex&op=sharestore&irefresh=1', 500);
		    return false;
		});
        //加载好友动态分页
		$('#friendtrace').lazyinit();
		$('#friendtrace').lazyshow({url:"index.php?act=member_snsindex&op=tracelist&curpage=1",'iIntervalId':true});
		//心情字符个数动态计算
		$("#content_weibo").charCount({
			allowed: 140,
			warning: 10,
			counterContainerID:'weibocharcount',
			firstCounterText:'<?php echo $lang['sns_charcount_tip1'];?>',
			endCounterText:'<?php echo $lang['sns_charcount_tip2'];?>',
			errorCounterText:'<?php echo $lang['sns_charcount_tip3'];?>'
		});
		$("[nc_type='visitmodule']").bind('click',function(){
			var data_str = $(this).attr('data-param');
		    eval( "data_str = "+data_str);
		    $("[nc_type='visitmodule']").removeClass('active');
		    $("[nc_type='visitmodule']").addClass('normal');
		    $(this).removeClass('normal');
		    $(this).addClass('active');
		    $("[nc_type='visitlist']").hide();
		    $("#"+data_str.name).show();
		});

		// 标签切换
		$('.tab').children('li').click(function(){
			$('.tab').children('li').removeClass().addClass('normal');
			$(this).removeClass().addClass('active');

			var trace_sign = $(this).attr('nctype');
			var url_friendtrace	= 'index.php?act=member_snsindex&op=tracelist&curpage=1';
			var url_storetrace	= 'index.php?act=member_storesns&op=stracelist';
			$('#friendtrace,#storetrace').html('').hide();
			$('#'+trace_sign).show('fast',function(){
				$('#'+trace_sign).lazyinit();
				$('#'+trace_sign).lazyshow({url:eval('url_'+trace_sign),'iIntervalId':true});
			});
		});
	});
</script>
