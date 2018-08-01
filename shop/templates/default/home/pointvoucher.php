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
        <li><?php echo $lang['home_voucher_usable'];?>&nbsp;<a href="index.php?act=member_voucher"><strong><?php echo $output['vouchercount'];?></strong></a>&nbsp;<?php echo $lang['home_voucher_unit'];?></li>
      </ul>
      <?php } else { ?>
      <dl>
        <dt class="user-pic"><span class="thumb size60"><i></i><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); ?>" onload="javascript:DrawImage(this,60,60);"/></span></dt>
        <dd class="user-login"><?php echo $lang['pointprod_list_hello_tip5']; ?></dd>
        <dd class="user-login-btn"><a href="javascript:login_dialog();"><?php echo $lang['pointprod_list_hello_login']; ?></a></dd>
      </dl>
      <ul>
        <li><a href="<?php echo urlShop('article', 'show', array('article_id' => 39));?>" target="_blank"><?php echo $lang['pointprod_list_hello_pointrule']; ?></a></li>
      </ul>
      <?php }?>
    </div>
    <div class="nc-voucher-info">
      <dl>
        <dt>
          <h3><?php echo $lang['home_voucher_title'];?></h3>
          <span><?php echo $lang['home_voucher_title_desc'];?></span></dt>
        <?php if (!empty($output['voucherlist'])){?>
        <dd>
          <ul>
            <?php foreach ($output['voucherlist'] as $k=>$v){?>
            <li><a href="#price<?php echo $k;?>" target="_self"><?php echo $k;?><?php echo $lang['currency_zh'].$lang['home_voucher'];?></a></li>
            <?php }?>
          </ul>
        </dd>
        <?php }?>
      </dl>
    </div>
  </div>
  <div class="nc-layout-right">
    <?php if (!empty($output['voucherlist'])){?>
    <?php foreach ($output['voucherlist'] as $k=>$v){?>
    <div id="price<?php echo $k;?>" class="nc-voucher-list">
      <div class="title">
        <h3><?php echo $k;?><?php echo $lang['currency_zh'].$lang['home_voucher'];?></h3>
      </div>
      <?php if (!empty($v['voucher'])){?>
      <ul>
        <?php foreach ($v['voucher'] as $k_voucher=>$v_voucher){?>
        <li class="item">
          <div class="voucher-pic" nc_type='exchangebtn' data-param='{"vid":"<?php echo $v_voucher['voucher_t_id'];?>"}'> <span class="thumb size160"><i></i> <img src="<?php echo $v_voucher['voucher_t_customimg'];?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(240);?>'" onload="javascript:DrawImage(this,160,160);" /> </span>
            <dl>
              <dt><?php echo $lang['home_voucher'];?></dt>
              <dd><?php echo $lang['home_voucher_desc_1']; ?><?php echo $v_voucher['voucher_t_limit'];?><?php echo $lang['currency_zh'].$lang['home_voucher_desc_2']; ?><em><?php echo $lang['currency'].$v_voucher['voucher_t_price'];?></em></dd>
            </dl>
          </div>
          <div class="voucher-store">
            <p class="logo"> <span class="thumb size160-48"><i></i> <img src="<?php echo getStoreLogo($v_voucher['store_label']);?>"  onload="javascript:DrawImage(this,160,48);" > </span></p>
            <p class="name"><a target="_blank" href="<?php echo urlShop('show_store', 'index', array('store_id'=>$v_voucher['store_id']));?>"><?php echo $v_voucher['store_name'];?></a></p>
          </div>
        </li>
        <?php }?>
      </ul>
      <?php }else{?>
      <div class="norecord"><?php echo $lang['home_voucher_list_null'];?></div>
      <?php }?>
    </div>
    <?php }?>
    <?php }?>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/home.js" id="dialog_js" charset="utf-8"></script> 
