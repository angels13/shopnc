<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_point.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">
<div class="nc-layout-all">
  <div class="nc-layout-left">
    <div class="nc-user-info" style="background: #DEE6D8;">
      <?php if($_SESSION['is_login'] == '1'){?>
      <dl>
        <dt class="user-pic"><span class="thumb size60"><i></i><img src="<?php if ($output['member_info']['member_avatar']!='') { echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.DS.$output['member_info']['member_avatar']; } else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); } ?>" onload="javascript:DrawImage(this,60,60);"/></span></dt>
        <dd class="user-name"><?php echo $lang['pointprod_list_hello_tip1']; ?><?php echo $_SESSION['member_name'];?></dd>
        <dd class="user-pointprod"><?php echo $lang['pointprod_list_hello_tip2']; ?><strong><?php echo $output['member_info']['member_points']; ?></strong>&nbsp;<?php echo $lang['points_unit']; ?></dd>
        <dd class="user-pointprod-log"><a href="index.php?act=member_points" target="_blank"><?php echo $lang['pointprod_pointslog'];?></a></dd>
      </dl>
      <ul>
        <li><?php echo $lang['pointprod_list_hello_tip3']; ?>&nbsp;<a href="index.php?act=pointcart"><strong><?php echo $output['pcartnum']; ?></strong></a>&nbsp;<?php echo $lang['pointprod_pointprod_unit']; ?></li>
      </ul>
      <?php } else { ?>
      <dl>
      	<dt class="user-pic"><span class="thumb size60"><i></i><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); ?>" onload="javascript:DrawImage(this,60,60);"/></span></dt>
        <dd class="user-login"><?php echo $lang['pointprod_list_hello_tip5']; ?></dd>
        <dd class="user-login-btn"><a href="javascript:login_dialog();"><?php echo $lang['pointprod_list_hello_login']; ?></a></dd>
      </dl>
      <ul>
        <li><a href="<?php echo urlShop('article', 'show', array('article_id' => 39));?>" target="_blank"><?php echo $lang['pointprod_list_hello_pointrule']; ?></a></li>
        <li><a href="<?php echo urlShop('article', 'show', array('article_id' => 40));?>" target="_blank"><?php echo $lang['pointprod_list_hello_pointexrule']; ?></a></li>
      </ul>
      <?php }?>
    </div>
    <div class="nc-exchange-info">
      <div class="title"><?php echo $lang['pointprod_info_goods_exchangelist']; ?></div>
      <ul class="exchangeNote">
        <?php if (is_array($output['orderprod_list']) && count($output['orderprod_list'])>0){ ?>
        <?php foreach ($output['orderprod_list'] as $v){ ?>
        <li>
          <div class="picFloat">
            <div class="pic"><i></i><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['point_goodsid']));?>"> <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_POINTPROD.DS.str_ireplace('.', '_small.', $v['point_goodsimage']);?>" onload="javascript:DrawImage(this,64,64);" /></a> </div>
          </div>
          <div class="info">
            <p class="user"><?php echo str_cut($v['point_buyername'],'4').'***'; ?><?php echo $lang['pointprod_info_goods_alreadyexchange']; ?></p>
            <p class="name"><?php echo $v['point_goodsname']; ?></p>
          </div>
        </li>
        <?php } ?>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="nc-layout-right">
    <div class="left2 giftsClass">
      <div class="gift_con">
        <div class="giftWare" >
          <div class="title">
            <h2><?php echo $output['prodinfo']['pgoods_name'];?></h2>
          </div>
          <div class="wareInfo">
            <div class="warePic">
              <div class="picFloat">
                <div class="pic"><i></i><a href="<?php echo $output['prodinfo']['pgoods_image']; ?>"><img src="<?php echo $output['prodinfo']['pgoods_image']; ?>" onload="javascript:DrawImage(this,300,300);"></a></div>
              </div>
            </div>
            <div class="wareText">
              <div class="rate">
                <h3><?php echo $lang['pointprod_info_needpoint'].$lang['nc_colon']; ?><span><?php echo $output['prodinfo']['pgoods_points']; ?><?php echo $lang['points_unit']; ?></span></h3>
                <p class="hr">&nbsp;</p>
                <?php if($_SESSION['is_login'] == '1'){ ?>
                <h4><?php echo $lang['pointprod_info_mypoint'].$lang['nc_colon']; ?><span><?php echo $output['member_info']['member_points']; ?><?php echo $lang['points_unit']; ?></span></h4>
                <?php } ?>
                <h4><?php echo $lang['pointprod_goodsprice'].$lang['nc_colon']; ?><span class="cost"><?php echo $lang['currency'].$output['prodinfo']['pgoods_price']; ?></span></h4>
                <h4><?php echo $lang['pointprod_info_goods_serial'].$lang['nc_colon']; ?><span class="cost"><?php echo $output['prodinfo']['pgoods_serial']; ?></span></h4>
                <p class="hr">&nbsp;</p>
                <!-- 兑换时间 -->
                <div class="exchange">
                  <?php if ($output['prodinfo']['pgoods_islimittime'] == 1){ ?>
                  <h6><?php echo $lang['pointprod_info_goods_limittime'].$lang['nc_colon']; ?>
                    <?php if ($output['prodinfo']['pgoods_starttime'] && $output['prodinfo']['pgoods_endtime']){
              		echo @date('Y-m-d H:i:s',$output['prodinfo']['pgoods_starttime']).'&nbsp;'.$lang['pointprod_info_goods_limittime_to'].'&nbsp;'.@date('Y-m-d H:i:s',$output['prodinfo']['pgoods_endtime']);
              	}?>
                  </h6>
                  <?php if ($output['prodinfo']['ex_state'] == 'going'){?>
                  <h6><?php echo $lang['pointprod_info_goods_lasttime']; ?>&nbsp;&nbsp;<i id="dhpd"><?php echo $output['prodinfo']['timediff']['diff_day']; ?></i> <?php echo $lang['pointprod_info_goods_lasttime_day']; ?> <i id="dhph"><?php echo $output['prodinfo']['timediff']['diff_hour']; ?></i> <?php echo $lang['pointprod_info_goods_lasttime_hour']; ?> <i id="dhpm"><?php echo $output['prodinfo']['timediff']['diff_mins']; ?></i> <?php echo $lang['pointprod_info_goods_lasttime_mins']; ?> <i id="dhps"><?php echo $output['prodinfo']['timediff']['diff_secs']; ?></i> <?php echo $lang['pointprod_info_goods_lasttime_secs']; ?></h6>
                  <?php }?>
                  <?php } ?>
                  <!-- 剩余库存 -->
                  <?php if ($output['prodinfo']['ex_state'] == 'going'){?>
                  <h6><?php echo $lang['pointprod_info_goods_lastnum'].$lang['nc_colon']; ?><?php echo $output['prodinfo']['pgoods_storage']; ?>
                    <input type="hidden" id="storagenum" value="<?php echo $output['prodinfo']['pgoods_storage']; ?>"/>
                  </h6>
                  <?php }?>
                  <!-- 兑换按钮 -->
                  <?php if ($output['prodinfo']['ex_state'] == 'willbe'){ ?>
                  <span class="btn-off"><i class="ico"></i><?php echo $lang['pointprod_willbe']; ?></span>
                  <?php }elseif ($output['prodinfo']['ex_state'] == 'end') {?>
                  <span class="btn-off"><i class="ico"></i><?php echo $lang['pointprod_exchange_end']; ?></span>
                  <?php }else{?>
                  <h6><?php echo $lang['pointprod_info_goods_exchangenum'].$lang['nc_colon']; ?>
                    <input name="exnum" type="text" class="text" id="exnum" value='1' size="4"/>
                  </h6>
                  <span class="btn-on" onclick="return add_to_cart();"><i class="ico"></i><?php echo $lang['pointprod_exchange']; ?></span>
                  <!-- 限制兑换数量 -->
                  <?php if ($output['prodinfo']['pgoods_islimit'] == 1){?>
                  <h5><?php echo $lang['pointprod_info_goods_limitnum_tip1']; ?><?php echo $output['prodinfo']['pgoods_limitnum']; ?><?php echo $lang['pointprod_pointprod_unit']; ?></h5>
                  <input type="hidden" id="limitnum" value="<?php echo $output['prodinfo']['pgoods_limitnum']; ?>"/>
                  <?php }else {?>
                  <input type="hidden" id="limitnum" value=""/>
                  <?php } ?>
                  <?php }?>
                </div>
                <p class="hr">&nbsp;</p>
                <dl class="copyUrl">
                  <dt><?php echo $lang['pointprod_info_goods_share'].$lang['nc_colon']; ?></dt>
                  <dd>
                    <input id="shareurl" type="text" class="url" value="<?php echo urlShop('pointprod', 'pinfo', array('id' => $output['prodinfo']['pgoods_id']));?>" readonly="readonly" >
                    <input type="button" class="copyBtn" value="<?php echo $lang['pointprod_info_goods_copyurl']; ?>" onclick="javascript:copy_url();"/>
                  </dd>
                </dl>
                <dl>
                  <dt><?php echo $lang['pointprod_info_goods_collectionurl'].$lang['nc_colon']; ?></dt>
                  <dd>
                    <!-- Baidu Button BEGIN -->
                    <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare"> <span class="bds_more"><?php echo $lang['pointprod_info_goods_share_to'];?></span> <a class="bds_tqq"></a> <a class="bds_tsina"></a> <a class="bds_renren"></a> <a class="bds_ty"></a> <a class="bds_qq"></a> <a class="bds_baidu"></a> <a class="bds_taobao"></a> <a class="bds_copy"></a> <a class="shareCount"></a> </div>
                    <script type="text/javascript" id="bdshare_js" data="type=tools" ></script>
                    <script type="text/javascript" id="bdshell_js"></script>
                    <script type="text/javascript">
				document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?t=" + new Date().getHours();
			</script>
                    <!-- Baidu Button END -->
                  </dd>
                </dl>
              </div>
            </div>
            <div class="clear"></div>
          </div>
          <div class="wareIntro">
            <ul class="userMenu">
              <li><?php echo $lang['pointprod_info_goods_description']; ?></li>
            </ul>
            <div class="con"> <?php echo $output['prodinfo']['pgoods_body']; ?> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/home.js" id="dialog_js" charset="utf-8"></script>
