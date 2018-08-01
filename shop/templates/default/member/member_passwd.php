<?php defined('InShopNC') or exit('Access Invalid!');?>


<div class="wrap">
<div class="tabmenu"><?php include template('layout/submenu');?></div>
  <div class="ncu-form-style">
    <form method="post" id="password_form" name="password_form" action="index.php?act=home&op=passwd">
      <input type="hidden" name="form_submit" value="ok"  />
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_member_your_password'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="password"  maxlength="40" class="text" name="orig_password" id="orig_password" />
          <label for="orig_password" generated="true" class="error"></label>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_member_new_password'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="password"  maxlength="40" class="password" name="new_password" id="new_password"/>
          <label for="new_password" generated="true" class="error"></label>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_member_ensure_password'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="password" maxlength="40" class="password" name="confirm_password" id="confirm_password" />
          <label for="confirm_password" generated="true" class="error"></label>
        </dd>
      </dl>
      <dl class="bottom">
        <dt>&nbsp;</dt>
        <dd>
          <input type="submit" class="submit" value="<?php echo $lang['home_member_submit'];?>" />
        </dd>
      </dl>
    </form>
  </div>
</div>
<script type="text/javascript">
$(function(){
    $('#password_form').validate({
         submitHandler:function(form){
            ajaxpost('password_form', '', '', 'onerror') 
        },   
        rules : {
            orig_password : {
                required : true
            },
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
            orig_password : {
                required : '<?php echo $lang['home_member_old_password_null'];?>'
            },
            new_password  : {
                required   : '<?php echo $lang['home_member_new_password_null'];?>',
                minlength  : '<?php echo $lang['home_member_password_range'];?>'
            },
            confirm_password : {
                required   : '<?php echo $lang['home_member_ensure_password_null'];?>',
                equalTo    : '<?php echo $lang['home_member_diffent_password'];?>'
            }
        }
    });
});
</script> 
