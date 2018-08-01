<?php defined('InShopNC') or exit('Access Invalid!');?>
<script>
var PURL = [<?php echo $output['purl'];?>];
</script>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<script src="<?php echo SHOP_RESOURCE_SITE_URL.'/js/search_goods.js';?>"></script>
<script src="<?php echo RESOURCE_SITE_URL.'/js/class_area_array.js';?>"></script>

<div class="nch-container wrapper">
  <div class="left">
    <div class="nch-module nch-module-style01">
      <div class="title">
        <h3><?php echo $lang['brand_index_recommend_brand'];?></h3>
      </div>
      <div class="content">
        <?php if(isset($output['brand_r']) && is_array($output['brand_r'])){?>
        <ul class="nch-sidebar-brand">
          <?php foreach($output['brand_r'] as $key=>$brand_r){?>
          <li class="picture"> <a href="<?php echo urlShop('brand', 'list',array('brand'=>$brand_r['brand_id']));?>" target="_blank"> <span class="brand-logo"><img src="<?php echo brandImage($brand_r['brand_pic']);?>" alt="<?php echo $brand_r['brand_name'];?>" /></span> <span class="brand-name"><?php echo $brand_r['brand_name'];?></span></a> </li>
          <?php }?>
        </ul>
        <?php }?>
      </div>
    </div>
    <div class="nch-module nch-module-style03"><?php echo loadadv(37,'html');?> </div>
    <div class="nch-module nch-module-style03">
      <div class="title">
        <h3><?php echo $lang['brand_index_viewed_goods']; ?></h3>
      </div>
      <div class="content">
        <?php foreach ($output['viewed_goods'] as $k=>$v){?>
        <dl class="nch-sidebar-bowers">
          <dt class="goods-name"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$v['goods_id'])); ?>"><?php echo $v['goods_name']; ?></a></dt>
          <dd class="goods-pic"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$v['goods_id'])); ?>"><img src="<?php echo thumb($v, 60); ?>" title="<?php echo $v['goods_name']; ?>" alt="<?php echo $v['goods_name']; ?>" ></a></dd>
          <dd class="goods-price"><?php echo $lang['currency'];?><?php echo $v['goods_price']; ?></dd>
        </dl>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="right">
    <div class="shop_con_list" id="main-nav-holder">
      <nav class="sort-bar" id="main-nav">
      <div class="nch-all-category">
        <div class="all-category">
            <?php require template('layout/home_goods_class');?>
        </div>
      </div>
        <div class="nch-sortbar-array"> 排序方式：
          <ul class="array">
            <li <?php if(!$_GET['key']){?>class="selected"<?php }?>><a href="<?php echo dropParam(array('order', 'key'));?>" class="nobg" title="<?php echo $lang['brand_index_default_sort'];?>"><?php echo $lang['brand_index_default'];?></a></li>
            <li <?php if($_GET['key'] == '1'){?>class="selected"<?php }?>><a href="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '1') ? replaceParam(array('key' => '1', 'order' => '1')):replaceParam(array('key' => '1', 'order' => '2')); ?>" <?php if($_GET['key'] == '1'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> title="<?php echo ($_GET['order'] == 'desc' && $_GET['key'] == '1')?$lang['brand_index_sold_asc']:$lang['brand_index_sold_desc']; ?>"><?php echo $lang['brand_index_sold'];?><i></i></a></li>
            <li <?php if($_GET['key'] == '2'){?>class="selected"<?php }?>><a href="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '2') ? replaceParam(array('key' => '2', 'order' => '1')):replaceParam(array('key' => '2', 'order' => '2')); ?>" <?php if($_GET['key'] == '2'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> title="<?php  echo ($_GET['order'] == 'desc' && $_GET['key'] == '2')?$lang['brand_index_click_asc']:$lang['brand_index_click_desc']; ?>"><?php echo $lang['brand_index_click']?><i></i></a></li>
            <li <?php if($_GET['key'] == '3'){?>class="selected"<?php }?>><a href="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '3') ? replaceParam(array('key' => '3', 'order' => '1')):replaceParam(array('key' => '3', 'order' => '2')); ?>" <?php if($_GET['key'] == '3'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> title="<?php echo ($_GET['order'] == 'desc' && $_GET['key'] == '3')?$lang['brand_index_price_asc']:$lang['brand_index_price_desc']; ?>"><?php echo $lang['brand_index_price'];?><i></i></a></li>
          </ul>
        </div>
        <div class="nch-sortbar-owner">商品类型： <span><a href="<?php echo dropParam(array('type'));?>" <?php if (!isset($_GET['type']) || !in_array($_GET['type'], array(1,2))) {?>class="selected"<?php }?>><i></i>全部</a></span> <span><a href="<?php echo replaceParam(array('type' => '1'));?>" <?php if ($_GET['type'] == 1) {?>class="selected"<?php }?>><i></i>商城自营</a></span> <span><a href="<?php echo replaceParam(array('type' => '2'));?>" <?php if ($_GET['type'] == 2) {?>class="selected"<?php }?>><i></i>商家加盟</a></span> </div>
        <div class="nch-sortbar-location">商品所在地：
          <div class="select-layer">
            <div class="holder"><em nc_type="area_name"><?php echo $lang['brand_index_area']; ?><!-- 所在地 --></em></div>
            <div class="selected"><a nc_type="area_name"><?php echo $lang['brand_index_area']; ?><!-- 所在地 --></a></div>
            <i class="direction"></i>
            <ul class="options">
              <?php require(BASE_TPL_PATH.'/home/goods_class_area.php');?>
            </ul>
          </div>
        </div>
        <div class="pagination"> <?php echo $output['show_page1']; ?> </div>
      </nav>

      <!-- 商品列表循环  -->
      <?php require_once (BASE_TPL_PATH.'/home/goods.squares.php');?>
      <div class="tc mt20 mb20">
        <div class="pagination"> <?php echo $output['show_page']; ?> </div>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script>
<script>
$(function(){
 //浮动导航  waypoints.js
    $('#main-nav-holder').waypoint(function(event, direction) {
        $(this).parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
    });
});
<?php if(isset($_GET['area_id']) && intval($_GET['area_id']) > 0){?>
$(function(){
    // 选择地区后的地区显示
    $('[nc_type="area_name"]').html(nc_class_a[<?php echo intval($_GET['area_id']);?>]);
});
<?php }?>
</script>