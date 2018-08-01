<div class="eject_con">
  <div id="warning"></div>
  <dl>
    <dt><?php echo $lang['return_order_returnsn'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['return']['refund_sn']; ?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['return_order_add_time'].$lang['nc_colon'];?></dt>
    <dd class="goods-time"><?php echo date("Y-m-d H:i:s",$output['return']['add_time']);?> </dd>
  </dl>
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
    <dt><?php echo $lang['return_state'].$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['state_array'][$output['return']['seller_state']]; ?> </dd>
  </dl>
  <?php if ($output['return']['seller_time'] > 0) { ?>
  <dl>
    <dt><?php echo $lang['return_seller_message'].$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['return']['seller_message']; ?> </dd>
  </dl>
  <?php } ?>
  <?php if ($output['return']['express_id'] > 0 && !empty($output['return']['invoice_no'])) { ?>
  <dl>
    <dt><?php echo '物流信息'.$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['e_name'].' , '.$output['return']['invoice_no']; ?> </dd>
  </dl>
  <?php } ?>
  <?php if ($output['return']['seller_state'] == 2 && $output['return']['refund_state'] >= 2) { ?>
  <dl>
    <dt><?php echo '平台确认'.$lang['nc_colon'];?></dt>
    <dd><?php echo $output['admin_array'][$output['return']['refund_state']]; ?></dd>
  </dl>
  <?php } ?>
  <?php if ($output['return']['admin_time'] > 0) { ?>
  <dl>
    <dt><?php echo '平台备注'.$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['return']['admin_message']; ?> </dd>
  </dl>
  <?php } ?>
</div>
