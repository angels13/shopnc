<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_point.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">
<div class="nc-layout-all">
<div class="nc-layout-left">
  <div class="nc-user-info">
    <?php if($_SESSION['is_login'] == '1'){?>
    <dl>
      <dt class="user-pic"><span class="thumb size60"><i></i><img src="<?php if ($output['member_info']['member_avatar']!='') { echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.DS.$output['member_info']['member_avatar']; } else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); } ?>" onload="javascript:DrawImage(this,60,60);"/></span></dt>
      <dd class="user-name"><?php echo $lang['pointprod_list_hello_tip1']; ?><?php echo $_SESSION['member_name'];?></dd>
      <dd class="user-pointprod"><?php echo $lang['pointprod_list_hello_tip2']; ?><strong><?php echo $output['member_info']['member_points']; ?></strong>&nbsp;<?php echo $lang['points_unit']; ?></dd>
      <dd class="user-pointprod-log"><a href="index.php?act=member_points" target="_blank"><?php echo $lang['pointprod_pointslog'];?></a></dd>
    </dl>
    <ul>
      <?php if (C('pointprod_isuse')==1){?>
      <li><?php echo $lang['pointprod_list_hello_tip3']; ?>&nbsp;<a href="index.php?act=pointcart"><strong><?php echo $output['pcartnum']; ?></strong></a>&nbsp;<?php echo $lang['pointprod_pointprod_unit']; ?></li>
      <?php }?>
      <?php if (C('voucher_allow')==1){?>
      <li><?php echo $lang['home_voucher_usable'];?>&nbsp;<a href="index.php?act=member_voucher"><strong><?php echo $output['vouchercount'];?></strong></a>&nbsp;<?php echo $lang['home_voucher_unit'];?></li>
      <?php }?>
    </ul>
    <?php } else { ?>
    <dl>
      <dt class="user-pic"><span class="thumb size60"><i></i><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); ?>" onload="javascript:DrawImage(this,60,60);"/></span></dt>
      <dd class="user-login"><?php echo $lang['pointprod_list_hello_tip5']; ?></dd>
      <dd class="user-login-btn"><a href="javascript:login_dialog();"><?php echo $lang['pointprod_list_hello_login']; ?></a></dd>
    </dl>
    <?php if (C('pointprod_isuse')==1){?>
    <ul>
      <li><a href="<?php echo urlShop('article', 'show', array('article_id' => 39));?>" target="_blank"><?php echo $lang['pointprod_list_hello_pointrule']; ?></a></li>
      <li><a href="<?php echo urlShop('article', 'show', array('article_id' => 40));?>" target="_blank"><?php echo $lang['pointprod_list_hello_pointexrule']; ?></a></li>
    </ul>
    <?php }?>
    <?php }?>
  </div>
  <?php if (C('voucher_allow')==1){?>
  <div class="nc-voucher-info">
    <dl>
      <dt>
        <h3><?php echo $lang['home_voucher_title'];?></h3>
        <span><?php echo $lang['home_voucher_title_desc'];?></span></dt>
      <?php if (!empty($output['pricelist'])){?>
      <dd>
        <ul>
          <?php foreach ($output['pricelist'] as $k=>$v){?>
          <li><a href="<?php echo urlShop('pointvoucher', 'index');?>#price<?php echo $v['voucher_price'];?>" target="_self"><?php echo $v['voucher_price'];?><?php echo $lang['currency_zh'].$lang['home_voucher'];?></a></li>
          <?php }?>
        </ul>
      </dd>
      <?php }?>
    </dl>
  </div>
  <?php }?>
  <?php if (C('pointprod_isuse')==1){?>
  <div class="nc-exchange-info">
    <dl>
      <dt>
        <h3><?php echo $lang['pointprod_ad_text1'];?></h3>
        <span><?php echo $lang['pointprod_ad_text2'];?></span></dt>
      <dd>
        <h4><?php echo $lang['pointprod_list_top3']; ?></h4>
        <ul>
          <?php if (is_array($output['converlist']) && count($output['converlist'])){?>
          <?php foreach ($output['converlist'] as $v){?>
          <li>
            <dl class="nc-exchange-gift">
              <dt class="gift-name"><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['pgoods_id']));?>" target="_blank"><?php echo $v['pgoods_name']; ?></a></dt>
              <dd class="gift-pic"><i></i><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['pgoods_id']));?>" target="_blank"><img src="<?php echo $v['pgoods_image'] ?>" onload="javascript:DrawImage(this,160,160);" title="<?php echo $v['pgoods_name']; ?>" alt="<?php echo $v['pgoods_name']; ?>"></a></dd>
              <dd class="gift-price"><?php echo $lang['pointprod_goodsprice'].$lang['nc_colon']; ?><em><?php echo $lang['currency'].$v['pgoods_price']; ?></em></dd>
              <dd class="gift-pointpord"><?php echo $lang['pointprod_exchange_point'].$lang['nc_colon']; ?><span><?php echo $v['pgoods_points']; ?></span><?php echo $lang['points_unit']; ?></dd>
            </dl>
          </li>
          <?php } ?>
          <?php }?>
        </ul>
      </dd>
    </dl>
  </div>
  <?php }?>
