<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="wrap-all ncu-order-view">
  <dl>
    <dt><?php echo $lang['store_order_state'].$lang['nc_colon'];?></dt>
    <dd><strong><?php echo $output['order_info']['state_desc']; ?></strong></dd>
    <dt><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?></dt>
    <dd> <?php echo $output['order_info']['order_sn']; ?> </dd>
    <dt><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?></dt>
    <dd><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></dd>
  </dl>
  <h3><?php echo $lang['store_show_order_info'];?></h3>
  <table class="ncsc-table-style">
    <thead>
      <tr>
        <th class="w10">&nbsp;</th>
        <th class="w70">&nbsp;</th>
        <th class="tl"><?php echo $lang['store_show_order_goods_name'];?></th>
        <th class="w100"><?php echo $lang['store_show_order_amount'];?></th>
        <th class="w100"><?php echo $lang['store_show_order_price'];?></th>
        <th class="w100">实际支付额</th>
        <th class="w100">佣金比例</th>
        <th class="w100">支付佣金</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($output['order_info']['extend_order_goods'] as $goods) { ?>
      <tr class="bd-line">
        <td></td>
        <td><div class="goods-pic-small"><span class="thumb size60"><i></i><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods['goods_id']));?>"><img src="<?php echo thumb($goods,60); ?>" /></a></span></div></td>
        <td class="tl"><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods['goods_id']));?>"><?php echo $goods['goods_name']; ?></a><br/><?php echo orderGoodsType($goods['goods_type']);?>
          </dl></td>
        <td><?php echo $goods['goods_num']; ?></td>
        <td><?php echo $lang['currency'];?><?php echo $goods['goods_price']; ?></td>
        <td><?php echo $lang['currency'];?><?php echo $goods['goods_pay_price']; ?></td>
        <td><?php echo $goods['commis_rate']; ?>%</td>
        <td><?php echo $lang['currency'];?><?php echo ncPriceFormat($goods['goods_pay_price']*$goods['commis_rate']/100); ?></td>
        </tr>
      <?php }?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="20" class="transportation tl">
     <?php if(!empty($output['order_info']['shipping_fee']) && $output['order_info']['shipping_fee'] != '0.00'){ ?>
        <?php echo $lang['store_show_order_tp_fee'];?>: <span><?php echo $lang['currency'];?><?php echo $output['order_info']['shipping_fee']; ?></span> 
     <?php }else{?>
     	<?php echo $lang['nc_common_shipping_free'];?>
     <?php }?>
     <?php echo $lang['store_order_sum'].$lang['nc_colon'];?><b><?php echo $lang['currency'];?><?php echo $output['order_info']['order_amount']; ?></b>
	<?php if($output['order_info']['refund_amount'] > 0) { ?>
	(<?php echo $lang['store_order_refund'];?>:<?php echo $lang['currency'].$output['order_info']['refund_amount'];?>)
	<?php } ?>
     </td>
      </tr>
    </tfoot>
  </table>
  <ul class="order_detail_list">
    <?php if($output['order_info']['payment_name']) { ?>
    <li><?php echo $lang['store_order_pay_method'].$lang['nc_colon'];?><?php echo $output['order_info']['payment_name']; ?></li>
    <?php } ?>
    <li><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></li>
    <?php if(intval($output['order_info']['payment_time'])) { ?>
    <li><?php echo $lang['store_show_order_pay_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['payment_time']); ?></li>
    <?php } ?>
    <?php if($output['order_info']['shipping_time']) { ?>
    <li><?php echo $lang['store_show_order_send_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['shipping_time']); ?></li>
    <?php } ?>
    <?php if(intval($output['order_info']['finnshed_time'])) { ?>
    <li><?php echo $lang['store_show_order_finish_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['finnshed_time']); ?></li>
    <?php } ?>
  </ul>

  <?php if(!empty($output['order_info']['extend_order_common']['promotion_info']) || !empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
  <!-- S 促销信息 -->
  <h3><?php echo $lang['nc_promotion'];?></h3>
  <div style="height:30px;line-height:30px;">
    <?php if(!empty($output['order_info']['extend_order_common']['promotion_info'])){ ?>
    <span style="color:red">满即送</span> <?php echo $output['order_info']['extend_order_common']['promotion_info'];?>
    <?php } ?>
    <?php if(!empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
    <span style="color:red">代金券</span> 面额 : <?php echo $output['order_info']['extend_order_common']['voucher_price'];?>
     编码 : <?php echo $output['order_info']['extend_order_common']['voucher_code'];?></a>
    <?php } ?>
  </div>
  <!-- E 促销信息 -->
  <?php } ?>

  <!-- 物流信息 -->
  <h3><?php echo $lang['store_show_order_shipping_info'];?></h3>
  <dl>
    <?php if (!empty($output['order_info']['shipping_code'])) { ?>
    <dt><?php echo $lang['store_order_shipping_no'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['order_info']['shipping_code']; ?></dd>
    <?php } ?>
    <?php if($output['order_info']['extend_order_common']['order_message']) { ?>
    <dt><?php echo $lang['store_show_order_buyer_message'].$lang['nc_colon'];?></dt>
    <dd><?php echo $output['order_info']['extend_order_common']['order_message']; ?>&nbsp;</dd>
    <?php } ?>
    <dt class="cb"><?php echo $lang['store_show_order_receiver'].$lang['nc_colon'];?></dt>
    <dd style="width:90%">
  <?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp;
  <?php echo @$output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;
  <?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?>        
    </dd>
    <div class="clear"></div>
  </dl>

<!-- 发票信息 -->
<h3>发票信息</h3>
<dl class="logistics">
<?php foreach ((array)$output['order_info']['extend_order_common']['invoice_info'] as $key => $value){?>
  <dt class = 'cb'><?php echo $key.$lang['nc_colon'];?></dt>
  <dd style="width:90%;"><?php echo $value;?></dd>    
<?php } ?>
</dl>

<!-- 订单操作日志 -->
  <?php if(is_array($output['order_log']) and !empty($output['order_log'])) { ?>
  <h3><?php echo $lang['store_show_order_handle_history'];?></h3>
  <ul class="log-list">
  <?php foreach($output['order_log'] as $val) { ?>
  <li> <?php echo $val['log_role'];?> <?php echo $val['log_user'];?>&emsp;<?php echo $lang['member_show_order_at'];?>&emsp;<?php echo date("Y-m-d H:i:s",$val['log_time']); ?>&emsp;<?php echo $val['log_msg'];?></li>
  <?php } ?>
  </ul>
  <?php } ?>

<!-- 退款记录 -->
  <?php if(is_array($output['refund_list']) and !empty($output['refund_list'])) { ?>
  <h3><?php echo $lang['store_order_refund'];?></h3>
  <ul class="log-list">
  <?php foreach($output['refund_list'] as $val) { ?>
  <li> 发生时间<?php echo $lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$val['admin_time']); ?>&emsp;&emsp;退款单号<?php echo $lang['nc_colon'];?><?php echo $val['refund_sn'];?>&emsp;&emsp;退款金额<?php echo $lang['nc_colon'];?><?php echo $lang['currency'];?><?php echo $val['refund_amount']; ?>&emsp;备注<?php echo $lang['nc_colon'];?><?php echo $val['goods_name'];?></li>
  <?php } ?>
  </ul>
  <?php } ?>

<!-- 退货记录 -->
  <?php if(is_array($output['return_list']) and !empty($output['return_list'])) { ?>
  <h3><?php echo $lang['store_order_return'];?></h3>
  <ul class="log-list">
  <?php foreach($output['return_list'] as $val) { ?>
  <li> 发生时间<?php echo $lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$val['admin_time']); ?>&emsp;&emsp;退款单号<?php echo $lang['nc_colon'];?><?php echo $val['refund_sn'];?>&emsp;&emsp;退款金额<?php echo $lang['nc_colon'];?><?php echo $lang['currency'];?><?php echo $val['refund_amount']; ?>&emsp;备注<?php echo $lang['nc_colon'];?><?php echo $val['goods_name'];?></li>
  <?php } ?>
  </ul>
<?php } ?>
  </div>