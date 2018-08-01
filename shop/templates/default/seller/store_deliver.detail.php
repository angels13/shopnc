<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
#container .layout {
	background: none !important;
	min-height: 100px!important;
}
</style>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="store_deliver" />
    <input type="hidden" name="op" value="search_deliver" />
    <tr>
      <td>&nbsp;</td>
      <th class="tr"><?php echo $lang['store_order_order_sn'];?></th>
      <td class="w150"><input type="text" class="text" name="order_sn" value="<?php echo trim($_GET['order_sn']); ?>"  placeholder="<?php echo $lang['store_order_order_sn_search']; ?>" /></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit"value="<?php echo $lang['store_order_search'];?>" /></label></td>
    </tr>
  </table>
</form>
  <?php if ($output['order_info']['order_sn'] == ''){?>
  <table class="ncsc-table-style">
    <tbody>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['nc_common_result_null'];?></span></td>
      </tr>
    </tbody>
  </table>
  <?php }else {?>
<div class="mt15">
  <h4 class="mb5"><?php echo $lang['member_show_express_ship_dstatus'];?></h4>
  <span><?php echo $lang['member_change_order_no'].$lang['nc_colon'];?><?php echo $output['order_info']['order_sn']; ?></span><span class="ml30"><?php echo $lang['member_order_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></span></div>
<ul class="express-log" id="express_list">
  <?php if(!empty($output['order_info']['extend_order_common']['shipping_time'])) { ?>
  <li class="loading"><?php echo $lang['nc_common_loading'];?></li>
  <?php } ?>
</ul>
<div class="ncm-notes" ><span><?php echo $lang['member_show_expre_type'];?></span><span class="ml30"><?php echo $lang['member_show_express_ship_code'].$lang['nc_colon'];?><?php echo $output['order_info']['shipping_code']; ?></span><span class="ml30"><?php echo $lang['member_show_expre_company'].$lang['nc_colon'];?><a target="_blank" href="<?php echo $output['e_url'];?>"><?php echo $output['e_name'];?></a></span></div>
<div class="express-oredr">
  <h4><?php echo $lang['member_show_order_info'];?></h4>
  <ul>
    <?php if(is_array($output['order_info']['extend_order_goods']) && !empty($output['order_info']['extend_order_goods'])) {
					foreach($output['order_info']['extend_order_goods'] as $goods) {
				?>
    <li>
      <div class="goods-pic-small"><span class="thumb size60 tip" title="<?php echo $goods['goods_name']; ?>"><i></i><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$goods['goods_id']));?>"><img src="<?php echo thumb($goods, 60); ?>"/></a></span> </div>
      <div class="goods-name"><?php echo $goods['goods_name']; ?></div>
      <div class="goods-price"><i class="mr5"><?php echo $goods['goods_price']; ?></i>x <?php echo $goods['goods_num']; ?></div>
    </li>
    <?php } } ?>
  </ul>
</div>
<div class="express-add">
  <p><strong><?php echo $lang['member_show_receive_info'].$lang['nc_colon'];?></strong><?php echo $output['order_info']['extend_order_common']['reciver_name']?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></p>
  <p><strong><?php echo $lang['member_show_deliver_info'].$lang['nc_colon'];?></strong><?php echo $output['daddress_info']['seller_name']; ?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>&nbsp;<?php echo $output['daddress_info']['company'];?></p>
</div>
<?php }?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script> 
<script>
$(function(){
	//Ajax提示
	$('.tip').poshytip({
		className: 'tip-yellowsimple',
		showTimeout: 1,
		alignTo: 'target',
		alignX: 'center',
		alignY: 'bottom',
		offsetX: 5,
		offsetY: 0,
		allowTipHover: false
	});
      var_send = '<li><?php echo date("Y-m-d H:i:s",$output['order_info']['extend_order_common']['shipping_time']); ?>&nbsp;&nbsp;<?php echo $lang['member_show_seller_has_send'];?></li>';
	$.getJSON('index.php?act=store_deliver&op=get_express&e_code=<?php echo $output['e_code']?>&shipping_code=<?php echo $output['order_info']['shipping_code']?>&t=<?php echo random(7);?>',function(data){
		if(data){
			data = var_send+data;
			$('#express_list').html(data).next().css('display','');
		}else{
			$('#express_list').html(var_send);
		}
	});
});
</script>