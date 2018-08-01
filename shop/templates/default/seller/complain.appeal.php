<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
    <?php 
    include template('seller/complain_order.info');
    include template('seller/complain_complain.info');
    if($output['complain_info']['member_status'] === 'accused') {
        include template('seller/complain_appeal.submit');
    }
?>
  </div>
</div>
