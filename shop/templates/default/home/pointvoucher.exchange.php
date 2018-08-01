<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>

</style>
<div class="voucherinfo">
  <?php if ($output['result'] === true){?>
  <form method="post" action="index.php?act=pointvoucher&op=voucherexchange_save" id="exform" onsubmit="ajaxpost('exform', '', '', 'onerror');">
    <input type="hidden" name="form_submit" value="ok"/>
    <input type="hidden" name="vid" value="<?php echo $output['voucher_info']['voucher_t_id']; ?>"/>
    <dl style=" margin-top:-10px;">
      <dt><?php echo $lang['home_voucher_storename'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['voucher_info']['store_name'];?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_price'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['voucher_info']['voucher_t_price'].$lang['currency_zh'];?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_indate'].$lang['nc_colon'];?></dt>
      <dd><?php echo @date('Y-m-d',$output['voucher_info']['voucher_t_end_date']);?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_points'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['voucher_info']['voucher_t_points'].$lang['points_unit'];?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_usecondition'].$lang['nc_colon'];?></dt>
      <dd><?php echo $lang['home_voucher_usecondition_desc1']; ?><?php echo $output['voucher_info']['voucher_t_limit'].$lang['currency_zh'];?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_remainnum'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['voucher_info']['voucher_t_total']-$output['voucher_info']['voucher_t_giveout'];?><?php echo $lang['home_voucher_unit'];?></dd>
    </dl>
    <dl>
      <dt><?php echo $lang['home_voucher_memberpoints'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['member_info']['member_points'].$lang['points_unit'];?></dd>
    </dl>
    <div class="enter">
      <input type="submit" class="submit" value="<?php echo $lang['home_voucher_exchangbtn'];?>"/>
    </div>
  </form>
  <?php }else {?>
  <div class="errormsg"><?php echo $output['message'];?></div>
  <?php }?>
</div>
