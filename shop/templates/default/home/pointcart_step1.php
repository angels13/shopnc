<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_cart.css" rel="stylesheet" type="text/css">
<style type="text/css">
.head-search-bar, .head-user-menu, .public-nav-layout {
	display: none !important;
}
</style>
<div class="wrapper pr">
  <ul class="ncc-flow ncc-point-flow">
    <li class=""><i class="step1"></i>
      <p><?php echo $lang['pointcart_ensure_order'];?></p>
      <sub></sub>
      <div class="hr"></div>
    </li>
    <li class="current"><i class="step2"></i>
      <p><?php echo $lang['pointcart_ensure_info'];?></p>
      <sub></sub>
      <div class="hr"></div>
    </li>
    <li class=""><i class="step4"></i>
      <p><?php echo $lang['pointcart_exchange_finish'];?></p>
      <sub></sub>
      <div class="hr"></div>
    </li>
  </ul>
  <div class="ncc-main">
    <div class="ncc-title">
      <h3><?php echo $lang['pointcart_ensure_info'];?></h3>
      <h5>请仔细核对填写收货、发票等信息，以确保物流快递及时准确投递。</h5>
    </div>
    <form method="post" id="porder_form" name="porder_form" action="index.php?act=pointcart&op=step2">
      <div class="ncc-receipt-info">
        <div class="ncc-receipt-info-title">
          <h3><?php echo $lang['pointcart_step1_receiver_address'];?><a href="index.php?act=member&op=address" target="_blank">[管理]</a></h3>
        </div>
        <!-- 已经存在的收获地址start -->
        <div id="addressone_model" style="display:none;">
          <ul class="receive_add address_item">
            <li class="goto"><?php echo $lang['cart_step1_receiver_jsz'];?></li>
            <li address="" buyer=""></li>
          </ul>
        </div>
        <div id="addresslist" class="ncc-candidate-items">
          <?php foreach((array)$output['address_list'] as $k=>$val){ ?>
          <ul class="receive_add address_item <?php if ($k == 0) echo 'selected_address'; ?>">
            <li class="goto">
              <?php if ($k == 0) echo $lang['cart_step1_receiver_jsz'];else echo '&nbsp;';?>
            </li>
            <li address="<?php echo $val['area_info']; ?>&nbsp;&nbsp;<?php echo $val['address']; ?>" buyer="<?php echo $val['true_name']; ?>&nbsp;&nbsp;<?php if($val['mob_phone']) echo $val['mob_phone']; else echo $val['tel_phone']; ?>">
              <input id="address_<?php echo $val['address_id']; ?>" type="radio" city_id="<?php echo $val['city_id']?>" name="address_options" value="<?php echo $val['address_id']; ?>" <?php if ($k == 0) echo 'checked'; ?>/>
              &nbsp;&nbsp;<?php echo $val['area_info']; ?>&nbsp;&nbsp;<?php echo $val['address']; ?>&nbsp;&nbsp; <?php echo $val['true_name']; ?><?php echo $lang['cart_step1_receiver_shou'];?>&nbsp;&nbsp;
              <?php if($val['mob_phone']) echo $val['mob_phone']; else echo $val['tel_phone']; ?>
            </li>
          </ul>
          <?php } ?>
          <input type="hidden" id="chooseaddressid" name="chooseaddressid" value='<?php echo $output['address_list'][0]['address_id'];?>'/>
        </div>
        <!-- 已经存在的收获地址end -->
      </div>

      <!-- 留言start -->
      <div class="ncc-receipt-info">
        <div class="ncc-receipt-info-title">
          <h3><?php echo $lang['pointcart_step1_chooseprod'];?></h3>
        </div>

        <!-- 已经选择礼品start -->

        <table class="ncc-table-style">
          <thead>
            <tr>
              <th class="w20"></th>
              <th class="tl" colspan="2"><?php echo $lang['pointcart_step1_goods_info'];?></th>
              <th class="w120"><?php echo $lang['pointcart_step1_goods_point'];?></th>
              <th class="w120"><?php echo $lang['pointcart_step1_goods_num'];?></th>
            </tr>
          </thead>
          <tbody>
            <?php
	  			if(is_array($output['pointprod_arr']['pointprod_list']) and count($output['pointprod_arr']['pointprod_list'])>0) {
				foreach($output['pointprod_arr']['pointprod_list'] as $val) {
			?>
            <tr class="shop-list ">
              <td></td>
              <td class="w60"><a href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $val['pgoods_id']));?>" class="ncc-goods-thumb" target="_blank"><img src="<?php echo pointprodThumb($val['pgoods_image']); ?>" alt="<?php echo $val['pgoods_name']; ?>"/></a></td>
              <td class="tl"><dl class="ncc-goods-info">
                  <dt><a target="_blank" href="<?php echo urlShop('pointprod', 'pinfo', array('id' => $val['pgoods_id']));?>"><?php echo $val['pgoods_name']; ?></a></dt>
                </dl></td>
              <td><?php echo $val['onepoints']; ?><?php echo $lang['points_unit']; ?></td>
              <td><?php echo $val['quantity']; ?></td>
            </tr>
            <?php } }?>
            <tr>
            <td></td>
              <td colspan="20" class="tl"><label><?php echo $lang['pointcart_step1_message'].$lang['nc_colon'];?>
                  <input type="text" class="w400 text" onclick="pcart_messageclear(this);" value="<?php echo $lang['pointcart_step1_message_advice'];?>" />
                </label></td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="20"><div class="ncc-all-account"><?php echo $lang['pointcart_cart_allpoints'].$lang['nc_colon'];?><em><?php echo $output['pointprod_arr']['pgoods_pointall']; ?></em><?php echo $lang['points_unit']; ?></div></td>
            </tr>
          </tfoot>
        </table>
        <!-- 已经选择礼品end -->

      </div>
      <div class="ncc-bottom"> <a href="index.php?act=pointcart" class="ncc-btn"><?php echo $lang['pointcart_step1_return_list'];?></a><a href="javascript:void($('#porder_form').submit());" class="ncc-btn ncc-btn-acidblue fr"><?php echo $lang['pointcart_step1_submit'];?></a> </div>
    </form>
  </div>
</div>
<script type="text/javascript">
	//<![CDATA[
	var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
    //选择已经存在的收货人地址
    $('.address_item').live('click',function(){
    	$(this).parent().find('.goto').html('&nbsp;');
    	$(this).children().first().html('<?php echo $lang['cart_step1_receiver_jsz'];?>');
        var checked_address_radio = $(this).find("input[name='address_options']");
        $(checked_address_radio).attr('checked', true);
        $('.address_item').removeClass('selected_address');
        $(this).addClass('selected_address');
        $("#chooseaddressid").val($(checked_address_radio).val());
    });
	function pcart_messageclear(tt){
		if (!tt.name)
		{
			tt.value    = '';
			tt.name = 'pcart_message';
		}
	}
</script>
