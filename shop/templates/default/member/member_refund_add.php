<div class="eject_con">
  <div id="warning"></div>
  <form id="post_form" method="post" action="index.php?act=member_refund&op=add_refund&order_id=<?php echo $output['order']['order_id']; ?>&goods_id=<?php echo $output['goods']['rec_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['refund_order_amount'].$lang['nc_colon'];?></dt>
      <dd><?php echo $lang['currency'];?><?php echo ncPriceFormat($output['order']['order_amount']); ?>
        <?php if ($output['order']['refund_amount'] > 0) { ?>
        (<?php echo $lang['refund_add'].$lang['nc_colon'].$lang['currency'].$output['order']['refund_amount'];?>)
        <?php } ?>
        </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo '商品名称'.$lang['nc_colon'];?></dt>
      <dd>
        <?php echo $output['goods']['goods_name']; ?>
      </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo '商品单价'.$lang['nc_colon'];?></dt>
      <dd>
        <?php echo $lang['currency'];?><?php echo $output['goods']['goods_price']; ?>
      </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo '购买数量'.$lang['nc_colon'];?></dt>
      <dd>
        <?php echo $output['goods']['goods_num']; ?>
      </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo $lang['refund_pay_refund'].$lang['nc_colon'];?></dt>
      <dd>
        <?php echo $output['goods']['goods_pay_price']; ?>
        <p class="hint"><?php echo '可退金额由系统根据订单商品实际成交额和已退款金额自动计算得出。';?></p>
      </dd>
    </dl>
    <dl>
      <dt class="required"><em class="pngFix"></em><?php echo '申请类型'.$lang['nc_colon'];?></dt>
      <dd>
        <label>
          <input type="radio" name="refund_type" value="1" />
          <?php echo '退款(不退货)';?> </label>
        <label>
          <input type="radio" name="refund_type" id="confirm_refund_type" value="2" />
          <?php echo '退货并退款';?> </label>
      </dd>
    </dl>
    <dl>
      <dt class="required"><em class="pngFix"></em><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></dt>
      <dd>
        <input type="text" class="text w50" name="refund_amount" value="<?php echo $output['goods']['goods_pay_price']; ?>" />
        <p class="hint"><?php echo '退款金额不能超过可退金额。';?></p>
      </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo $lang['return_order_return'].$lang['nc_colon'];?></dt>
      <dd>
        <input type="text" class="text w50" name="goods_num" value="" />
      </dd>
    </dl>
    <dl>
      <dt class="required"><em class="pngFix"></em><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></dt>
      <dd>
        <textarea name="buyer_message" rows="3" class="textarea w250"></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <p class="hint"><?php echo $lang['refund_buyer_desc'];?></p>
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
            refund_type : {
                required   : true
            },
            refund_amount : {
                required   : true,
                number   : true,
                min:0.01,
                max:<?php echo $output['goods']['goods_pay_price']; ?>
            },
            goods_num : {
                required   : '#confirm_refund_type:checked',
                digits   : true,
                min:1,
                max:<?php echo $output['goods']['goods_num']; ?>
            },
            buyer_message : {
                required   : true
            }
        },
        messages : {
            refund_type  : {
                required  : '<?php echo '请选择申请类型';?>'
            },
            refund_amount  : {
                required  : '<?php echo $lang['refund_pay_refund'];?> <?php echo $output['goods']['goods_pay_price']; ?>',
                number   : '<?php echo $lang['refund_pay_refund'];?> <?php echo $output['goods']['goods_pay_price']; ?>',
                min   : '<?php echo $lang['refund_pay_min'];?> 0.01',
	            max   : '<?php echo $lang['refund_pay_refund'];?> <?php echo $output['goods']['goods_pay_price']; ?>'
            },
            goods_num  : {
                required  : '<?php echo $lang['return_add_return'];?> <?php echo $output['goods']['goods_num']; ?>',
                digits   : '<?php echo $lang['return_add_return'];?> <?php echo $output['goods']['goods_num']; ?>',
                min   : '<?php echo $lang['return_number_min'];?> 1',
	            max   : '<?php echo $lang['return_number_max'];?> <?php echo $output['goods']['goods_num']; ?>'
            },
            buyer_message  : {
                required   : '<?php echo $lang['refund_buyer_message_null'];?>'
            }
        }
    });
});
</script>
