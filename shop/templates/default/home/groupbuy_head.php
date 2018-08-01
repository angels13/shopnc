<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_group.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
//鼠标触及更替li样式
$(document).ready(function(){
    $("#list").hide();
    $("#button_show").click(function(){
        $("#list").toggle();
    });
    $("#button_close").click(function(){
        $("#list").hide();
    });
    var area = $.cookie('<?php echo COOKIE_PRE;?>groupbuy_area');
   if(area == null) {
        $("#show_area_name").html("<?php echo $lang['text_country'];?>");
        $("#groupbuy_area").val('');
    }
   else {
        area_array = area.split(",");
        $("#show_area_name").html(area_array[1]);
        $("#groupbuy_area").val(area);
    }
});

//团购地区筛选
function set_groupbuy_area(area) {
    if(area == '') {
        $("#groupbuy_area").val('');
        $("#show_area_name").html("<?php echo $lang['text_country'];?>");
        $.cookie('<?php echo COOKIE_PRE;?>groupbuy_area',null);
    }
    else {
        area_array = area.split(",");
        $("#groupbuy_area").val(area_array[0]);
        $("#show_area_name").html(area_array[1]);
        $.cookie('<?php echo COOKIE_PRE;?>groupbuy_area',area);
    }
    $('#list').hide();
}
</script>
<div id="headRelative" class="ncg-header">
  <div class="title">
    <h1><?php echo $lang['text_groupbuy'];?></h1>
    <div class="city">[<a href="javascript:void(0)" id="button_show">
      <h2 id="show_area_name">&nbsp;</h2>
      <?php if ($_GET['op'] != 'groupbuy_detail') {?><i class="arrow"></i><?php }?> </a>]</div>
  </div>
  <?php if ($_GET['op'] != 'groupbuy_detail') {?>
  <div id="list" class="list" style="display:none;"><a id="button_close" class="close" href="javascript:void(0)">X</a>
    <ul>
      <li><a href="<?php echo dropParam(array('area_id'));?>" onClick="set_groupbuy_area('')" ><?php echo $lang['text_country'];?></a></li>
      <?php if(is_array($output['area_list'])) { ?>
      <?php foreach($output['area_list'] as $groupbuy_area) { ?>
      <?php if(intval($groupbuy_area['deep']) === 0) { ?>
      <li><a href="<?php echo replaceParam(array('area_id' => $groupbuy_area['area_id']));?>" onClick="set_groupbuy_area('<?php echo $groupbuy_area['area_id'].','.$groupbuy_area['area_name'];?>')" ><?php echo $groupbuy_area['area_name'];?></a></li>
      <?php } ?>
      <?php } ?>
      <?php } ?>
    </ul>
  </div>
  <?php }?>
</div>
