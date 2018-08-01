<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <div class="ncu-form-style">
    <form method="post" id="cash_form" action="index.php?act=predeposit&op=pd_cash_add">
      <input type="hidden" name="form_submit" value="ok" />
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['predeposit_cash_price'].$lang['nc_colon']; ?></dt>
        <dd>
          <p>
            <input name="pdc_amount" type="text" class="text w50 mr5" id="pdc_amount" maxlength="10" >
            <?php echo $lang['currency_zh'];?></p>
          <p class="hint mt5"><?php echo $lang['predeposit_cash_price_tip'].$lang['nc_colon']; ?><?php echo floatval($output['member_info']['available_predeposit']); ?>&nbsp;&nbsp;<?php echo $lang['currency_zh']; ?></p>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['predeposit_cash_shoukuanbank'].$lang['nc_colon']; ?></dt>
        <dd>
          <p><input name="pdc_bank_name" type="text" class="text w200 mr5" id="pdc_bank_name" maxlength="40"/></p>
          <p class="hint"><?php echo $lang['predeposit_cash_shoukuanname_tip']; ?></p>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['predeposit_cash_shoukuanaccount'].$lang['nc_colon'];?></dt>
        <dd>
          <p>
            <input name="pdc_bank_no" type="text" class="text w200 mr5" id="pdc_bank_no" maxlength="30"/>
          </p>
          <p class="hint"><?php echo $lang['predeposit_cash_shoukuanaccount_tip']; ?></p>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em>&nbsp;<?php echo $lang['predeposit_cash_shoukuanname'].$lang['nc_colon'];?></dt>
        <dd>
          <p><input name="pdc_bank_user" type="text" class="text w100 mr5" id="pdc_bank_user" maxlength="10"/></p>
          <p class="hint"><?php echo $lang['predeposit_cash_shoukuanauser_tip']; ?></p>
          </dd>
      </dl>
      <dl class="bottom">
        <dt>&nbsp; </dt>
        <dd>
          <input type="submit"  class="submit" value="<?php echo $lang['nc_submit']; ?>" />
        </dd>
      </dl>
    </form>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$('#cash_form').validate({
    	submitHandler:function(form){
			ajaxpost('cash_form', '', '', 'onerror')
		},
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        rules : {
	        pdc_amount      : {
	        	required  : true,
	            number    : true,
	            min       : 0.01,
	            max       : <?php echo floatval($output['member_info']['available_predeposit']); ?>
            },
            pdc_bank_name :{
            	required  : true
            },
            pdc_bank_no : {
            	required  : true
            },
            pdc_bank_user : {
	        	required  : true
	        }
        },
        messages : {
        	pdc_amount	  : {
            	required  :'<?php echo $lang['predeposit_cash_add_pricenull_error']; ?>',
            	number    :'<?php echo $lang['predeposit_cash_add_pricemin_error']; ?>',
            	min    	  :'<?php echo $lang['predeposit_cash_add_pricemin_error']; ?>',
            	max       :'<?php echo $lang['predeposit_cash_add_enough_error']; ?>'
            },
            pdc_bank_name :{
            	required   :'<?php echo $lang['predeposit_cash_add_shoukuanbanknull_error']; ?>'
            },
            pdc_bank_no : {
            	required   :'<?php echo $lang['predeposit_cash_add_shoukuanaccountnull_error']; ?>'
            },
            pdc_bank_user : {
	        	required  : '<?php echo $lang['predeposit_cash_add_shoukuannamenull_error']; ?>'
	        }
        }
    });
});
</script>