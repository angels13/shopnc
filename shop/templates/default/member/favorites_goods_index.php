<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <table class="ncu-table-style shoplist">
    <thead>
      <tr nc_type="table_header">
        <th class="w30"></th>
        <th class="w70"></th>
        <th class="tl"><?php echo $lang['favorite_product_name'];?></th>
        <th class="w20"></th>
        <th class="tl w150"><?php echo $lang['favorite_store_info'];?></th>
        <th class="w80"><?php echo $lang['favorite_product_price'];?></th>
        <th class="w120"><?php echo $lang['favorite_date'];?></th>
        <th class="w60"><?php echo $lang['favorite_popularity'];?></th>
        <th class="w90"><?php echo $lang['favorite_handle'];?></th>
      </tr>
      <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){?>
      <tr>
        <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
        <td colspan="20"><label for="all"><?php echo $lang['nc_select_all'];?></label>
          <a href="javascript:void(0);" class="ncu-btn1" uri="index.php?act=member_favorites&op=delfavorites&type=goods" name="fav_id" confirm="<?php echo $lang['nc_ensure_del'];?>" nc_type="batchbutton"><span><?php echo $lang['nc_del'];?></span></a>
          <div class="model-switch-btn"><span><?php echo $lang['favorite_view_mode'].$lang['nc_colon'] ;?></span> <a href="index.php?act=member_favorites&op=fglist&show=list" class="onlist" title="<?php echo $lang['favorite_view_mode_list'];?>"><?php echo $lang['favorite_view_mode_list'];?></a><a href="index.php?act=member_favorites&op=fglist&show=pic" class="pic" title="<?php echo $lang['favorite_view_mode_pic'];?>"><?php echo $lang['favorite_view_mode_pic'];?></a><a href="index.php?act=member_favorites&op=fglist&show=store" class="store" title="<?php echo $lang['favorite_view_mode_shop'];?>"><?php echo $lang['favorite_view_mode_shop'];?></a></div></td>
      </tr>
      <?php }?>
    </thead>
    <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){ ?>
    <tbody>
      <?php foreach($output['favorites_list'] as $key=>$favorites){?>
      <tr class="bd-line">
        <td style="vertical-align: middle;"><input type="checkbox" class="checkitem" value="<?php echo $favorites['goods']['goods_id'];?>"/></td>
        <td><div class="goods-pic-small"><span class="thumb size60"><i></i><a href="index.php?act=goods&goods_id=<?php echo $favorites['goods']['goods_id'];?>" target="_blank"><img src="<?php echo thumb($favorites['goods'], 60);?>" onload="javascript:DrawImage(this,60,60);" /></a></span></div>
          <div class="sns-share"><i></i><a href="javascript:void(0)" nc_type="sharegoods" data-param='{"gid":"<?php echo $favorites['goods']['goods_id'];?>"}' title="<?php echo $lang['favorite_snsshare_goods'];?>"><?php echo $lang['favorite_snsshare_goods'];?></a></div></td>
        <td><dl class="goods-name">
            <dt><a href="index.php?act=goods&goods_id=<?php echo $favorites['goods']['goods_id'];?>" target="_blank"><?php echo $favorites['goods']['goods_name'];?></a></dt>
            <dd class="share-sale"><span><?php echo $lang['favorite_selled'].$lang['nc_colon'] ;?><em><?php echo $favorites['goods']['goods_salenum'];?></em><?php echo $lang['piece'];?></span><span>(<em><?php echo $favorites['goods']['evaluation_count'];?></em><?php echo $lang['favorite_number_of_consult'] ;?>)</span></dd>
          </dl></td>
        <td></td>
        <td class="tl"><p> <a href="<?php echo urlShop('show_store','index',array('store_id'=> $favorites['goods']['store_id']), $favorites['goods']['store_domain']);?>" target="_blank"><?php echo $favorites['goods']['store_name'];?></a>
            <?php if(!empty($output['store_favorites']) && in_array($favorites['goods']['store_id'],$output['store_favorites'])){ ?>
            <span class="goods-favorite" title="<?php echo $lang['favorite_collected_store'];?>"><i class="have">&nbsp;</i></span>
            <?php }else{?>
            <a href="javascript:collect_store('<?php echo $favorites['goods']['store_id'];?>','store','')" class="goods-favorite" title="<?php echo $lang['favorite_collect_store'];?>" nc_store="<?php echo $favorites['goods']['store_id'];?>"> <i class="add">&nbsp;</i></a>
            <?php }?>
          </p>
          <p class="mt5"><?php echo $lang['favorite_message'].$lang['nc_colon'];?><span member_id="<?php echo $favorites['goods']['member_id'];?>"></span>
            <?php if(!empty($favorites['goods']['store_qq'])){?>
            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $favorites['goods']['store_qq'];?>&site=qq&menu=yes" title="QQ: <?php echo $favorites['goods']['store_qq'];?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $favorites['goods']['store_qq'];?>:52" style=" vertical-align: middle;"/></a>
            <?php }?>
            <?php if(!empty($favorites['goods']['store_ww'])){?>
            <a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=<?php echo $favorites['goods']['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?php echo $favorites['goods']['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" alt="Wang Wang"  style=" vertical-align: middle;"/></a>
            <?php }?>
          </p></td>
        <td class="goods-price"><strong><?php echo ncPriceFormat($favorites['goods']['goods_price']);?></strong></td>
        <td class="goods-time"><?php echo date("Y-m-d",$favorites['fav_time']);?></td>
        <td><?php echo $favorites['goods']['goods_collect'];?></td>
        <td><p><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', 'index.php?act=member_favorites&op=delfavorites&type=goods&fav_id=<?php echo $favorites['fav_id'];?>');" class="ncu-btn2"><?php echo $lang['nc_del_&nbsp'];?></a></p></td>
      </tr>
      <?php }?>
    </tbody>
    <?php }else{?>
    <tbody>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?><span></td>
      </tr>
    </tbody>
    <?php }?>
    <?php if(!empty($output['favorites_list']) && is_array($output['favorites_list'])){?>
    <tfoot>
      <tr>
        <td><input type="checkbox" id="all2" class="checkall"/></td>
        <td colspan="20"><label for="all2"><?php echo $lang['nc_select_all'];?></label>
          <a href="javascript:void(0);" class="ncu-btn1" uri="index.php?act=member_favorites&op=delfavorites&type=goods" name="fav_id" confirm="<?php echo $lang['nc_ensure_del'];?>" nc_type="batchbutton"><span><?php echo $lang['nc_del'];?></span></a>
          <div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
    </tfoot>
    <?php }?>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" charset="utf-8"></script>
