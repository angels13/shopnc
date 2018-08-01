<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name="act" value="member_return" />
        <input type="hidden" name="op" value="index" />
      <tr>
        <td>&nbsp;</td>
        <th style="width:115px"><select name="type">
            <option value="order_sn" <?php if($_GET['type'] == 'order_sn'){?>selected<?php }?>><?php echo $lang['return_order_ordersn']; ?></option>
            <option value="return_sn" <?php if($_GET['type'] == 'return_sn'){?>selected<?php }?>><?php echo $lang['return_order_returnsn']; ?></option>
            <option value="goods_name" <?php if($_GET['type'] == 'goods_name'){?>selected<?php }?>><?php echo '商品名称'; ?></option>
          </select>
          <?php echo $lang['nc_colon'];?></th>
        <td class="w160"><input type="text" class="text" name="key" value="<?php echo trim($_GET['key']); ?>" /></td>
        <th><?php echo $lang['return_order_add_time'].$lang['nc_colon'];?></th>
        <td class="w180"><input type="text" class="text" name="add_time_from" id="add_time_from" value="<?php echo $_GET['add_time_from']; ?>" />
          &#8211;
          <input id="add_time_to" type="text" class="text"  name="add_time_to" value="<?php echo $_GET['add_time_to']; ?>" /></td>
        <td class="w90 tc"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></td>
      </tr>
    </table>
  </form>
  <table class="ncu-table-style">
    <thead>
      <tr>
        <th class="w10"></th>
        <th colspan="2">商品/订单号/退货号</th>
        <th class="w80"><?php echo $lang['refund_order_refund'];?></th>
        <th class="w80"><?php echo $lang['return_order_return'];?></th>
        <th class="w150"><?php echo $lang['return_order_add_time'];?></th>
        <th class="w60"><?php echo $lang['return_state'];?></th>
        <th class="w60">平台确认</th>
        <th class="w100"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <?php if (is_array($output['return_list']) && !empty($output['return_list'])) { ?>
    <tbody>
      <?php foreach ($output['return_list'] as $key => $val) { ?>
      <tr class="bd-line" >
        <td></td>
        <td><div class="goods-pic-small"><span class="thumb size60"><i></i><a href="<?php echo urlShop('goods','index',array('goods_id'=> $val['goods_id']));?>" target="_blank"><img src="<?php echo thumb($val,60);?>"/></a></span></div></td>
        <td title="<?php echo $val['store_name']; ?>">
		<dl class="goods-name"><dt><a href="<?php echo urlShop('goods','index',array('goods_id'=> $val['goods_id']));?>" target="_blank"><?php echo $val['goods_name']; ?></a></dt>
        <dd><?php echo $lang['refund_order_ordersn'].$lang['nc_colon'];?><a href="index.php?act=member_order&op=show_order&order_id=<?php echo $val['order_id']; ?>" target="_blank"><?php echo $val['order_sn'];?></a></dd>
        <dd><?php echo $lang['return_order_returnsn'].$lang['nc_colon'];?><?php echo $val['refund_sn']; ?></dd></dl></td>
        <td><?php echo $lang['currency'];?><?php echo $val['refund_amount'];?></td>
        <td><?php echo $val['return_type'] == 2 ? $val['goods_num']:'无';?></td>
        <td class="goods-time"><?php echo date("Y-m-d H:i:s",$val['add_time']);?></td>
        <td><?php echo $output['state_array'][$val['seller_state']]; ?></td>
        <td><?php echo ($val['seller_state'] == 2 && $val['refund_state'] >= 2) ? $output['admin_array'][$val['refund_state']]:'无'; ?></td>
        <td><a href="javascript:void(0)" nc_type="dialog" dialog_title="<?php echo $lang['nc_view'];?>" dialog_id="return_order" dialog_width="480"
            uri="index.php?act=member_return&op=view&return_id=<?php echo $val['refund_id']; ?>"> <?php echo $lang['nc_view'];?> </a>
        	<?php if($val['seller_state'] == 2 && $val['return_type'] == 2 && $val['goods_state'] == 1) { ?>
        	<p><a href="javascript:void(0)" class="ncu-btn7 mt5" nc_type="dialog" dialog_title="发货" dialog_id="return_ship" dialog_width="480"
        	    uri="index.php?act=member_return&op=ship&return_id=<?php echo $val['refund_id']; ?>"> 发货 </a></p>
        	<?php } ?>
        	<?php if($val['seller_state'] == 2 && $val['return_type'] == 2 && $val['goods_state'] == 3) { ?>
        	<p><a href="javascript:void(0)" class="ncu-btn7 mt5" nc_type="dialog" dialog_title="延迟时间" dialog_id="return_delay" dialog_width="480"
        	    uri="index.php?act=member_return&op=delay&return_id=<?php echo $val['refund_id']; ?>"> 延迟时间 </a></p>
        	<?php } ?>
        </td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if (is_array($output['return_list']) && !empty($output['return_list'])) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<script type="text/javascript">
	$(function(){
	    $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
	    $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
	});
</script>
