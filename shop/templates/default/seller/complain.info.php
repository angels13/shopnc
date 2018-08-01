<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
    <?php 
    include template('seller/complain_order.info');
    include template('seller/complain_complain.info');
    if(!empty($output['complain_info']['appeal_message'])) {
        include template('seller/complain_appeal.info');
    }
    if(intval($output['complain_info']['complain_state'])>20) {
        include template('seller/complain_talk.info');
    }
    if(!empty($output['complain_info']['final_handle_message'])) {
        include template('seller/complain_finish.info');
    }
?>
  </div>
</div>