<script>
 function copy_url()
 {
	 var txt = $("#shareurl").val();
	 if(window.clipboardData)
	    {
	        // the IE-manier
	        window.clipboardData.clearData();
	        window.clipboardData.setData("Text", txt);
	        alert("<?php echo $lang['pointprod_info_goods_urlcopy_succcess'];?>");
	    }
	    else if(navigator.userAgent.indexOf("Opera") != -1)
	    {
	        window.location = txt;
	        alert("<?php echo $lang['pointprod_info_goods_urlcopy_succcess'];?>");
	    }
	    else if (window.netscape)
	    {
	        // dit is belangrijk maar staat nergens duidelijk vermeld:
	        // you have to sign the code to enable this, or see notes below
	        try {
	            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	        } catch (e) {
	            alert("<?php echo $lang['pointprod_info_goods_urlcopy_fail'];?>!\n<?php echo $lang['pointprod_info_goods_urlcopy_fail1'];?>\'about:config\'<?php echo $lang['pointprod_info_goods_urlcopy_fail2'];?>\n<?php echo $lang['pointprod_info_goods_urlcopy_fail3'];?>\'signed.applets.codebase_principal_support\'<?php echo $lang['pointprod_info_goods_urlcopy_fail4'];?>\'true\'");
	            return false;
	        }
	        // maak een interface naar het clipboard
	        var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
	        if (!clip){return;}
	        // alert(clip);
	        // maak een transferable
	        var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
	        if (!trans){return;}
	        // specificeer wat voor soort data we op willen halen; text in dit geval
	        trans.addDataFlavor('text/unicode');
	        // om de data uit de transferable te halen hebben we 2 nieuwe objecten
	        // nodig om het in op te slaan
	        var str = new Object();
	        var len = new Object();
	        str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
	        var copytext = txt;
	        str.data = copytext;
	        trans.setTransferData("text/unicode",str,copytext.length*2);
	        var clipid = Components.interfaces.nsIClipboard;
	        if (!clip){return false;}
	        clip.setData(trans,null,clipid.kGlobalClipboard);
	        alert("<?php echo $lang['pointprod_info_goods_urlcopy_succcess'];?>");
	    }
 }
