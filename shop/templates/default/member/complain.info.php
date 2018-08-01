<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
    <?php 
    include template('member/complain_order.info');
    include template('member/complain_complain.info');
    if(!empty($output['complain_info']['appeal_message'])) {
        include template('member/complain_appeal.info');
    }
    if(intval($output['complain_info']['complain_state'])>20) {
        include template('member/complain_talk.info');
    }
    if(!empty($output['complain_info']['final_handle_message'])) {
        include template('member/complain_finish.info');
    }
?>
  </div>
</div>