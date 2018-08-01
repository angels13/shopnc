<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
    <a class="ncu-btn3" href="index.php?act=predeposit&op=recharge_add">马上充值</a>
  </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name="act" value="predeposit" />
      <input type="hidden" name="op" value="index" />
      <tr>
        <th><?php echo $lang['predeposit_pricetype_available'].$lang['nc_colon']; ?></th>
        <td><?php echo $output['member_info']['available_predeposit']; ?> <?php echo $lang['currency_zh'];?><td>
        <th><?php echo $lang['predeposit_rechargesn'].$lang['nc_colon'];?></th>
        <td class="w200 tc"><input type="text" class="text" name="pdr_sn" value="<?php echo $_GET['"pdr_sn"'];?>"/></td>
        <td class="w90 tc"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></td>
      </tr>
    </table>
  </form>
  <table class="ncu-table-style">
    <thead>
      <tr>
        <th><?php echo $lang['predeposit_rechargesn']; ?></th>
        <th class="w150"><?php echo $lang['predeposit_addtime']; ?></th>
        <th class="w100"><?php echo $lang['predeposit_payment']; ?></th>
        <th class="w150"><?php echo $lang['predeposit_recharge_price']; ?>(<?php echo $lang['currency_zh'];?>)</th>
        <th class="w100"><?php echo $lang['predeposit_paystate']; ?></th>
        <th class="w100"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($output['list'])>0) { ?>
      <?php foreach($output['list'] as $val) { ?>
      <tr class="bd-line">
        <td class="goods-num"><?php echo $val['pdr_sn'];?></td>
        <td class="goods-time"><?php echo date('Y-m-d H:i:s',$val['pdr_add_time']);?></td>
        <td><?php echo $val['pdr_payment_name'];?></td>
        <td class="goods-price"><?php echo $val['pdr_amount'];?></td>
        <td><?php echo intval($val['pdr_payment_state']) ? '已支付' : '未支付';?></td>
        <td>
		<?php if (!intval($val['pdr_payment_state'])){?>
          <p><a class="ncu-btn2 mt5" href="index.php?act=buy&op=pd_pay&pay_sn=<?php echo $val['pdr_sn'];?>">支付</a></p>
          <p><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', 'index.php?act=predeposit&op=recharge_del&id=<?php echo $val['pdr_id']; ?>');" class="ncu-btn2 mt5"><?php echo $lang['nc_del_&nbsp'];?></a></p>
          <?php }else{?>
          <p><a href="index.php?act=predeposit&op=recharge_show&id=<?php echo $val['pdr_id']; ?>" class="ncu-btn2 mt5"><?php echo $lang['nc_view'];?></a></p>
          <?php }?>
          </td>
      </tr>
      <?php } ?>
      <?php } else {?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if (count($output['list'])>0) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div>
        </td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>