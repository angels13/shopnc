<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
<div class="tabmenu"><?php include template('layout/submenu');?></div>
  <div class="bind">
    <?php if (!empty($output['member_info']['member_qqopenid'])){?>
    <input type="hidden" name="form_submit" value="ok"  />
    <h3 class="qq"><?php echo $lang['member_qqconnect_binding_tip_1'];?>&nbsp;<strong><?php echo $_SESSION['member_name'];?></strong>&nbsp;<?php echo $lang['member_qqconnect_binding_tip_2'];?> <?php echo $output['member_info']['member_qqinfoarr']['name'];?> <?php echo $lang['member_qqconnect_binding_tip_3'];?></h3>
    <div class="left">
      <form method="post" id="editbind_form" name="editbind_form" action="index.php?act=member_qqconnect&op=unbind">
        <input type='hidden' id="is_editpw" name="is_editpw" value='no'/>
        <dl>
          <dt><?php echo $lang['member_qqconnect_unbind_click']; ?></dt>
          <dd>
            <input class="submit" type="submit" value="<?php echo $lang['member_qqconnect_unbind_submit'];?>" />
          </dd>
        </dl>
      </form>
    </div>
    <div class="right">
      <form method="post" id="editpw_form" name="editpw_form" action="index.php?act=member_qqconnect&op=unbind">
        <input type='hidden' id="is_editpw" name="is_editpw" value='yes'/>
        <dl>
          <dd><span><?php echo $lang['member_qqconnect_modpw_newpw']; ?>:</span>
            <input type="password"  name="new_password" id="new_password"/>
            <label for="new_password" generated="true" class="error"></label>
          </dd>
          <dd><span><?php echo $lang['member_qqconnect_modpw_two_password']; ?>:</span>
            <input type="password"  name="confirm_password" id="confirm_password" />
            <label for="confirm_password" generated="true" class="error"></label>
          </dd>
          <dd><span>&nbsp;</span>
            <input class="submit" type="submit" value="<?php echo $lang['member_qqconnect_unbind_updatepw_submit'];?>" />
          </dd>
          <dd>
            <p class="hint"><?php echo $lang['member_qqconnect_modpw_tip_1']; ?>&nbsp;<?php echo $_SESSION['member_name']; ?>&nbsp;<?php echo $lang['member_qqconnect_modpw_tip_2'];?></p>
          </dd>
          <dd></dd>
        </dl>
      </form>
    </div>
    <div class="clear"></div>
    <?php } else {?>
      <div class="left">
        <p class="ico"><a href="<?php echo SHOP_SITE_URL;?>/api.php?act=toqq"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/qq_bind_small.gif"></a></a>
        <p class="hint"><?php echo $lang['member_qqconnect_binding_click']; ?></p>
      </div>
      <div class="right">
        <dl>
          <dt class="qq"><?php echo $lang['member_qqconnect_binding_goodtip_1']; ?></dt>
          <dd>
            <p><?php echo $lang['member_qqconnect_binding_goodtip_2']; ?></p>
            <p class="hint"><?php echo $lang['member_qqconnect_binding_goodtip_3']; ?></p>
          </dd>
        </dl>
      </div>
    <div class="clear"></div>
    <?php }?>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$("#unbind").hide();

    $('#editpw_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('td').next('td');
            error_td.find('.field_notice').hide();
            error_td.append(error);
        },
        rules : {
            new_password : {
                required   : true,
                minlength  : 6,
                maxlength  : 20
            },
            confirm_password : {
                required   : true,
                equalTo    : '#new_password'
            }
        },
        messages : {
            new_password  : {
                required   : '<?php echo $lang['member_qqconnect_new_password_null'];?>',
                minlength  : '<?php echo $lang['member_qqconnect_password_range'];?>'
            },
            confirm_password : {
                required   : '<?php echo $lang['member_qqconnect_ensure_password_null'];?>',
                equalTo    : '<?php echo $lang['member_qqconnect_input_two_password_again'];?>'
            }
        }
    });
});
function showunbind(){
	$("#unbind").show();
}
function showpw(){
	$("#is_editpw").val('yes');
	$("#editbinddiv").hide();
	$("#editpwul").show();
}
</script>