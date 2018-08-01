<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.ncc-table-style tbody tr.item_disabled td {
	background: none repeat scroll 0 0 #F9F9F9;
	height: 30px;
	padding: 10px 0;
	text-align: center;
}
</style>
<div class="ncc-receipt-info">
  <div class="ncc-receipt-info-title">
    <h3>商品清单</h3>
    <?php if(!empty($output['ifcart'])){?>
    <a href="index.php?act=cart"><?php echo $lang['cart_step1_back_to_cart'];?></a>
    <?php }?>
  </div>
  <table class="ncc-table-style">
    <thead>
      <tr>
        <th class="w20"></th>
        <th></th>
        <th><?php echo $lang['cart_index_store_goods'];?></th>
        <th class="w120"><?php echo $lang['cart_index_price'].'('.$lang['currency_zh'].')';?></th>
        <th class="w120"><?php echo $lang['cart_index_amount'];?></th>
        <th class="w120"><?php echo $lang['cart_index_sum'];?></th>
      </tr>
    </thead>
    <?php foreach($output['store_cart_list'] as $store_id => $cart_list) {?>
    <tbody>
      <tr>
        <th colspan="20"><i class="icon-home"></i><a target="_blank" href="<?php echo urlShop('show_store','index',array('store_id'=>$store_id));?>"><?php echo $cart_list[0]['store_name']; ?></a>
          <div class="store-sale">
            <?php if (!empty($output['cancel_calc_sid_list'][$store_id])) {?>
            <em><i class="icon-gift"></i>免运费</em><?php echo $output['cancel_calc_sid_list'][$store_id]['desc'];?>
            <?php } ?>
            <?php if (!empty($output['store_mansong_rule_list'][$store_id])) {?>
            <em><i class="icon-gift"></i>满即送</em><?php echo $output['store_mansong_rule_list'][$store_id]['desc'];?>
            <?php } ?>
            &emsp;</div>
        </th>
      </tr>
      <?php foreach($cart_list as $cart_info) {?>
      <tr id="cart_item_<?php echo $cart_info['cart_id'];?>" class="shop-list <?php echo ($cart_info['state'] && $cart_info['storage_state']) ? '' : 'item_disabled';?>">
        <td><?php if ($cart_info['state'] && $cart_info['storage_state']) {?>
          <input type="hidden" value="<?php echo $cart_info['cart_id'].'|'.$cart_info['goods_num'];?>" name="cart_id[]">
          <?php } ?></td>
        <?php if ($cart_info['bl_id'] == '0') {?>
        <td class="w60"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$cart_info['goods_id']));?>" target="_blank" class="ncc-goods-thumb"><img src="<?php echo thumb($cart_info,60);?>" alt="<?php echo $cart_info['goods_name']; ?>" /></a></td>
        <?php } ?>
        <td class="tl" <?php if ($cart_info['bl_id'] != '0') {?>colspan="2"<?php }?>><dl class="ncc-goods-info">
            <dt><a href="<?php echo urlShop('goods','index',array('goods_id'=>$cart_info['goods_id']));?>" target="_blank"><?php echo $cart_info['goods_name']; ?></a></dt>
            <dd>
              <?php if ($cart_info['ifxianshi']) {?>
              <span class="xianshi">限时折扣</span>
              <?php }?>
              <?php if ($cart_info['ifgroupbuy']) {?>
              <span class="groupbuy">团购</span>
              <?php }?>
              <?php if ($cart_info['bl_id'] != '0') {?>
              <span class="buldling">优惠套装</span>
              <?php }?>
            </dd>
          </dl></td>
        <td class="w120">￥<em><?php echo $cart_info['goods_price']; ?></em></span></td>
        <td class="w60"><?php echo $cart_info['state'] ? $cart_info['goods_num'] : ''; ?></td>
        <td class="w120"><?php if ($cart_info['state'] && $cart_info['storage_state']) {?>
          ￥<em id="item<?php echo $cart_info['cart_id']; ?>_subtotal" nc_type="eachGoodsTotal"><?php echo $cart_info['goods_total']; ?></em>
          <?php } elseif (!$cart_info['storage_state']) {?>
          <span style="color: #F00;">库存不足</span>
          <?php }elseif (!$cart_info['state']) {?>
          <span style="color: #F00;">已下架</span>
          <?php }?></td>
      </tr>
      
      <!-- S bundling goods list -->
      <?php if (is_array($cart_info['bl_goods_list'])) {?>
      <?php foreach ($cart_info['bl_goods_list'] as $goods_info) { ?>
      <tr class="shop-list <?php echo $cart_info['state'] && $cart_info['storage_state'] ? '' : 'item_disabled';?>">
        <td></td>
        <td class="w60"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank" class="ncc-goods-thumb"><img src="<?php echo cthumb($goods_info['goods_image'],60,$store_id);?>" alt="<?php echo $goods_info['goods_name']; ?>" /></a></td>
        <td class="tl"><dl class="ncc-goods-info">
            <dt><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank"><?php echo $goods_info['goods_name']; ?></a> </dt>
          </dl></td>
        <td>￥<em><?php echo $goods_info['bl_goods_price'];?></em></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <?php } ?>
      <?php  } ?>
      <!-- E bundling goods list -->
      <?php } ?>
      
      <!-- S zengpin list -->
      <?php if (is_array($output['store_premiums_list'][$store_id])) {?>
      <?php foreach ($output['store_premiums_list'][$store_id] as $goods_info) { ?>
      <tr class="shop-list">
        <td></td>
        <td class="w60"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank" class="ncc-goods-thumb"><img src="<?php echo cthumb($goods_info['goods_image'],60,$store_id);?>" alt="<?php echo $goods_info['goods_name']; ?>" /></a></td>
        <td class="tl"><dl class="ncc-goods-info">
            <dt><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank" ><?php echo $goods_info['goods_name']; ?></a></dt>
            <dd><span class="zengpin">赠品</span></dd>
          </dl></td>
        <td>￥0.00</td>
        <td>1</td>
        <td> ￥0.00</td>
        <td></td>
      </tr>
      <?php } ?>
      <?php  } ?>
      <!-- E zengpin list -->
      
      <tr>
        <td class="w10"></td>
        <td class="tl" colspan="2">买家留言：
          <input type="text" value="" class="text w340" name="pay_message[<?php echo $store_id;?>]" maxlength="150">
          &nbsp;</td>
        <td class="tl" colspan="10"><div class="ncc-form-default"> </div></td>
      </tr>
      <tr>
        <td class="tr" colspan="20"><div class="ncc-store-account">
            <dl class="freight">
              <dt>运费：</dt>
              <dd>￥<em id="eachStoreFreight_<?php echo $store_id;?>">0.00</em></dd>
            </dl>
            <dl>
              <dt>商品金额：</dt>
              <dd>￥<em id="eachStoreGoodsTotal_<?php echo $store_id;?>"><?php echo $output['store_goods_total'][$store_id];?></em></dd>
            </dl>
            <?php if (!empty($output['store_mansong_rule_list'][$store_id]['discount'])) {?>
            <dl class="mansong">
              <dt>满即送-<?php echo $output['store_mansong_rule_list'][$store_id]['desc'];?>：</dt>
              <dd>￥<em id="eachStoreManSong_<?php echo $store_id;?>">-<?php echo $output['store_mansong_rule_list'][$store_id]['discount'];?></em></dd>
            </dl>
            <?php } ?>

            <!-- S voucher list -->

            <?php if (!empty($output['store_voucher_list'][$store_id]) && is_array($output['store_voucher_list'][$store_id])) {?>
            <dl class="voucher">
              <dt>
                <select nctype="voucher" name="voucher[<?php echo $store_id;?>]">
                  <option value="<?php echo $voucher['voucher_t_id'];?>|<?php echo $store_id;?>|0.00">选择代金券</option>
                  <?php foreach ($output['store_voucher_list'][$store_id] as $voucher) {?>
                  <option value="<?php echo $voucher['voucher_t_id'];?>|<?php echo $store_id;?>|<?php echo $voucher['voucher_price'];?>"><?php echo $voucher['desc'];?></option>
                  <?php } ?>
                </select> ：
              <dd>￥<em id="eachStoreVoucher_<?php echo $store_id;?>">-0.00</em></dd>
            </dl>
            <?php } ?>

            <!-- E voucher list -->

            <dl class="total">
              <dt>本店合计：</dt>
              <dd>￥<em store_id="<?php echo $store_id;?>" nc_type="eachStoreTotal"></em></dd>
            </dl>
          </div></td>
      </tr>
      <?php }?>

     <!-- S 预存款 -->
     <?php if (!empty($output['available_pd_amount'])) { ?>
     <tr>
        <td class="pd-account" colspan="20"><div class="ncc-pd-account">
            <div class="mt5 mb5"><label><input type="checkbox" checked class="vm mr5" value="1" name="pd_pay">使用预存款支付（当前可用余额：<em>￥<?php echo $output['available_pd_amount'];?></em>）</label></div>
            <div id="pd_password">登录密码：<input type="password" class="text w120" value="" name="password" id="password" maxlength="35">
            <input type="hidden" value="" name="password_callback" id="password_callback">
              <a class="ncc-btn-mini ncc-btn-orange" id="pd_pay_submit" href="javascript:void(0)">使用</a>
             </div>
          </div></td>
      </tr>
     <?php } ?>
     <!-- E 预存款 -->

    </tbody>
    <tfoot>
      <tr>
        <td colspan="20"><div class="ncc-all-account">订单总金额：￥<em id="orderTotal"></em>元</div></td>
      </tr>
    </tfoot>
  </table>
</div>
