<div class="eject_con">
  <div id="warning"></div>
  <dl>
    <dt><?php echo $lang['refund_order_refundsn'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['refund']['refund_sn'];?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['refund_order_add_time'].$lang['nc_colon'];?></dt>
    <dd><?php echo date("Y-m-d H:i:s",$output['refund']['add_time']);?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['refund_order_buyer'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['refund']['buyer_name']; ?></dd>
  </dl>
  <dl>
    <dt><?php echo '商品名称'.$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['refund']['goods_name']; ?> </dd>
  </dl>
  <dl>
    <dt><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></dt>
    <dd class="goods-price"><?php echo $lang['currency'];?><?php echo $output['refund']['refund_amount']; ?></dd>
  </dl>
  <dl>
    <dt><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['refund']['buyer_message']; ?> </dd>
  </dl>
  <dl>
    <dt><?php echo '处理状态'.$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['state_array'][$output['refund']['seller_state']]; ?> </dd>
  </dl>
  <?php if ($output['refund']['seller_time'] > 0) { ?>
  <dl>
    <dt><?php echo $lang['refund_seller_message'].$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['refund']['seller_message']; ?> </dd>
  </dl>
  <?php } ?>
  <?php if ($output['refund']['seller_state'] == 2) { ?>
  <dl>
    <dt><?php echo '平台确认'.$lang['nc_colon'];?></dt>
    <dd><?php echo $output['admin_array'][$output['refund']['refund_state']]; ?></dd>
  </dl>
  <?php } ?>
  <?php if ($output['refund']['admin_time'] > 0) { ?>
  <dl>
    <dt><?php echo '平台备注'.$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['refund']['admin_message']; ?> </dd>
  </dl>
  <?php } ?>
</div>
