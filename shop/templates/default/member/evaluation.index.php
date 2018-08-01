<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="wrap">
<div class="tabmenu">
  <ul id="listpj" class="tab">
    <li class="active"><a href="index.php?act=<?php echo $output['pj_act'];?>&op=list"><?php echo $lang['member_evaluation_toother'];?></a></li>
  </ul>
</div>
<form id="goodsevalform" method="get">
  <input type="hidden" name="act" value="<?php echo $output['pj_act'];?>"/>
  <input type="hidden" name="op" value="list"/>
  <input type="hidden" name="type" value="<?php echo $_GET['type'];?>"/>
  <table class="ncu-table-style order">
    <thead>
      <tr>
          <th class="w10"></th>
          <th>商品评价/商家回复</th>
          <th class="w10"></th>
      </tr>
    </thead>
    <tbody>
        <?php if (is_array($output['goodsevallist']) && !empty($output['goodsevallist'])) { ?>
        <?php foreach ((array)$output['goodsevallist'] as $k=>$v){?><tr>
<td class="sep-row" colspan="19"></td>
</tr>
<tr>
    <th colspan="20">
        <span class="ml10"><?php echo $lang['member_evaluation_goodsinfo_title'];?>
            <a target="_blank" href="<?php echo urlShop('goods', 'index', array('goods_id'=>$v['geval_goodsid'])); ?>"><?php echo $v['geval_goodsname']?></a>
        </span>
        <div class="raty" style="display:inline-block;" data-score="<?php echo $v['geval_scores'];?>"></div>
        <?php if(empty($v['geval_image'])) {?>
        <a href="<?php echo urlShop('member_evaluate', 'add_image', array('geval_id' => $v['geval_id']));?>">晒单</a>
        <?php } ?>
    </th>
</tr>
<tr>
    <td class="bdl"></td>
        <td class="tl">我的评价：<?php echo $v['geval_content'];?>
            <p class="date">[<?php echo @date('Y-m-d H:i:s',$v['geval_addtime']);?>]</p>
        </td><td class="bdr"></td>
    </tr>
    <?php if (!empty($v['geval_explain'])){?>
    <tr>
        <td class="bdl"></td>
        <td class="tl">
            <p style="color:#996600;padding:5px 0px;">[<?php echo $lang['member_evaluation_explain'];?>]<?php echo $v['geval_explain'];?></p>
        </td>
        <td class="bdr"></td>
    </tr>
    <?php }?>
    <?php if (!empty($v['geval_image'])){?>
    <tr>
        <td class="bdl"></td>
        <td class="tl">
            <ul class="evaluation-pic-list">
            <?php $image_array = explode(',', $v['geval_image']);?>
            <?php foreach ($image_array as $value) { ?>
            <li>
            <a nctype="nyroModal"  href="<?php echo snsThumb($value, 1024);?>">
                <img src="<?php echo snsThumb($value);?>">
            </a>
            </li>
            <?php } ?>
        </ul>
        </td>
        <td class="bdr"></td>
    </tr>
    <?php }?>
    <?php }?>
        <?php } else { ?>
        <tr>
            <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
        </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
    </tfoot>
  </table>
</form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/jquery.raty.min.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
    $('.raty').raty({
        path: "<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/img",
        readOnly: true,
        score: function() {
          return $(this).attr('data-score');
        }
    });

   $('a[nctype="nyroModal"]').nyroModal();
});
</script>


