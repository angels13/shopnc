<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
    <h2><?php echo $lang['member_show_order_desc'];?></h2>
    <dl class="box">
      <dt><?php echo $lang['member_change_order_no'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['order_sn']; ?> </dd>
      <dt><?php echo $lang['member_order_time'].$lang['nc_colon'];?></dt>
      <dd><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></dd>
    </dl>
  <div class="wrap-all ncu-order-view">
    <h3><?php echo $lang['member_show_order_seller_info'];?></h3>
    <dl>
      <dt><?php echo $lang['member_evaluation_store_name'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_store']['store_name']; ?></dd>
      <dt><?php echo $lang['member_address_phone_num'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_store']['store_tel']; ?></dd>
      <dt><?php echo $lang['member_address_location'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_store']['area_info'].'&nbsp;'.$output['order_info']['extend_store']['store_address']; ?></dd>
      <dt>QQ<?php echo $lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_store']['store_qq']; ?></dd>
      <dt><?php echo $lang['member_show_order_wangwang'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_store']['store_ww']; ?></dd>
    </dl><div class="clear"></div>
    <!--订单信息-->
    <h3><?php echo $lang['member_show_order_info'];?></h3>
    <table class="ncu-table-style">
      <thead>
        <tr>
          <th class="w10"></th>
          <th class="w70"></th>
          <th><?php echo $lang['member_order_goods_name'];?></th>
          <th><?php echo $lang['member_order_price'];?></th>
          <th><?php echo $lang['member_order_amount'];?></th>
          <th><?php echo $lang['member_order_goods_price'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($output['order_info']['extend_order_goods'] as $goods) {?>
        <tr class="bd-line">
          <td></td>
          <td><div class="goods-pic-small"><span class="thumb size60"><i></i><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods['goods_id'])); ?>"><img src="<?php echo thumb($goods,60); ?>" /></a></span></div></td>
          <td><dl class="goods-name">
              <dt><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods['goods_id'])); ?>"><?php echo $goods['goods_name']; ?></a></dt>
              <dd><?php echo orderGoodsType($goods['goods_type']); ?></dd>
            </dl></td>
          <td><?php echo $goods['goods_price']; ?></td>
          <td><?php echo $goods['goods_num']; ?></td>
          <td><?php echo sprintf('%.2f',$goods['goods_num']*$goods['goods_price']); ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">
            &emsp;<?php echo $lang['member_order_sum'].$lang['nc_colon'];?><b><?php echo $lang['currency'];?><?php echo $output['order_info']['order_amount']; ?></b>
	<?php if($output['order_info']['refund_amount'] > 0) { ?>
	(<?php echo $lang['member_order_refund'];?>:<?php echo $lang['currency'].$output['order_info']['refund_amount'];?>)
	<?php } ?>
          <?php if(!empty($output['order_info']['shipping_fee']) && $output['order_info']['shipping_fee'] != '0.00'){ ?>
          <?php echo $lang['member_show_order_tp_fee'].$lang['nc_colon'];?><span><?php echo $lang['currency'];?><?php echo $output['order_info']['shipping_fee']; ?> <?php if ($output['order_info']['shipping_name'] != ''){echo '('.$output['order_info']['shipping_name'].')';};?></span>
          <?php }else{?>
          	<?php echo $lang['nc_common_shipping_free'];?>
          <?php }?>
           </td>
        </tr>
      </tfoot>
    </table>
    <ul class="order_detail_list">
      <?php if($output['order_info']['payment_name']) { ?>
      <li><?php echo $lang['member_order_pay_method'].$lang['nc_colon'];?><?php echo $output['order_info']['payment_name']; ?></li>
      <?php } ?>
      <li><?php echo $lang['member_order_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></li>
      <?php if(intval($output['order_info']['payment_time'])) { ?>
      <li><?php echo $lang['member_show_order_pay_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['payment_time']); ?></li>
      <?php } ?>
      <?php if($output['order_info']['shipping_time']) { ?>
      <li><?php echo $lang['member_show_order_send_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['shipping_time']); ?></li>
      <?php } ?>
      <?php if(intval($output['order_info']['finnshed_time'])) { ?>
      <li><?php echo $lang['member_show_order_finish_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['finnshed_time']); ?></li>
      <?php } ?>
      <?php if($output['order_info']['payment_code'] != 'offline' && !in_array($output['order_info']['order_state'],array(ORDER_STATE_CANCEL,ORDER_STATE_NEW))) { ?>
      <li><?php echo '付款单号'.$lang['nc_colon'];?><?php echo $output['order_info']['pay_sn']; ?></li>
      <?php } ?>
    </ul>

    <!-- S 促销信息 -->
    <?php if(!empty($output['order_info']['extend_order_common']['promotion_info']) || !empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
    <h3><?php echo $lang['nc_promotion'];?></h3>
    <div style="height:30px;line-height:30px;">
        <?php if(!empty($output['order_info']['extend_order_common']['promotion_info'])){ ?>
        <span style="color:red">满即送</span> <?php echo $output['order_info']['extend_order_common']['promotion_info'];?></a>
        <?php } ?>
        <?php if(!empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
        <span style="color:red">代金券</span> 面额 <?php echo $lang['nc_colon'];?> <?php echo $output['order_info']['extend_order_common']['voucher_price'];?>
         编码 : <?php echo $output['order_info']['extend_order_common']['voucher_code'];?></a>
        <?php } ?>
    </div>
    <?php } ?>
    <!-- E 促销信息 -->

    <!-- 物流信息 -->
    <h3><?php echo $lang['member_show_order_shipping_info'];?></h3>
    <dl class="logistics">
      <?php if (!empty($output['order_info']['shipping_code'])) { ?>
      <dt><?php echo $lang['member_show_order_shipping_no'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['shipping_code'];?></dd>
      <?php } ?>
      <?php if($output['order_info']['extend_order_common']['order_message']) { ?>
      <dt><?php echo $lang['member_show_order_buyer_message'].$lang['nc_colon'];?></dt>
      <dd><?php echo $output['order_info']['extend_order_common']['order_message']; ?></dd>
      <?php } ?>
      <dt class="cb"><?php echo $lang['member_show_order_receiver'].$lang['nc_colon'];?></dt>
      <dd style="width:90%;">
      <?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp;
      <?php echo @$output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;
      <?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?>
      </dd>
      <?php if(!empty($output['daddress_info'])) { ?>
      <dt>发货人<?php echo $lang['nc_colon'];?></dt>
      <dd style="width:90%"><?php echo $output['daddress_info']['seller_name']; ?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>&nbsp;<?php echo $output['daddress_info']['company'];?></dd>
      <?php } ?>
    </dl>

    <!-- 发票信息 -->
    <h3>发票信息</h3>
    <dl class="logistics">
    <?php foreach ((array)$output['order_info']['extend_order_common']['invoice_info'] as $key => $value){?>
      <dt class = 'cb'><?php echo $key.$lang['nc_colon'];?></dt>
      <dd style="width:90%;"><?php echo $value;?></dd>
    <?php } ?>
    </dl>

    <!-- 操作历史 -->
    <?php if(is_array($output['order_log'])) { ?>
    <h3><?php echo $lang['member_show_order_handle_history'];?></h3>
    <ul class="log-list">
      <?php foreach($output['order_log'] as $val) { ?>
      <li> <?php echo $val['log_role'];?>&emsp;<?php echo $lang['member_show_order_at'];?>&emsp;<?php echo date("Y-m-d H:i:s",$val['log_time']); ?>&emsp;<?php echo $val['log_msg'];?></li>
      <?php } ?>
    </ul>
    <?php } ?>

    <!-- 退款记录 -->
    <?php if(is_array($output['refund_list']) and !empty($output['refund_list'])) { ?>
    <h3><?php echo $lang['member_order_refund'];?></h3>
    <ul class="log-list">
      <?php foreach($output['refund_list'] as $val) { ?>
      <li> 发生时间<?php echo $lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$val['admin_time']); ?>&emsp;&emsp;退款单号<?php echo $lang['nc_colon'];?><?php echo $val['refund_sn'];?>&emsp;&emsp;退款金额<?php echo $lang['nc_colon'];?><?php echo $lang['currency'];?><?php echo $val['refund_amount']; ?>&emsp;备注<?php echo $lang['nc_colon'];?><?php echo $val['goods_name'];?></li>
      <?php } ?>
    </ul>
    <?php } ?>

    <!-- 退货记录 -->
    <?php if(is_array($output['return_list']) and !empty($output['return_list'])) { ?>
    <h3><?php echo $lang['member_order_return'];?></h3>
    <ul class="log-list">
      <?php foreach($output['return_list'] as $val) { ?>
      <li> 发生时间<?php echo $lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$val['admin_time']); ?>&emsp;&emsp;退款单号<?php echo $lang['nc_colon'];?><?php echo $val['refund_sn'];?>&emsp;&emsp;退款金额<?php echo $lang['nc_colon'];?><?php echo $lang['currency'];?><?php echo $val['refund_amount']; ?>&emsp;备注<?php echo $lang['nc_colon'];?><?php echo $val['goods_name'];?></li>
      <?php } ?>
    </ul>
    <?php } ?>
  </div>
</div>
</div>