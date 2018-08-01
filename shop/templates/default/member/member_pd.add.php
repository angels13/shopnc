<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <div class="ncu-form-style">
    <form method="post" id="recharge_form" action="index.php">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="act" value="predeposit" />
      <input type="hidden" name="op" value="recharge_add" />
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['predeposit_recharge_price'].$lang['nc_colon']; ?></dt>
        <dd>
          <input name="pdr_amount" type="text" class="text w50 mr5" id="pdr_amount" maxlength="8"/>
          <?php echo $lang['currency_zh'];?> </dd>
      </dl>
      <dl class="bottom">
        <dt>&nbsp; </dt>
        <dd>
          <input type="submit" class="submit" value="<?php echo $lang['nc_submit']; ?>" />
        </dd>
      </dl>
    </form>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$('#recharge_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        rules : {
        	pdr_amount      : {
	        	required  : true,
	            number    : true,
	            min       : 0.01
            }
        },
        messages : {
        	pdr_amount		: {
            	required  :'<?php echo $lang['predeposit_recharge_add_pricenull_error']; ?>',
            	number    :'<?php echo $lang['predeposit_recharge_add_pricemin_error']; ?>',
                min    	  :'<?php echo $lang['predeposit_recharge_add_pricemin_error']; ?>'
            }
        }
    });
});
</script>