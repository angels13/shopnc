<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="wrap">
  <div class="ncm-notes">
    <h4><?php echo $lang['sharebind_list_tip1'];?></h4>
    <p><?php echo $lang['sharebind_list_tip2'];?></p>
  </div>
  <ul class="bind-account-list">
    <?php if(!empty($output['app_arr'])){?>
    <?php foreach($output['app_arr'] as $k=>$v){?>
    <li>
      <div class="account-item"><span class="website-icon"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/member/shareicon/shareicon_<?php echo $k;?>.png"></span>
        <dl>
          <dt><?php echo $v['name'];?></dt>
          <dd><?php echo $v['url'];?></dd>
          <dd class="operate">
            <?php if ($v['isbind']){?>
            <em><?php echo $lang['sharebind_list_bindtime'].$lang['nc_colon']; ?><?php echo @date('Y-m-d',$v['snsbind_updatetime']);?></em> <a href="javascript:void(0);" nc_type="unbindbtn" data-param='{"apikey":"<?php echo $k;?>","apiname":"<?php echo $v['name'];?>"}'><?php echo $lang['sharebind_list_unbind'];?></a>
            <?php }else {?>
            <a class="ncu-btn2" nc_type="bindbtn" data-param='{"apikey":"<?php echo $k;?>","apiname":"<?php echo $v['name'];?>"}' href="javascript:void(0);"><?php echo $lang['sharebind_list_immediatelybind'];?></a>
            <?php }?>
          </dd>
        </dl>
      </div>
    </li>
    <?php }?>
    <?php }?>
  </ul>
</div>
<textarea id="bindtooltip_module" style="display:none;">
<div class="eject_con"><dl><dt style="width:25%"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/member/shareicon/shareicon_@apikey.png" width="40" height="40" class="mt5 mr20"></dt><dd style="width:75%"><p><?php echo $lang['sharebind_list_popup_tip1'];?><strong class="ml5 mr5">@apiname</strong><?php echo $lang['sharebind_list_popup_tip2'];?></p><p class="red"><?php echo $lang['sharebind_list_popup_tip3'];?>@apiname<?php echo $lang['sharebind_list_popup_tip4'];?></p></dd></dl>
<dl class="bottom"><dt style="width:25%">&nbsp;</dt>
        <dd style="width:75%"><a href="javascript:void(0);" nc_type="finishbtn" data-param='{"apikey":"@apikey"}' class="ncu-btn2 mr10"><?php echo $lang['sharebind_list_finishbind'];?></a><span><?php echo $lang['sharebind_list_unfinishedbind'];?><a target="_blank" href="<?php echo SHOP_SITE_URL;?>/api.php?act=sharebind&type=@apikey" class="ml5"><?php echo $lang['sharebind_list_againbind'];?></a></span></dd>
      </dl>
</div>
</textarea>
<script type="text/javascript">
$(function(){
	$("[nc_type='bindbtn']").bind('click',function(){
		var data_str = $(this).attr('data-param');
	    eval( "data_str = "+data_str);
	    var html = $("#bindtooltip_module").text();
	    //替换关键字
	    html = html.replace(/@apikey/g,data_str.apikey);
	    html = html.replace(/@apiname/g,data_str.apiname);
	    CUR_DIALOG = html_form("bindtooltip", "<?php echo $lang['sharebind_list_accountconnect'];?>", html, 360, 0);
	    window.open('api.php?act=sharebind&type='+data_str.apikey);
	});
	$("[nc_type='unbindbtn']").bind('click',function(){
		var data_str = $(this).attr('data-param');
	    eval( "data_str = "+data_str);
	    var confirm_tip = "<?php echo $lang['sharebind_list_unbind_confirmtip1'];?>"+data_str.apiname+"<?php echo $lang['sharebind_list_unbind_confirmtip2'];?>";
	    ajax_get_confirm(confirm_tip,'index.php?act=member_sharemanage&op=unbind&type='+data_str.apikey);
	});
	$("[nc_type='finishbtn']").live('click',function(){
		CUR_DIALOG.close();
		location.reload();
	});
});
</script>