</div>
<div class="nc-layout-right">
<?php echo loadadv(35,'html');?>
	<?php if (C('voucher_allow')==1){?>
    <div class="nc-voucher-list">
      <div class="title">
        <h3><?php echo $lang['home_voucher_newtitle'];?></h3><span><a href="<?php echo urlShop('pointvoucher', 'index');?>"><?php echo $lang['home_voucher_moretitle'];?></a></span></div>
      <?php if (!empty($output['new_voucher'])){?>
      <ul>
      	<?php foreach ($output['new_voucher'] as $k=>$v){?>
        <li class="item">
          <div class="voucher-pic" nc_type="exchangebtn" data-param='{"vid":"<?php echo $v['voucher_t_id'];?>"}'>
          	<span class="thumb size160"><i></i>
          		<img src="<?php echo $v['voucher_t_customimg'];?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(240);?>'" onload="javascript:DrawImage(this,160,160);" />
          	</span>
            <dl>
              <dt><?php echo $lang['home_voucher'];?></dt>
              <dd><?php echo $lang['home_voucher_desc_1']; ?><?php echo $v['voucher_t_limit'];?><?php echo $lang['currency_zh'].$lang['home_voucher_desc_2'];?><em><?php echo $lang['currency'].$v['voucher_t_price'];?></em></dd>
            </dl>
          </div>
          <div class="voucher-store">
            <p class="logo">
            	<span class="thumb size160-48"><i></i>
            		<img src="<?php echo getStoreLogo($v['store_label']);?>"  onload="javascript:DrawImage(this,160,48);" >
            	</span></p>
            <p class="name"><a target="_blank" href="<?php echo urlShop('show_store', 'index', array('store_id'=>$v['store_id']));?>"><?php echo $v['store_name'];?></a></p>
          </div>
        </li>
        <?php }?>
      </ul>
      <?php }else{?>
        <div class="norecord"><?php echo $lang['home_voucher_list_null'];?></div>
      <?php }?>
    </div>
    <?php }?>
    <?php if (C('pointprod_isuse')==1){?>
    <div class="nc-exchange-list">
      <div class="title"><h3><?php echo $lang['pointprod_list_newprod'];?></h3><span><a href="<?php echo urlShop('pointprod', 'plist');?>"><?php echo $lang['pointprod_list_more'];?></a></span></div>
      <?php if (is_array($output['new_pointsprod']) && count($output['new_pointsprod'])>0){?>
      <ul>
        <?php foreach ($output['new_pointsprod'] as $k=>$v){?>
        <li class="item">
          <dl class="nc-exchange-gift">
            <dt class="gift-name"><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['pgoods_id']));?>" target="_blank" tile="<?php echo $v['pgoods_name']; ?>"><?php echo $v['pgoods_name']; ?></a></dt>
            <dd class="gift-pic"><i></i> <a target="_blank" href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['pgoods_id']));?>"> <img src="<?php echo $v['pgoods_image'] ?>" onload="javascript:DrawImage(this,160,160);" alt="<?php echo $v['pgoods_name']; ?>" /> </a></dd>
            <dd class="gift-price"><?php echo $lang['pointprod_goodsprice'].$lang['nc_colon']; ?><em><?php echo $lang['currency'].$v['pgoods_price']; ?></em></dd>
            <dd class="gift-pointpord"><?php echo $lang['pointprod_pointsname'].$lang['nc_colon'];?><?php echo $v['pgoods_points']; ?><?php echo $lang['points_unit']; ?></dd>
          </dl>
        </li>
        <?php } ?>
      </ul>
      <?php }else{?>
        <div class="norecord"><?php echo $lang['pointprod_list_null'];?></div>
      <?php }?>
    </div>
    <?php }?>
</div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/home.js" id="dialog_js" charset="utf-8"></script>
