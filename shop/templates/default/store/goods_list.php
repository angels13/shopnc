<?php defined('InShopNC') or exit('Access Invalid!');?>
  <article id="content">
    <section class="layout expanded mt10" >
      <article class="nc-goods-main">
        <div class="nc-s-c-s3 ncg-list">
          <div class="title pngFix">
            <h4>
              <?php if(!empty($_GET['stc_id'])){echo $output['stc_name'];}elseif(!empty($_GET['keyword'])){echo $lang['show_store_index_include'].$_GET['keyword'].$lang['show_store_index_goods'];}else{ echo $lang['nc_whole_goods']; }?>
            </h4>
          </div>
          <div class="ncs-goodslist-bar"><ul class="ncs-array">
            <li class='<?php echo $_GET['key'] == '1'?'selected':'';?>'><a <?php if($_GET['key'] == '1'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> href="<?php echo ($_GET['key'] == '1' && $_GET['order'] == '2') ? replaceParam(array('key' => '1', 'order'=>'1')) : replaceParam(array('key' => '1', 'order' => '2'));?>"><?php echo $lang['show_store_all_new'];?></a></li>
            <li class='<?php echo $_GET['key'] == '2'?'selected':'';?>'><a <?php if($_GET['key'] == '2'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> href="<?php echo ($_GET['key'] == '2' && $_GET['order'] == '2') ? replaceParam(array('key' => '2', 'order'=>'1')) : replaceParam(array('key' => '2', 'order' => '2'));?>"><?php echo $lang['show_store_all_price'];?></a></li>
            <li class='<?php echo $_GET['key'] == '3'?'selected':'';?>'><a <?php if($_GET['key'] == '3'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> href="<?php echo ($_GET['key'] == '3' && $_GET['order'] == '2') ? replaceParam(array('key' => '3', 'order'=>'1')) : replaceParam(array('key' => '3', 'order' => '2'));?>"><?php echo $lang['show_store_all_sale'];?></a></li>
            <li class='<?php echo $_GET['key'] == '4'?'selected':'';?>'><a <?php if($_GET['key'] == '4'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> href="<?php echo ($_GET['key'] == '4' && $_GET['order'] == '2') ? replaceParam(array('key' => '4', 'order'=>'1')) : replaceParam(array('key' => '4', 'order' => '2'));?>"><?php echo $lang['show_store_all_collect'];?></a></li>
            <li class='<?php echo $_GET['key'] == '5'?'selected':'';?>'><a <?php if($_GET['key'] == '5'){?>class="<?php echo $_GET['order'] == 1 ? 'asc' : 'desc';?>"<?php }?> href="<?php echo ($_GET['key'] == '5' && $_GET['order'] == '2') ? replaceParam(array('key' => '5', 'order'=>'1')) : replaceParam(array('key' => '5', 'order' => '2'));?>"><?php echo $lang['show_store_all_click'];?></a></li>
          </ul></div>
          <div class="content">
            <?php if(!empty($output['recommended_goods_list']) && is_array($output['recommended_goods_list'])){?>
            <ul>
              <?php foreach($output['recommended_goods_list'] as $value){?>
              <li>
                <dl>
                  <dt><a href="<?php echo urlShop('goods', 'index', array('goods_id'=>$value['goods_id']));?>" target="_blank"><?php echo $value['goods_name']?></a></dt>
                  <dd class="ncg-pic pngFix"><a href="<?php echo urlShop('goods', 'index',array('goods_id'=>$value['goods_id']));?>" target="_blank" class="thumb size160"><i></i><img src="<?php echo thumb($value, 240);?>" onload="javascript:DrawImage(this,160,160);" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a></dd>
                  <dd class="ncg-price"><em class="price"><?php echo $lang['currency'];?>
                      <?php if(intval($value['group_flag']) === 1) { ?>
                      <?php echo $value['group_price']?>
                      <?php } elseif(intval($value['xianshi_flag']) === 1) { ?>
                      <?php echo ncPriceFormat($value['goods_price'] * $value['xianshi_discount'] / 10);?>
                      <?php } else { ?>
                      <?php echo $value['goods_price']?>
                      <?php } ?>
                  </em></dd>
                  <dd class="ncg-sold"><?php echo $lang['nc_sell_out'];?><strong><?php echo $value['goods_salenum'];?></strong> <?php echo $lang['nc_jian'];?></dd>
                </dl>
              </li>
              <?php }?>
            </ul>
            
            <div class="pagination"><?php echo $output['show_page']; ?></div>
            <?php }else{?>
            <div class="nothing">
              <p><?php echo $lang['show_store_index_no_record'];?></p>
            </div>
            <?php }?>
            <div class="clear"></div>
          </div>
        </div>
      </article>
      <aside class="nc-sidebar">
        <?php include template('store/info');?>
        <?php include template('store/left');?>
      </aside>
    </section>
    <div class="clear"></div>
  </article>