function GetRTime2() //积分礼品兑换倒计时
{
   var rtimer=null;
   var startTime = new Date();
   var EndTime = <?php echo intval($output['prodinfo']['pgoods_endtime'])*1000;?>;
   var NowTime = new Date();
   var nMS =EndTime - NowTime.getTime();
   if(nMS>0)
   {
       var nD=Math.floor(nMS/(1000*60*60*24));
       var nH=Math.floor(nMS/(1000*60*60)) % 24;
       var nM=Math.floor(nMS/(1000*60)) % 60;
       var nS=Math.floor(nMS/1000) % 60;
       document.getElementById("dhpd").innerHTML=pendingzero(nD);
       document.getElementById("dhph").innerHTML=pendingzero(nH);
       document.getElementById("dhpm").innerHTML=pendingzero(nM);
       document.getElementById("dhps").innerHTML=pendingzero(nS);
       if(nS==0&&nH==0&&nM==0)
       {
          // document.getElementById("returntime").style.display='none';
           clearTimeout(rtimer2);
           window.location.href=window.location.href;
           return;
       }
       rtimer2=setTimeout("GetRTime2()",1000);
   }
}
GetRTime2();
function pendingzero(str)
{
   var result=str+"";
   if(str<10)
   {
       result="0"+str;
   }
   return result;
}
//加入购物车
function add_to_cart()
{
	var storagenum = parseInt($("#storagenum").val());//库存数量
	var limitnum = parseInt($("#limitnum").val());//限制兑换数量
	var quantity = parseInt($("#exnum").val());//兑换数量
	//验证数量是否合法
	var checkresult = true;
	var msg = '';
	if(!quantity >=1 ){//如果兑换数量小于1则重新设置兑换数量为1
		quantity = 1;
	}
	if(limitnum > 0 && quantity > limitnum){
		checkresult = false;
		msg = '<?php echo $lang['pointprod_info_goods_exnummaxlimit_error']; ?>';
	}
	if(storagenum > 0 && quantity > storagenum){
		checkresult = false;
		msg = '<?php echo $lang['pointprod_info_goods_exnummaxlast_error']; ?>';
	}
	if(checkresult == false){
		alert(msg);
		return false;
	}else{
		window.location.href = '<?php echo SHOP_SITE_URL; ?>/index.php?act=pointcart&op=add&pgid=<?php echo $output['prodinfo']['pgoods_id']; ?>&quantity='+quantity;
	}
}
</script>
