<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
     <div class="text-intro"><?php echo $lang['predeposit_pricetype_available'].$lang['nc_colon']; ?><?php echo $output['member_info']['available_predeposit']; ?>&nbsp;<?php echo $lang['currency_zh'];?> <?php echo $lang['predeposit_pricetype_freeze'].$lang['nc_colon']; ?><?php echo $output['member_info']['freeze_predeposit']; ?>&nbsp;<?php echo $lang['currency_zh'];?></div></div>
  <table class="ncu-table-style">
    <thead>
      <tr>
      	<th class="w10"></th>
      	<th class="w150 tl"><?php echo $lang['predeposit_addtime']; ?></th>
        <th class="w120 tl"><?php echo $lang['predeposit_pricetype_available'];?>(<?php echo $lang['currency_zh'];?>)</th>
        <th class="w120 tl"><?php echo $lang['predeposit_pricetype_freeze'];?>(<?php echo $lang['currency_zh'];?>)</th>
        <th class="tl"><?php echo $lang['predeposit_log_desc'];?></th>
      </tr>
    </thead>

    <tbody><?php  if (count($output['list'])>0) { ?>
      <?php foreach($output['list'] as $v) { ?>
      <tr class="bd-line">
      	<td></td>
        <td class="goods-time tl"><?php echo @date('Y-m-d H:i:s',$v['lg_add_time']);?></td>
        <td class="tl"><?php echo floatval($v['lg_av_amount']) ? (floatval($v['lg_av_amount']) > 0 ? '+' : null ).$v['lg_av_amount'] : null;?></td>
        <td class="tl"><?php echo floatval($v['lg_freeze_amount']) ? (floatval($v['lg_freeze_amount']) > 0 ? '+' : null ).$v['lg_freeze_amount'] : null;?></td>
        <td class="tl"><?php echo $v['lg_desc'];?></td>
      </tr>
      <?php } ?>
      <?php } else {?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php  if (count($output['list'])>0) { ?>
      <tr>
        <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>