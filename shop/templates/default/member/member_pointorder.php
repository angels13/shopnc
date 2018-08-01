<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <table class="ncu-table-style order">
    <thead>
      <tr>
        <th class="w10"></th>
        <th class="w70"></th>
        <th class="tl"><?php echo $lang['member_pointorder_info_prodinfo'];?></th>
        <th class="w90"><?php echo $lang['member_pointorder_exchangepoints'];?></th>
        <th class="w90"><?php echo $lang['member_pointorder_exnum'];?></th>
        <th class="w110"><?php echo $lang['member_pointorder_exchangepoints_shippingfee'];?></th>
        <th class="w110"><?php echo $lang['member_pointorder_orderstate_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(count($output['order_list'])>0){ ?>
      <?php foreach ($output['order_list'] as $val) { ?>
      <tr>
        <td colspan="19" class="sep-row"></td>
      </tr>
      <tr>
        <th colspan="20"><span class="fl ml10"><?php echo $lang['member_pointorder_ordersn'].$lang['nc_colon'];?><em class="goods-num"><?php echo $val['point_ordersn']; ?></em></span><span class="fl ml20"><?php echo $lang['member_pointorder_addtime'].$lang['nc_colon'];?><em class="goods-time"><?php echo @date("Y-m-d H:i:s",$val['point_addtime']); ?></em></span><span class="fl ml20"><i></i><a href="index.php?act=member_pointorder&op=order_info&order_id=<?php echo $val['point_orderid']; ?>" target="_blank" class="nc-show-order" ><?php echo $lang['member_pointorder_viewinfo'];?></a></span></th>
      </tr>
      <?php foreach($val['prodlist'] as $k=>$v) {?>
      <tr>
        <td class="bdl"></td>
        <td><div class="goods-pic-small"> <span class="thumb size60"> <i></i><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['point_goodsid']));?>" target="_blank"> <img src="<?php echo UPLOAD_SITE_URL.DS.$v['point_goodsimage']; ?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(60);?>'" onload="javascript:DrawImage(this,60,60);"/></a></span></div></td>
        <td class="tl"><dl class="goods-name">
            <dt><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $v['point_goodsid']));?>" target="_blank"><?php echo $v['point_goodsname']; ?></a></dt>
          </dl></td>
        <td><?php echo $v['point_goodspoints']; ?></td>
        <td><?php echo $v['point_goodsnum']; ?></td>
        <?php if ((count($val['prodlist']) > 1 && $k ==0) || (count($val['prodlist']) == 1)){?>
        <td class="bdl" rowspan="<?php echo count($val['prodlist']);?>">
        <p class="price"><strong><?php echo $val['point_allpoint']; ?></strong></p>
          </td>
        <td class="bdl bdr" rowspan="<?php echo count($val['prodlist']);?>"><p><?php echo $val['point_orderstatetext']['order_state']; ?><?php echo $val['point_orderstatetext']['change_state'] == ''?'':','.$val['point_orderstatetext']['change_state']; ?></p>
          
          <?php if ($val['point_shippingcharge'] == 1 && $val['point_orderstate'] == 11) { ?>
          <p><?php echo $val['point_paymentname']; ?></p>
          <?php } ?>
          <?php if ($val['point_orderstate'] == 30) { ?>
          <p><a href="javascript:void(0)" onclick="drop_confirm('<?php echo $lang['member_pointorder_confirmreceivingtip']; ?>','index.php?act=member_pointorder&op=receiving_order&order_id=<?php echo $val['point_orderid']; ?>');" class="ncu-btn7 mt5" ><?php echo $lang['member_pointorder_confirmreceiving']; ?></a></p>
          <?php } ?>
          <?php if (($val['point_shippingcharge'] == 1 && $val['point_orderstate'] == 10) || ($val['point_shippingcharge'] != 1 && $val['point_orderstate'] == 20)) { ?>
          <p><a href="javascript:void(0)" onclick="drop_confirm('<?php echo $lang['member_pointorder_cancel_confirmtip']; ?>','index.php?act=member_pointorder&op=cancel_order&order_id=<?php echo $val['point_orderid']; ?>');" style="color:#F30; text-decoration:underline;"><?php echo $lang['member_pointorder_cancel_title']; ?></a></p>
          <?php } ?><?php if ($val['point_shippingcharge'] == 1 && $val['point_orderstate'] == 10) { ?>
          <p><a href="index.php?act=pointcart&op=step3&order_id=<?php echo $val['point_orderid']; ?>" class="ncu-btn6 mt5" ><?php echo $lang['member_pointorder_pay']; ?></a> </p>
          <?php } ?></td>
        <?php } ?>
      </tr>
      <?php } ?>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if(count($output['order_list'])>0){ ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
