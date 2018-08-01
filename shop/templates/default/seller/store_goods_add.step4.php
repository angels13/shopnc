
<ul class="add-goods-step">
  <li><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>选择商品分类</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>填写商品详情</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li><i class="icon icon-camera-retro "></i>
    <h6>STIP.3</h6>
    <h2>上传商品图片</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li class="current"><i class="icon icon-ok-circle"></i>
    <h6>STIP.4</h6>
    <h2>商品发布成功</h2>
  </li>
</ul>
<div class="alert alert-block hr32">
  <h2><i class="icon-ok-circle mr10"></i><?php echo $lang['store_goods_step3_goods_release_success'];?>&nbsp;&nbsp;<?php if (C('goods_verify')) {?>等待管理员审核商品！<?php }?></h2>
  <div class="hr16"></div>
  <strong>
    <a class="ml30 mr30" href="<?php echo urlShop('goods', 'index', array('goods_id'=>$output['goods_id']));?>"><?php echo $lang['store_goods_step3_viewed_product'];?>&gt;&gt;</a>
    <a href="<?php echo urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => urlShop('store_goods_online', 'index')));?>"><?php echo $lang['store_goods_step3_edit_product'];?>&gt;&gt;</a>
  </strong>
  <div class="hr16"></div>
  <h4 class="ml10"><?php echo $lang['store_goods_step3_more_actions'];?></h4>
  <ul class="ml30">
    <li>1. <?php echo $lang['store_goods_step3_continue'];?> &quot; <a href="<?php echo urlShop('store_goods_add', 'index');?>"><?php echo $lang['store_goods_step3_release_new_goods'];?></a>&quot;</li>
    <li>2. <?php echo $lang['store_goods_step3_access'];?> &quot; <?php echo $lang['nc_user_center'];?>&quot; <?php echo $lang['store_goods_step3_manage'];?> &quot;<a href="<?php echo urlShop('store_goods_online', 'index');?>"><?php echo $lang['nc_member_path_goods_list'];?></a>&quot;</li>
    <!-- li>4. <?php echo $lang['store_goods_step3_choose_add'];?> &quot; <a href="index.php?act=store_groupbuy&op=groupbuy_add"><?php echo $lang['store_goods_step3_groupbuy_activity'];?></a> &quot;</li -->
    <li>3. <?php echo $lang['store_goods_step3_participation'];?> &quot; <a href="<?php echo urlShop('store_activity', 'store_activity');?>"><?php echo $lang['store_goods_step3_special_activities'];?></a> &quot;</li>
  </ul>
</div>
