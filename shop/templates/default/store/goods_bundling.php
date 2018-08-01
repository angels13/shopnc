<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if(!empty($output['bundling_array']) && is_array($output['bundling_array'])){$i=0;?>

<div class="ncs-bundling-container">
  <div class="F-center">
  <?php foreach($output['bundling_array'] as $val){?>
  <?php if(!empty($output['b_goods_array'][$val['id']]) && is_array($output['b_goods_array'][$val['id']])){$i++;?>
  <div class="ncs-bundling-list">
  <ul>
    <h3><?php echo $lang['bundling'].$i.$lang['nc_colon'].$val['name'];?></h3>
    <?php ksort($output['b_goods_array'][$val['id']]);foreach($output['b_goods_array'][$val['id']] as $v){?>
    <li>
      <dl>
        <dt class="goods-name" title="<?php echo $v['name'];?>"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $v['id']));?>" target="block"><?php echo $v['name'];?></a></dt>
        <dd class="goods-thumb"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $v['id']));?>" target="block"><img src="<?php echo $v['image'];?>" title="<?php echo $v['name'];?>" alt="<?php echo $v['name'];?>" onload="javascript:DrawImage(this,100,100);" /></a></dd>
        <dd class="goods-price">商城价<?php echo $lang['nc_colon'].$lang['currency'].$v['shop_price'];?></dd>
        <dd class="price">优惠后：<?php echo $lang['currency'].$v['price'];?></dd>
      </dl>
    </li>
    <?php }?>
  </ul></div>
  <div class="ncs-bundling-price">
    <p class="tcj">套装价：<span><?php echo $lang['currency'].$val['price'];?></span></p>
    <p class="js"><?php echo $lang['bundling_save'];?><span><?php echo $lang['currency'].ncPriceFormat(floatval($val['cost_price'])-floatval($val['price']));?></span></p>
    <?php if ($val['freight'] > 0) {?>
    <p class="">运&emsp;费：<span><?php echo $lang['currency'].$val['freight'];?></span></p>
    <?php }?>
    <p class="mt5"><a href="javascript:void(0);"  nctype="addblcart_submit" bl_id="<?php echo $val['id']?>" class="ncs-btn ncs-btn-red"><i class="icon-th-large"></i><?php echo $lang['bundling_buy'];?></a></p>
  </div>
  <?php }?>
  <?php }?>
  </div>
<?php if(count($output['bundling_array']) != 1){?>
<div class="F-prev">&nbsp;</div>
<div class="F-next">&nbsp;</div>
<?php }?>
</div>
<script>
$(function(){
	$('#ncs-bundling').show();
	$('.ncs-bundling-container').F_slider({len:<?php echo $i;?>});
    $('a[nctype="addblcart_submit"]').click(function(){
        addblcart($(this).attr('bl_id'));
     });	
});

/* add one bundling to cart */ 
function addblcart(bl_id)
{
	<?php if ($_SESSION['is_login'] !== '1'){?>
	   login_dialog();
    <?php } else {?>
        var url = 'index.php?act=cart&op=add';
        $.getJSON(url, {'bl_id':bl_id}, function(data){
        	if(data != null){
        		if (data.state)
                {
                    $('#bold_num').html(data.num);
                    $('#bold_mly').html(price_format(data.amount));
                    $('.ncs-cart-popup').fadeIn('fast');
                    // 头部加载购物车信息
                    load_cart_information();
                }
                else
                {
                    showDialog(data.msg, 'error','','','','','','','','',2);
                }
        	}
        });
    <?php } ?>
}
</script>
<?php }?>
