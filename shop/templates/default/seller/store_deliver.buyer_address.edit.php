<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="eject_con">
  <div class="adds">
    <div id="warning"></div>
    <form method="post" action="index.php?act=store_deliver&op=buyer_address&order_id=<?php echo $_GET['order_id'];?>" id="address_form" target="_parent">
      <input type="hidden" name="form_submit" value="ok" />
      <dl>
        <dt class="required"><?php echo $lang['member_address_receiver_name'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="text" class="text" name="new_reciver_name" id="new_reciver_name" value="<?php echo $output['address_info']['reciver_name'];?>"/>
        </dd>
      </dl>
      <dl>
        <dt class="required"><?php echo $lang['member_address_address'].$lang['nc_colon'];?></dt>
        <dd>
          <textarea class="textarea w300" name="new_address" id="new_address"><?php echo $output['address_info']['reciver_info']['address'];?></textarea>
        </dd>
      </dl>
      <dl>
        <dt class="required"><?php echo $lang['member_address_phone'].$lang['nc_colon'];?></dt>
        <dd>
          <input type="text" class="text" name="new_phone" id="new_phone" value="<?php echo $output['address_info']['reciver_info']['phone'];?>"/>
        </dd>
      </dl>
      <div class="bottom"> <a href="javascript:void(0);" id="submit" class="submit"><?php echo $lang['nc_common_button_save'];?></a> </div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#address_form').validate({
        rules : {
            true_name : {
                required : true
            },
            address : {
                required : true
            },
            phone : {
                required : true
            }
        },
        messages : {
            true_name : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['cart_step1_input_receiver'];?>'
            },
            address : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['cart_step1_input_address'];?>'
            },
            phone : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['cart_step1_telphoneormobile'];?>'
            }
        }
    });
	$('#submit').on('click',function(){
		if ($('#address_form').valid()) {
			$('#reciver_phone').val($('#new_phone').val());
			$('#reciver_address').val($('#new_address').val());
			$('#reciver_name').val($('#new_reciver_name').val());
			var content = $('#new_reciver_name').val() + '&nbsp;' + $('#new_phone').val() + '&nbsp;' + $('#new_address').val();
			$('#buyer_address_span').html(content);
			DialogManager.close('edit_buyer_address');
		}
	});
	$('#new_phone').val($('#reciver_phone').val());
	$('#new_address').val($('#reciver_address').val());
	$('#new_reciver_name').val($('#reciver_name').val());	
});
</script>