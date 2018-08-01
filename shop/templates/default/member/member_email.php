<?php defined('InShopNC') or exit('Access Invalid!');?>


<div class="wrap">
<div class="tabmenu"><?php include template('layout/submenu');?></div>
  <div class="ncu-form-style">
    <form method="post" id="email_form" action="index.php?act=home&op=email">
      <input type="hidden" name="form_submit" value="ok" />
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_member_your_password'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="password" class="password"  maxlength="40" name="orig_password" id="orig_password" />
          <label for="orig_password" generated="true" class="error"></label>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_member_email'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="text" class="text"  maxlength="40" name="email" id="email" />
          <label for="email" generated="true" class="error"></label>
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
    $('#email_form').validate({
        submitHandler:function(form){
            ajaxpost('email_form', '', '', 'onerror') 
        },
        rules : {
            orig_password : {
                required : true
            },
           email : {
                required   : true,
                email      : true,
                remote   : {
                    url : 'index.php?act=login&op=check_email',
                    type: 'get',
                    data:{
                        email : function(){
                            return $('#email').val();
                        }
                    }
                }
            }
        },
        messages : {
            orig_password : {
                required : '<?php echo $lang['home_member_password_null'];?>'
            },
            email : {
                required : '<?php echo $lang['home_member_email_null'];?>',
                email    : '<?php echo $lang['home_member_email_format_wrong'];?>',
				remote	 : '<?php echo $lang['home_member_email_exists'];?>'
            }
        }
    });
});
</script>
