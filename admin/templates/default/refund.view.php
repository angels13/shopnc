<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['refund_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=refund&op=refund_manage"><span><?php echo '待处理';?></span></a></li>
        <li><a href="index.php?act=refund&op=refund_all"><span><?php echo '所有记录';?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_view'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo '退款单号'.$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['refund_sn']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo '商品名称'.$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['goods_name']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo $lang['refund_order_refund'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo ncPriceFormat($output['refund']['refund_amount']); ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo $lang['refund_buyer_message'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['buyer_message']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo '卖家审核'.$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['state_array'][$output['refund']['seller_state']];?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo $lang['refund_seller_message'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['seller_message']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <?php if ($output['refund']['seller_state'] == 2) { ?>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo '平台确认'.$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['admin_array'][$output['refund']['refund_state']];?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><?php echo $lang['refund_message'].$lang['nc_colon'];?></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['refund']['admin_message']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15" ><a href="JavaScript:void(0);" class="btn" onclick="history.go(-1)"><span><?php echo $lang['nc_back'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
</div>