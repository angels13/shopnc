<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form id="post_form" method="post" action="index.php?act=store_return&op=edit&return_id=<?php echo $output['return']['refund_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
  <dl>
    <dt><?php echo '商品名称'.$lang['nc_colon'];?></dt>
    <dd><?php echo $output['return']['goods_name']; ?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></dt>
    <dd><?php echo $lang['currency'];?><?php echo $output['return']['refund_amount']; ?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['return_order_return'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['return']['return_type']==2 ? $output['return']['goods_num']:'无'; ?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['return_buyer_message'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['return']['buyer_message']; ?></dd>
  </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['return_seller_confirm'].$lang['nc_colon'];?></dt>
      <dd>
        <label>
          <input type="radio" name="seller_state" value="2" />
          <?php echo $lang['nc_yes'];?> </label>
          <label><input name="return_type" type="checkbox" value="1" />
            弃货</label>
        <p class="hint">如果选择弃货，买家将不用退回原商品，提交后直接由管理员确认退款。</p>
        <label>
          <input type="radio" name="seller_state" value="3" />
          <?php echo $lang['nc_no'];?> </label>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['return_message'].$lang['nc_colon'];?></dt>
      <dd>
        <textarea name="seller_message" class="textarea w300"></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <p class="hint">
        	如是同意退货，请及时关注买家的发货情况，并进行收货（发货5天后可以选择未收到，超过7天不处理按弃货处理）。<br>
        	</p>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" id="confirm_button" name="confirm_button" value="<?php echo $lang['nc_ok'];?>" /></label>
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
         submitHandler: function(form) {
			    	ajaxpost('post_form', '', '', 'onerror');
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
                required  : '<i class="icon-exclamation-sign"></i><?php echo $lang['return_seller_confirm_null'];?>'
            },
            seller_message  : {
                required   : '<i class="icon-exclamation-sign"></i><?php echo $lang['return_message_null'];?>'
            }
        }
	    });
});
</script>
