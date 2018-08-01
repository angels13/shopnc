<?php defined('InShopNC') or exit('Access Invalid!');?>
  <article id="content">
    <section class="layout expanded mt10" >
      <article class="nc-goods-main">
        <div class="flexslider">
          <ul class="slides">
            <?php if(!empty($output['store_slide']) && is_array($output['store_slide'])){?>
            <?php for($i=0;$i<5;$i++){?>
            <?php if($output['store_slide'][$i] != ''){?>
            <li><a <?php if($output['store_slide_url'][$i] != '' && $output['store_slide_url'][$i] != 'http://'){?>href="<?php echo $output['store_slide_url'][$i];?>"<?php }?>><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_SLIDE.DS.$output['store_slide'][$i];?>"></a></li>
            <?php }?>
            <?php }?>
            <?php }else{?>
            <li><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_SLIDE.DS;?>f01.jpg"></li>
            <li><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_SLIDE.DS;?>f02.jpg"></li>
            <li><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_SLIDE.DS;?>f03.jpg"></li>
            <li><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_SLIDE.DS;?>f04.jpg"></li>
            <?php }?>
          </ul>
        </div>
        <div class="nc-s-c-s3 ncg-list mt10">
          <div class="title pngFix"> <span><a href="<?php echo urlShop('show_store', 'goods_all', array('store_id' => $_GET['store_id']));?>" class="more"><?php echo $lang['nc_more'];?></a></span>
            <h4> <?php echo $lang['show_store_index_recommend'];?></h4>
          </div>
          <div class="content pt20">
            <?php if(!empty($output['recommended_goods_list']) && is_array($output['recommended_goods_list'])){?>
            <ul>
              <?php foreach($output['recommended_goods_list'] as $value){?>
              <li>
                <dl>
                  <dt><a href="<?php echo urlShop('goods', 'index', array('goods_id'=>$value['goods_id']));?>" target="_blank"><?php echo $value['goods_name']?></a></dt>
                  <dd class="ncg-pic pngFix"><a href="<?php echo urlShop('goods', 'index', array('goods_id'=>$value['goods_id']));?>" target="_blank" class="thumb"><i></i><img src="<?php echo thumb($value, 240);?>" onload="javascript:DrawImage(this,160,160);" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a></dd>
                  <dd class="ncg-price"><em class="price"><i><?php echo $lang['currency'];?></i>
                      <?php if(intval($value['group_flag']) === 1) { ?>
                      <?php echo $value['group_price']?>
                      <?php } elseif(intval($value['xianshi_flag']) === 1) { ?>
                      <?php echo ncPriceFormat($value['goods_price'] * $value['xianshi_discount'] / 10);?>
                      <?php } else { ?>
                      <?php echo $value['goods_price']?>
                      <?php } ?>
                  </em></dd>
                  <dd class="ncg-sold"><?php echo $lang['show_store_index_be_sold'];?><strong><?php echo $value['goods_salenum'];?></strong> <?php echo $lang['nc_jian'];?></dd>
                </dl>
              </li>
              <?php }?>
            </ul>
            <?php }else{?>
            <div class="nothing">
              <p><?php echo $lang['show_store_index_no_record'];?></p>
            </div>
            <?php }?>
          </div>
        </div>
        <div class="nc-s-c-s3 ncg-list mt10">
          <div class="title pngFix"><span><a href="<?php echo urlShop('show_store', 'goods_all', array('store_id' => $_GET['store_id']));?>" class="more"><?php echo $lang['nc_more'];?></a></span>
            <h4><?php echo $lang['show_store_index_new_goods'];?></h4>
          </div>
          <div class="content pt20">
            <?php if(!empty($output['new_goods_list']) && is_array($output['new_goods_list'])){?>
            <ul>
              <?php foreach($output['new_goods_list'] as $value){?>
              <li>
                <dl>
                  <dt><a href="<?php echo urlShop('goods', 'index', array('goods_id'=>$value['goods_id']));?>" target="_blank"><?php echo $value['goods_name'];?></a></dt>
                  <dd class="ncg-pic pngFix"><a href="<?php echo urlShop('goods', 'index', array('goods_id'=>$value['goods_id']));?>" target="_blank" class="thumb"><i></i><img src="<?php echo thumb($value, 240)?>" onload="javascript:DrawImage(this,160,160);" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a></dd>
                  <dd class="ncg-price"><em class="price"><i><?php echo $lang['currency'];?></i>
                      <?php if(intval($value['group_flag']) === 1) { ?>
                      <?php echo $value['group_price']?>
                      <?php } elseif(intval($value['xianshi_flag']) === 1) { ?>
                      <?php echo ncPriceFormat($value['goods_price'] * $value['xianshi_discount'] / 10);?>
                      <?php } else { ?>
                      <?php echo $value['goods_price']?>
                      <?php } ?>
                  </em></dd>
                  <dd class="ncg-sold"><?php echo $lang['show_store_index_be_sold'];?><strong><?php echo $value['goods_salenum'];?></strong> <?php echo $lang['nc_jian'];?></dd>
                </dl>
              </li>
              <?php }?>
            </ul>
            <?php }else{?>
            <div class="nothing">
              <p><?php echo $lang['show_store_index_no_record'];?></p>
            </div>
            <?php }?>
          </div>
        </div>
      </article>
      <aside class="nc-sidebar">
        <?php include template('store/info');?>
        <?php include template('store/left');?>
      </aside>
    </section>
  </article>
<!-- 引入幻灯片JS --> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.flexslider-min.js"></script> 
<!-- 绑定幻灯片事件 --> 
<script type="text/javascript">
	$(window).load(function() {
		$('.flexslider').flexslider();
	});
</script>
