<div class="eject_con">
  <div id="warning"></div>
  <form id="post_form" method="post" action="index.php?act=member_return&op=delay&return_id=<?php echo $output['return']['refund_id']; ?>" onsubmit="ajaxpost('post_form','','','onerror')">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        卖家选择没收到已经发货的商品，请联系物流进行确认，提交后将重新计时，卖家可以再次确认收货。
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
