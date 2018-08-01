<script type="text/javascript">
$(document).ready(function(){
    var state = <?php echo empty($output['complain_info']['complain_state'])?0:$output['complain_info']['complain_state'];?>;
    if(state <= 10) {
        $("#state_new").addClass('red');
    }
    if(state == 20 ){
        $("#state_new").addClass('green');
        $("#state_appeal").addClass('red');
    }
    if(state == 30 ){
        $("#state_new").addClass('green');
        $("#state_appeal").addClass('green');
        $("#state_talk").addClass('red');
    }
    if(state == 40 ){
        $("#state_new").addClass('green');
        $("#state_appeal").addClass('green');
        $("#state_talk").addClass('green');
        $("#state_handle").addClass('red');
    }
    if(state == 99 ){
        $("#state_new").addClass('green');
        $("#state_appeal").addClass('green');
        $("#state_talk").addClass('green');
        $("#state_handle").addClass('green');
        $("#state_finish").addClass('green');
    }
});
</script>

<h3><?php echo $lang['complain_progress'];?></h3>
<ul class="progress">
  <li id="state_new" class="text"><?php echo $lang['complain_state_new'];?></li>
  <li class="next-step"></li>
  <li id="state_appeal" class="text"><?php echo $lang['complain_state_appeal'];?></li>
  <li class="next-step" ></li>
  <li id="state_talk" class="text"><?php echo $lang['complain_state_talk'];?></li>
  <li class="next-step"></li>
  <li id="state_handle" class="text"><?php echo $lang['complain_state_handle'];?></li>
  <li class="next-step"></li>
  <li id="state_finish" class="text"><?php echo $lang['complain_state_finish'];?></li>
</ul>
<div class="clear"></div>
<h3><?php echo $lang['order_detail'];?></h3>
<dl>
  <dt><?php echo $lang['order_buyer_name'].$lang['nc_colon'];?></dt>
  <dd><?php echo $output['order_info']['buyer_name'];?></dd>
  <dt><?php echo $lang['order_state'].$lang['nc_colon'];?></dt>
  <dd><strong><?php echo $output['order_info']['order_state_text'];?></strong></dd>
  <dt> <?php echo $lang['order_sn'].$lang['nc_colon'];?></dt>
  <dd>
    <a href="<?php echo SHOP_SITE_URL;?>/index.php?act=store_order&op=show_order&order_id=<?php echo $output['order_info']['order_id'];?>" target="_blank"> <?php echo $output['order_info']['order_sn'];?> </a>
  </dd>
  <dt><?php echo $lang['order_datetime'].$lang['nc_colon'];?></dt>
  <dd><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></dd>
  <dt><?php echo $lang['order_price'].$lang['nc_colon'];?></dt>
  <dd><?php echo $lang['currency'].$output['order_info']['order_amount'];?></dd>
  <?php if(!empty($output['order_info']['voucher_price'])) { ?>
  <dt><?php echo $lang['order_voucher_price'].$lang['nc_colon'];?></dt>
  <dd><?php echo $lang['currency'].$output['order_info']['voucher_price'].'.00';?></dd>
  <dt><?php echo $lang['order_voucher_sn'].$lang['nc_colon'];?></dt>
  <dd><?php echo $output['order_info']['voucher_code'];?></dd>
  <?php } ?>
</dl>
