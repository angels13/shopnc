<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <table class="ncu-table-style">
    <thead>
      <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){?>
      <tr nc_type="table_header">
        <th colspan="8"> </th>
      </tr>
      <tr>
        <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
        <td colspan="1"><label for="all"><?php echo $lang['nc_select_all'];?></label>
          <a href="javascript:void(0);" class="ncu-btn1" uri="index.php?act=member_favorites&op=delfavorites&type=goods" name="fav_id" confirm="<?php echo $lang['nc_ensure_del'];?>" nc_type="batchbutton"><span><?php echo $lang['nc_del'];?></span></a>
          <div class="model-switch-btn"><span><?php echo $lang['favorite_view_mode'].$lang['nc_colon'] ;?></span> <a href="index.php?act=member_favorites&op=fglist&show=list" class="list" title="<?php echo $lang['favorite_view_mode_list'];?>"><?php echo $lang['favorite_view_mode_list'];?></a><a href="index.php?act=member_favorites&op=fglist&show=pic" class="onpic" title="<?php echo $lang['favorite_view_mode_pic'];?>"><?php echo $lang['favorite_view_mode_pic'];?></a><a href="index.php?act=member_favorites&op=fglist&show=store" class="store" title="<?php echo $lang['favorite_view_mode_shop'];?>"><?php echo $lang['favorite_view_mode_shop'];?></a></div></td>
      </tr>
      <?php }?>
    </thead>
    <tbody>
      <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){ ?>
      <tr>
        <td colspan="20" class="pic-model"><ul>
            <?php foreach($output['favorites_list'] as $key=>$favorites){?>
            <li>
              <dl>
                <dt>
                  <input type="checkbox" class="checkitem" value="<?php echo $favorites['goods']['goods_id'];?>"/>
                  <a href="index.php?act=goods&goods_id=<?php echo $favorites['goods']['goods_id'];?>" target="_blank"><?php echo $favorites['goods']['goods_name'];?></a></dt>
                <dd class="picture">
                  <div class="thumb size160"><i></i><a href="index.php?act=goods&goods_id=<?php echo $favorites['goods']['goods_id'];?>" target="_blank"><img src="<?php echo thumb($favorites['goods'], 240);?>" onload="javascript:DrawImage(this,160,160);" /></a></div>
                  <div class="handle"><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', 'index.php?act=member_favorites&op=delfavorites&type=goods&fav_id=<?php echo $favorites['fav_id'];?>');" title="<?php echo $lang['nc_del'];?>"><?php echo $lang['nc_del'];?></a></div>
                </dd>
                <dd><?php echo $lang['favorite_product_price'].$lang['nc_colon'];?><em class="goods-price orange"><?php echo ncPriceFormat($favorites['goods']['goods_price']);?></em> <?php echo $lang['currency_zh'];?></dd>
                <dd class="share-sale"><span><?php echo $lang['favorite_selled'].$lang['nc_colon'] ;?><em><?php echo $favorites['goods']['goods_salenum'];?></em><?php echo $lang['piece'];?></span><span>(<em><?php echo $favorites['goods']['evaluation_count'];?></em><?php echo $lang['favorite_number_of_consult'] ;?>)</span></dd>
                <dd><span class="fl"><?php echo $lang['favorite_popularity'].$lang['nc_colon'];?><?php echo $favorites['goods']['goods_collect'];?></span><a href="javascript:void(0)"  nc_type="sharegoods" data-param='{"gid":"<?php echo $favorites['goods']['goods_id'];?>"}' class="sns-share" title="<?php echo $lang['favorite_snsshare_goods'];?>"><?php echo $lang['nc_snsshare'];?></a></dd>
              </dl>
            </li>
            <?php }?>
          </ul></td>
      </tr>
      <?php }else{?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?><span></td>
      </tr>
      <?php }?>
    </tbody>
    <tfoot>
      <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){?>
      <tr>
        <td><input type="checkbox" id="all2" class="checkall"/></td>
        <td colspan="1"><label for="all2"><?php echo $lang['nc_select_all'];?></label>
          <a href="javascript:void(0);" class="ncu-btn1" uri="index.php?act=member_favorites&op=delfavorites&type=goods&show=<?php echo $_GET['show'];?>" name="fav_id" confirm="<?php echo $lang['nc_ensure_del'];?>" nc_type="batchbutton"><span><?php echo $lang['nc_del'];?></span></a>
          <div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php }?>
    </tfoot>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" charset="utf-8"></script>
