<div class="eject_con">
  <div id="warning"></div>
<?php if ($output['order_info']) {?>
  <form method="post" action="index.php?act=member_order&op=change_state&state_type=order_cancel&order_id=<?php echo $output['order_info']['order_id']; ?>" id="order_cancel_form" onsubmit="ajaxpost('order_cancel_form','','','onerror')">
    <input type="hidden" name="form_submit" value="ok" />
    <h2><?php echo $lang['member_change_ensure_cancel'];?>?</h2>
    <dl>
      <dt><?php echo $lang['member_order_sn'].$lang['nc_colon'];?></dt>
      <dd><span class="num"><?php echo $output['order_info']['order_sn']; ?></span></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['member_change_cancel_reason'].$lang['nc_colon'];?></dt>
      <dd>
        <ul class="checked">
          <li>
            <input type="radio" checked name="state_info" id="d1" value="<?php echo $lang['member_change_other_goods'];?>" />
            <label for="d1"><?php echo $lang['member_change_other_goods'];?></label>
          </li>
          <li>
            <input type="radio" name="state_info" id="d2" value="<?php echo $lang['member_change_other_shipping'];?>" />
            <label for="d2"><?php echo $lang['member_change_other_shipping'];?></label>
          </li>
          <li>
            <input type="radio" name="state_info" id="d3" value="<?php echo $lang['member_change_other_store'];?>" />
            <label for="d3"><?php echo $lang['member_change_other_store'];?></label>
          </li>
          <li>
            <input type="radio" name="state_info" flag="other_reason" id="d4" value="" />
            <label for="d4"><?php echo $lang['member_change_other_reason'];?></label>
          </li>
          <li id="other_reason" style="display:none;">
            <textarea name="state_info1" rows="2" id="other_reason_input"></textarea>
          </li>
        </ul>
      </dd>
    </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <input type="submit" id="confirm_button" class="submit" value="<?php echo $lang['nc_ok'];?>" />
    </dl>
  </form>
<?php } else { ?>
<p style="line-height:80px;text-align:center">该订单并不存在，请检查参数是否正确!</p>
<?php } ?>
</div>
<script type="text/javascript">
$(function(){
        $('#cancel_button').click(function(){
            DialogManager.close('seller_order_cancel_order');
         });
       $("input[name='state_info']").click(function(){
        if ($(this).attr('flag') == 'other_reason')
        {
            $('#other_reason').show();
        }
        else
        {
            $('#other_reason').hide();
        }
    });
});
</script>