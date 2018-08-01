<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
#container .layout {
	background: none !important;
	min-height: 100px!important;
}
</style>

<div class="wrap-shadow">
  <div class="wrap-all ncu-order-view">
    <h2><?php echo $lang['member_show_express_detail'];?></h2>
    <dl class="express-info">
      <dt><span><?php echo $lang['member_change_order_no'].$lang['nc_colon'];?><?php echo $output['order_info']['order_sn']; ?></span><span class="ml30"><?php echo $lang['member_order_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></span><span class="ml30"><?php echo $output['store_info']['store_name']; ?></span></dt>
      <dd><?php echo $lang['member_show_expre_my_fdback'].$lang['nc_colon'];?><?php echo $output['order_info']['extend_order_common']['order_message']; ?></dd>
    </dl>
    <div class="express-detail">
      <div class="sidebar">
        <p><?php echo $lang['member_show_expre_type'];?></p>
        <p><?php echo $lang['member_show_express_ship_code'].$lang['nc_colon'];?><?php echo $output['order_info']['shipping_code']; ?></p>
        <p><?php echo $lang['member_show_expre_company'].$lang['nc_colon'];?><a target="_blank" href="<?php echo $output['e_url'];?>"><?php echo $output['e_name'];?></a></p>
      </div>
      <div class="wrap">
        <div class="tabmenu">
          <ul class="tab pngFix">
            <li class="active"><a><?php echo $lang['member_show_express_ship_dstatus'];?></a></li>
          </ul>
        </div>
        <ul class="express-log" id="express_list">
          <?php if($output['order_info']['extend_order_common']['shipping_time']) { ?>
          <li class="loading"><?php echo $lang['nc_common_loading'];?></li>
          <?php } ?>
        </ul>
        <div class="ncm-notes" style="display:none"><?php echo $lang['member_show_express_ship_tips'];?></div>
        <div class="express-oredr">
          <h4><?php echo $lang['member_show_order_info'];?></h4>
          <ul>
            <?php if(is_array($output['order_goods_list']) and !empty($output['order_goods_list'])) {
					foreach($output['order_goods_list'] as $val) {
				?>
            <li>
              <div class="goods-pic-small"><span class="thumb size60 tip" title="<?php echo $val['goods_name']; ?>"><i></i><a target="_blank" href="index.php?act=goods&goods_id=<?php echo $val['goods_id']; ?>"><img src="<?php echo thumb($val,'tiny'); ?>" onload="javascript:DrawImage(this,60,60);"/></a></span> </div>
              <div class="goods-name"><?php echo $val['goods_name']; ?></div>
              <div class="goods-price"><i class="mr5"><?php echo $val['goods_price']; ?></i>x <?php echo $val['goods_num']; ?></div>
            </li>
            <?php } } ?>
          </ul>
        </div>
        <div class="express-add">
          <p><strong><?php echo $lang['member_show_receive_info'].$lang['nc_colon'];?></strong><?php echo $output['order_info']['extend_order_common']['reciver_name']?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></p>
          <p><strong><?php echo $lang['member_show_deliver_info'].$lang['nc_colon'];?></strong><?php echo $output['daddress_info']['seller_name']; ?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>&nbsp;<?php echo $output['daddress_info']['company'];?></p>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script> 

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
	$.getJSON('index.php?act=member_order&op&op=get_express&e_code=<?php echo $output['e_code']?>&shipping_code=<?php echo $output['order_info']['shipping_code']?>&t=<?php echo random(7);?>',function(data){
		if(data){
			data = var_send+data;
			$('#express_list').html(data).next().css('display','');
		}else{
			$('#express_list').html(var_send);
		}
	});
});
</script>