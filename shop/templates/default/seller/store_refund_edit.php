<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form id="post_form" method="post" action="index.php?act=store_refund&op=edit&refund_id=<?php echo $output['refund']['refund_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo '商品名称'.$lang['nc_colon'];?></dt>
      <dd><?php echo $output['refund']['goods_name']; ?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></dt>
      <dd><?php echo $lang['currency'];?><?php echo ncPriceFormat($output['refund']['refund_amount']); ?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['refund']['buyer_message']; ?></dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['refund_seller_confirm'].$lang['nc_colon'];?></dt>
      <dd>
        <label>
          <input type="radio" name="seller_state" value="2" />
          <?php echo $lang['nc_yes'];?> </label>
        <label>
          <input type="radio" name="seller_state" value="3" />
          <?php echo $lang['nc_no'];?> </label>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['refund_message'].$lang['nc_colon'];?></dt>
      <dd>
        <textarea name="seller_message" rows="3" class="textarea w300"></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <p class="hint">只能提交一次，请认真选择。<br>同意并经过平台确认后会将金额以预存款的形式返还给买家。<br>不同意时买家可以向平台投诉或再次申请。</p>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" /></label>
    </div>
  </form>
</div>
<script type="text/javascript">
$(function(){
    $('#post_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
		submitHandler:function(form){
			ajaxpost('post_form', '', '', 'onerror')
		},
        rules : {
            seller_state : {
                required   : true
            },
            seller_message : {
                required   : true
            }
        },
        messages : {
            seller_state  : {
                required  : '<i class="icon-exclamation-sign"></i><?php echo $lang['refund_seller_confirm_null'];?>'
            },
            seller_message  : {
                required   : '<i class="icon-exclamation-sign"></i><?php echo $lang['refund_message_null'];?>'
            }
        }
    });
});
</script>
