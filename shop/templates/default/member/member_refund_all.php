<div class="eject_con">
  <div id="warning"></div>
  <form id="post_form" method="post" action="index.php?act=member_refund&op=add_refund_all&order_id=<?php echo $output['order']['order_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['refund_order_amount'].$lang['nc_colon'];?></dt>
      <dd><?php echo ncPriceFormat($output['order']['order_amount']); ?></dd>
    </dl>
    <dl>
      <dt class="required"><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></dt>
      <dd>
        <?php echo ncPriceFormat($output['order']['order_amount']); ?>
      </dd>
    </dl>
    <dl>
      <dt class="required"><em class="pngFix"></em><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></dt>
      <dd>
        <textarea name="buyer_message" rows="3" class="textarea w250"><?php echo $output['refund']['buyer_message']; ?></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <p class="hint">建议与卖家沟通确认后提交申请。<br>卖家同意并经过平台确认后会将金额以预存款的形式返还给你。</p>
      </dd>
    </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
        <input type="submit" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" />
      </dd>
    </dl>
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
            buyer_message : {
                required   : true
            }
        },
        messages : {
            buyer_message  : {
                required   : '<?php echo $lang['refund_buyer_message_null'];?>'
            }
        }
    });
});
</script> 
