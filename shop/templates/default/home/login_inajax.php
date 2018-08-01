<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="quick-login">
  <form id="login_form" action="<?php echo SHOP_SITE_URL;?>/index.php?act=login" method="post" onsubmit="ajaxpost('login_form', '<?php echo SHOP_SITE_URL;?>/index.php?act=login', '', 'onerror');return false;"  class="bg" >
    <?php Security::getToken();?>
    <input type="hidden" name="form_submit" value="ok" />
    <input name="nchash" type="hidden" value="<?php echo getNchash();?>" />
    <dl>
      <dt><?php echo $lang['login_index_username'];?></dt>
      <dd>
        <input type="text" class="text" autocomplete="off"  name="user_name" id="user_name">
      </dd>
    </dl>
    <dl>
      <dt><?php echo $lang['login_index_password'];?></dt>
      <dd>
        <input type="password" class="text" name="password" autocomplete="off"  id="password">
      </dd>
    </dl>
    <?php if(C('captcha_status_login') == '1') { ?>
    <dl>
      <dt><?php echo $lang['login_index_checkcode'];?></dt>
      <dd>
        <input type="text" name="captcha" class="text fl w60" id="captcha" maxlength="4" size="10" />
        <img class="fl ml10" src="<?php echo SHOP_SITE_URL?>/index.php?act=seccode&op=makecode&nchash=<?php echo getNchash();?>" title="<?php echo $lang['login_index_change_checkcode'];?>" name="codeimage" border="0" id="codeimage" onclick="this.src='<?php echo SHOP_SITE_URL?>/index.php?act=seccode&op=makecode&nchash=<?php echo getNchash();?>&t=' + Math.random()"><span></span></dd>
    </dl>
    <?php } ?>
    <ul>
      <li><?php echo $lang['quick_login_please_regist1']?><a href="<?php echo SHOP_SITE_URL?>/index.php?act=login&op=register" class="register"><?php echo $lang['quick_login_please_regist2']?></a><?php echo $lang['quick_login_please_regist3']?></li>
      <li><?php echo $lang['quick_login_please_forget1']?><a href="<?php echo SHOP_SITE_URL?>/index.php?act=login&op=forget_password" class="forget"><?php echo $lang['quick_login_please_forget2']?></a><?php echo $lang['quick_login_please_forget3']?></li>
    </ul>
    <div class="enter">
      <input type="submit" class="submit" value="&nbsp;" name="Submit">
      <?php if ($GLOBALS['setting_config']['qq_isuse'] == 1 || $GLOBALS['setting_config']['sina_isuse'] == 1){?>
      <span class="other">
      <?php if ($GLOBALS['setting_config']['qq_isuse'] == 1){?>
      <a href="<?php echo SHOP_SITE_URL;?>/api.php?act=toqq" title="QQ" class="qq">&nbsp;</a>
      <?php } ?>
      <?php if ($GLOBALS['setting_config']['sina_isuse'] == 1){?>
      <a href="<?php echo SHOP_SITE_URL;?>/api.php?act=tosina" title="<?php echo $lang['nc_otherlogintip_sina']; ?>" class="sina">&nbsp;</a>
      <?php } ?>
      </span>
      <?php } ?>
    </div>
    <input type="hidden" value="<?php echo $_GET['ref_url']?>" name="ref_url">
  </form>
</div>