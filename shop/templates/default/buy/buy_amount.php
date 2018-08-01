<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="ncc-bottom"> <a href="javascript:void(0)" id='submitOrder' class="ncc-btn ncc-btn-acidblue fr"><?php echo $lang['cart_index_submit_order'];?></a> </div>
<script>
function submitNext(){
    if ($('#address_id').val() == ''){
		showDialog('<?php echo $lang['cart_step1_please_set_address'];?>', 'error','','','','','','','','',2);
		return;
	}
	if ($('#buy_city_id').val() == '') {
		showDialog('正在计算运费,请稍后', 'error','','','','','','','','',2);
		return;
	}
	if ($('input[name="pd_pay"]').attr('checked') && $('#password_callback').val() != '1') {
		showDialog('使用预存款支付，需输入登录密码并使用  ', 'error','','','','','','','','',2);
		return;
	}
	$('#order_form').submit();
}
$(function(){
	$('#submitOrder').on('click',function(){submitNext()});
	calcOrder();
});
</script>