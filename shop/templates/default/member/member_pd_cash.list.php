<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
    <a class="ncu-btn3" href="index.php?act=predeposit&op=pd_cash_add">申请提现</a>
  </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name="act" value="predeposit" />
      <input type="hidden" name="op" value="pd_cash_list" />
      <tr>
      <th></th><td></td>
        <th><?php echo $lang['predeposit_paystate'].$lang['nc_colon']; ?></th>
        <td class="w90 tr"><select id="paystate_search" name="paystate_search">
            <option value="0"><?php echo $lang['nc_please_choose'];?></option>
            <option <?php if ($_GET['paystate_search'] == '0') echo 'selected';?> value="0">未支付</option>
            <option <?php if ($_GET['paystate_search'] == '1') echo 'selected';?> value="1">已支付</option>
          </select>
       </td>
        <th><?php echo $lang['predeposit_cashsn'].$lang['nc_colon'];?></th>
        <td class="w200 tc"><input type="text" class="text" name="sn_search" value="<?php echo $_GET['sn_search'];?>"/></td>
        <td class="w90 tc"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></td>
      </tr>
    </table>
  </form>
  <table class="ncu-table-style">
    <thead>
      <tr>
        <th><?php echo $lang['predeposit_cashsn']; ?></th>
        <th><?php echo $lang['predeposit_apptime']; ?></th>
        <th><?php echo $lang['predeposit_cash_price']; ?>(<?php echo $lang['currency_zh']; ?>)</th>
        <th class="w150"><?php echo $lang['predeposit_paystate']; ?></th>
        <th class="w100"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php  if (count($output['list'])>0) { ?>
      <?php foreach($output['list'] as $val) { ?>
      <tr class="bd-line">
        <td class="goods-num"><?php echo $val['pdc_sn'];?></td>
        <td class="goods-time"><?php echo @date('Y-m-d H:i:s',$val['pdc_add_time']);?></td>
        <td class="goods-price"><?php echo $val['pdc_amount'];?></td>
        <td><?php echo str_replace(array('0','1'),array('未支付','已支付'),$val['pdc_payment_state']);?></td>
        <td><p><a href="index.php?act=predeposit&op=pd_cash_info&id=<?php echo $val['pdc_id']; ?>"><?php echo $lang['nc_view']; ?></a></p></td>
      </tr>
      <?php } ?>
      <?php } else {?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php  if (count($output['cash_list'])>0) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
