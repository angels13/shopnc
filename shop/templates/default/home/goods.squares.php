<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
#box { background: #FFF; width: 238px; height: 410px; margin: -390px 0 0 0; display: block; border: solid 4px #D93600; position: absolute; z-index: 999; opacity: .5}
.shopMenu { position: fixed; z-index:1; right:25%; top: 0;}

</style>
<div class="squares" nc_type="current_display_mode">
  <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){?>
  <ul class="list_pic">
    <?php foreach($output['goods_list'] as $value){?>
    <li class="item">
      <div class="goods-content" nctype_goods=" <?php echo $value['goods_id'];?>" nctype_store="<?php echo $value['store_id'];?>">
        <div class="goods-pic"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$value['goods_id']));?>" target="_blank" title="<?php echo $value['goods_name'];?>"><img src="<?php echo thumb($value, 240);?>" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a> </div>
        <?php if ($value['group_flag']) {?>
        <div class="goods-promotion"><span>团购商品</span></div>
        <?php } elseif ($value['xianshi_flag'])  {?>
        <div class="goods-promotion"><span>限时折扣</span></div>
        <?php }?>
        <div class="goods-info">
          <div class="goods-pic-scroll-show">
            <?php if(!empty($value['image'])) {?>
            <ul>
              <?php $i=0;foreach ($value['image'] as $val) {$i++?>
              <li<?php if($i==1) {?> class="selected"<?php }?>><a href="javascript:void(0);"><img src="<?php echo thumb($val, 60);?>"/></a></li>
              <?php }?>
            </ul>
            <?php }?>
          </div>
          <div class="goods-name"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$value['goods_id']));?>" target="_blank" title="<?php echo $value['goods_jingle'];?>"><?php echo $value['goods_name'];?><em><?php echo $value['goods_jingle'];?></em></a></div>

          <div class="goods-price">
          <em class="sale-price" title="<?php echo $lang['goods_class_index_store_goods_price'].$lang['nc_colon'].$lang['currency'].$value['goods_price'];?>"><?php echo ncPriceFormatForList($value['goods_price']);?></em>
          <em class="market-price" title="市场价：<?php echo $lang['currency'].$value['goods_marketprice'];?>"><?php echo ncPriceFormatForList($value['goods_marketprice']);?></em>
          <span class="raty" data-score="<?php echo $value['evaluation_good_star'];?>"></span>
      </div>
          <div class="sell-stat">
            <ul>
              <li><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $value['goods_id']));?>#ncGoodsRate" target="_blank" class="status"><?php echo $value['goods_salenum'];?></a><p>商品销量</p></li>
              <li><a href="<?php echo urlShop('goods', 'comments_list', array('goods_id' => $value['goods_id']));?>" target="_blank"><?php echo $value['evaluation_count'];?></a><p>用户评论</p></li>
              <li><em member_id="<?php echo $value['member_id'];?>">&nbsp;</em></li>
            </ul>
          </div>
          <div class="store"><a href="<?php echo urlShop('show_store','index',array('store_id'=>$value['store_id']), $value['store_domain']);?>" title="<?php echo $value['store_name'];?>" class="name"><?php echo $value['store_name'];?></a></div>
         <div class="add-cart">
           <?php if ($value['group_flag']) {?>
           <a href="javascript:void(0);" nctype="buy_now" data-param="{goods_id:<?php echo $value['goods_id'];?>}"><i class="icon-shopping-cart"></i>立即购买</a>
           <?php } else {?>
           <a href="javascript:void(0);" nctype="add_cart" data-param="{goods_id:<?php echo $value['goods_id'];?>}"><i class="icon-shopping-cart"></i>加入购物车</a>
           <?php }?>
         </div>
        </div>
      </div>
    </li>
    <?php }?>
    <div class="clear"></div>
  </ul>
  <?php }else{?>
  <div id="no_results" class="no-results"><i></i><?php echo $lang['index_no_record'];?></div>
  <?php }?>
</div>
<form id="buynow_form" method="post" action="<?php echo SHOP_SITE_URL;?>/index.php" target="_blank">
  <input id="act" name="act" type="hidden" value="buy" />
  <input id="op" name="op" type="hidden" value="buy_step1" />
  <input id="goods_id" name="cart_id[]" type="hidden"/>
</form>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/jquery.raty.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.raty').raty({
            path: "<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/img",
            readOnly: true,
            width: 80,
            score: function() {
              return $(this).attr('data-score');
            }
        });
    });
</script>

