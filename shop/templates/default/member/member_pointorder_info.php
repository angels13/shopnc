<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
      <h2><?php echo $lang['member_pointorder_info_ordersimple'];?></h2>
      <dl>
        <dt><?php echo $lang['member_pointorder_ordersn'].$lang['nc_colon'];?></dt>
        <dd><strong><?php echo $output['order_info']['point_ordersn']; ?></strong></dd>
        <dt><?php echo $lang['member_pointorder_orderstate'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_orderstatetext']['order_state']; ?><?php echo $output['order_info']['point_orderstatetext']['change_state'] ==''?'':','.$output['order_info']['point_orderstatetext']['change_state'];?></dd>
        <dt><?php echo $lang['member_pointorder_exchangepoints'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_allpoint'];?></dd>
        <?php if ($output['order_info']['point_shippingcharge'] == 1){ ?>
        <dt><?php echo $lang['member_pointorder_shippingfee'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_shippingfee'];?></dd>
        <?php } ?>
        <dt><?php echo $lang['member_pointorder_addtime'].$lang['nc_colon'];?></dt>
        <dd><?php echo @date('Y-m-d H:i:s',$output['order_info']['point_addtime']);?></dd>
      </dl>
      <h3><?php echo $lang['member_pointorder_info_memberinfo'];?></h3>
      <dl>
        <dt><?php echo $lang['member_pointorder_info_membername'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_buyername']; ?></dd>
        <dt><?php echo $lang['member_pointorder_info_memberemail'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_buyeremail']; ?></dd>
        <?php if ($output['order_info']['point_ordermessage']){ ?>
        <dt class="cb"><?php echo $lang['member_pointorder_info_ordermessage'].$lang['nc_colon'];?></dt>
        <dd style="width:90%"><?php echo $output['order_info']['point_ordermessage']; ?></dd>
        <?php } ?>
      </dl>
      <?php if ($output['order_info']['point_shippingcharge'] == 1){?>
      <h3><?php echo $lang['member_pointorder_info_paymentinfo'];?></h3>
      <dl>
        <dt><?php echo $lang['member_pointorder_payment'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_paymentname']; ?></dd>
        <?php if ($output['order_info']['point_paymenttime'] != ''){?>
        <dt><?php echo $lang['member_pointorder_info_paymenttime'].$lang['nc_colon'];?></dt>
        <dd><?php echo @date('Y-m-d H:i:s',$output['order_info']['point_paymenttime']);?></dd>
        <?php } ?>
        <dt class="cb"><?php echo $lang['member_pointorder_info_paymentmessage'].$lang['nc_colon'];?></dt>
        <dd style="width:90%;">        
        <?php $tmp = @unserialize($output['order_info']['point_paymessage']); ?>
        <?php if (is_array($tmp) && count($tmp)>0){ ?>
	      	<?php if ($tmp['user']) echo $lang['pay_bank_user'].' '.stripslashes($tmp['user']);?>
	      	<?php if ($tmp['bank']) echo $lang['pay_bank_bank'].' '.stripslashes($tmp['bank']);?>
	      	<?php if ($tmp['account']) echo $lang['pay_bank_account'].' '.stripslashes($tmp['account']);?>
	      	<?php if ($tmp['num']) echo $lang['pay_bank_num'].' '.stripslashes($tmp['num']);?>
	      	<?php if ($tmp['date']) echo $lang['pay_bank_date'].' '.stripslashes($tmp['date']);?>
	      	<?php if ($tmp['order']) echo $lang['pay_bank_order'].' '.stripslashes($tmp['order']);?>
	      	<?php if ($tmp['extend']) echo $lang['pay_bank_extend'].' '.stripslashes($tmp['extend']);?>
	    <?php }else{?>
        	<?php echo $output['order_info']['point_paymessage']; ?>
        <?php } ?>
        </dd>
      </dl>
      <?php }?>
      <h3><?php echo $lang['member_pointorder_info_shipinfo'];?></h3>
      <dl>
        <dt><?php echo $lang['member_pointorder_info_shipinfo_truename'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_truename']; ?></dd>
        <dt><?php echo $lang['member_pointorder_info_shipinfo_mobphone'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_mobphone']; ?>&nbsp;</dd>
        <dt><?php echo $lang['member_pointorder_info_shipinfo_telphone'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_telphone']; ?>&nbsp;</dd>
        <dt><?php echo $lang['member_pointorder_info_shipinfo_zipcode'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_zipcode']; ?>&nbsp;</dd>
        <?php if ($output['order_info']['point_shippingcode'] != ''){?>
        <dt><?php echo $lang['member_pointorder_shipping_code'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_shippingcode']; ?></dd>
        <?php }?>
        <?php if ($output['order_info']['point_shippingtime'] != ''){?>
        <dt><?php echo $lang['member_pointorder_shipping_time'].$lang['nc_colon'];?></dt>
        <dd><?php echo @date('Y-m-d',$output['order_info']['point_shippingtime']);?></dd>
        <?php }?>
        <?php if ($output['order_info']['point_shippingdesc'] != ''){?>
        <dt><?php echo $lang['member_pointorder_info_shipinfo_description'].$lang['nc_colon'];?></dt>
        <dd><?php echo $output['order_info']['point_shippingdesc']; ?></dd>
        <?php }?>
        <dt class="cb"><?php echo $lang['member_pointorder_info_shipinfo_address'].$lang['nc_colon'];?></dt>
        <dd style="width:90%;"><?php echo $output['order_info']['point_areainfo']; ?><?php echo $output['order_info']['point_address']; ?></dd>
        <div class="clear">&nbsp;</div>
      </dl>
      <table class="ncu-table-style">
        <thead>
          <tr>
            <th class="w10"></th>
            <th class="w70"></th>
            <th><?php echo $lang['member_pointorder_info_prodinfo'];?></th>
            <th class="w200"><?php echo $lang['member_pointorder_exchangepoints'];?></th>
            <th class="w200"><?php echo $lang['member_pointorder_info_prodinfo_exnum'];?></th>
          </tr>
        </thead>
        <tbody>
          <?php if(is_array($output['prod_list']) and !empty($output['prod_list'])) {
	  		foreach($output['prod_list'] as $val) {?>
          <tr class="bd-line">
            <td></td>
            <td><div class="goods-pic-small"><span class="thumb size60"><i></i><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $val['point_goodsid']));?>" target="_blank"> <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_POINTPROD.DS.str_ireplace('.', '_small.', $val['point_goodsimage']);?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(60);?>'" onload="javascript:DrawImage(this,60,60);" /></a></span></div></td>
            <td><dl class="goods-name">
                <dt><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $val['point_goodsid']));?>" target="_blank"><?php echo $val['point_goodsname']; ?></a></dt>
              </dl></td>
            <td><?php echo $val['point_goodspoints']; ?></td>
            <td><?php echo $val['point_goodsnum']; ?></td>
          </tr>
          <?php } }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="20"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
