<?php defined('InShopNC') or exit('Access Invalid!');?>
<script type="text/javascript">
var SHOP_SITE_URL = "<?php echo SHOP_SITE_URL; ?>";
var UPLOAD_SITE_URL = "<?php echo UPLOAD_SITE_URL; ?>";
</script>
<style type="text/css">
.evo-colorind-ie{
	position:relative; *top:0/*IE6,7*/ !important;
}
</style>


<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['web_config_index'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=web_config&op=web_config"><span><?php echo '板块区';?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo '焦点区';?></span></a></li>
        <li><a href="index.php?act=web_api&op=sale_edit"><span><?php echo '促销区';?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li><?php echo '焦点大图区可设置背景颜色，三张联动区一组三个图片。';?></li>
            <li><?php echo '所有相关设置完成，使用底部的“更新板块内容”前台展示页面才会变化。';?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <div class="homepage-focus" id="homepageFocusTab">
    <ul class="tab-menu">
      <li class="current" form="upload_screen_form"><?php echo '全屏(背景)焦点大图';?></li>
      <li form="upload_focus_form"><?php echo '三张联动焦点组图';?></li>
    </ul>
    <form id="upload_screen_form" class="tab-content" name="upload_screen_form" enctype="multipart/form-data" method="post" action="index.php?act=web_api&op=screen_pic" target="upload_pic">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="web_id" value="<?php echo $output['code_screen_list']['web_id'];?>">
      <input type="hidden" name="code_id" value="<?php echo $output['code_screen_list']['code_id'];?>">
      <div class="full-screen-slides">
        <ul>
          <?php if (is_array($output['code_screen_list']['code_info']) && !empty($output['code_screen_list']['code_info'])) { ?>
          <?php foreach ($output['code_screen_list']['code_info'] as $key => $val) { ?>
          <?php if (is_array($val) && !empty($val)) { ?>
          <li screen_id="<?php echo $val['pic_id'];?>" onclick="select_screen(<?php echo $val['pic_id'];?>);" title="可上下拖拽更改显示顺序">
            <div class="focus-thumb" style="background-color:<?php echo $val['color'];?>;" title="点击编辑选中区域内容"> <a class="del" href="JavaScript:del_screen(<?php echo $val['pic_id'];?>);" title="<?php echo $lang['nc_del'];?>">X</a> <img title="<?php echo $val['pic_name'];?>" src="<?php echo UPLOAD_SITE_URL.'/'.$val['pic_img'];?>"/></div>
            <input name="screen_list[<?php echo $val['pic_id'];?>][pic_id]" value="<?php echo $val['pic_id'];?>" type="hidden">
            <input name="screen_list[<?php echo $val['pic_id'];?>][pic_name]" value="<?php echo $val['pic_name'];?>" type="hidden">
            <input name="screen_list[<?php echo $val['pic_id'];?>][pic_url]" value="<?php echo $val['pic_url'];?>" type="hidden">
            <input name="screen_list[<?php echo $val['pic_id'];?>][color]" value="<?php echo $val['color'];?>" type="hidden">
            <input name="screen_list[<?php echo $val['pic_id'];?>][pic_img]" value="<?php echo $val['pic_img'];?>" type="hidden">
          </li>
          <?php } ?>
          <?php } ?>
          <?php } ?>
        </ul>
        <div class="add-focus"><a class="btn-add-nofloat" href="JavaScript:add_screen();"><?php echo '新增图片';?></a> <span class="s-tips"><i></i><?php echo '小提示：单击图片选中修改，拖动可以排序，添加最多不超过5个，保存后生效。';?></span></div>
      </div>
      <table id="upload_screen" class="table tb-type2" style="display:none;">
        <tbody>
          <tr>
            <td colspan="2" class="required"><?php echo '文字标题'.$lang['nc_colon'];?></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><input type="hidden" name="screen_id" value="">
              <input class="txt" type="text" name="screen_pic[pic_name]" value=""></td>
            <td class="vatop tips">图片标题文字将作为图片Alt形式显示。</td>
          </tr>
          <tr>
            <td colspan="2" class="required"><label><?php echo $lang['web_config_upload_url'].$lang['nc_colon'];?></label></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><input name="screen_pic[pic_url]" value="" class="txt" type="text"></td>
            <td class="vatop tips">输入图片要跳转的URL地址，正确格式应以"http://"开头，点击后将以"_blank"形式另打开页面。</td>
          </tr>
          <tr>
            <td colspan="2" class="required"><?php echo $lang['web_config_upload_adv_pic'].$lang['nc_colon'];?></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><span class="type-file-box">
              <input type='text' name='textfield' id='textfield1' class='type-file-text' />
              <input type='button' name='button' id='button1' value='' class='type-file-button' />
              <input name="pic" id="pic" type="file" class="type-file-file" size="30">
              </span></td>
            <td class="vatop tips">为确保显示效果正确，请选择最小不低于W:776px H:300px、最大不超过W:1920px H:481px的清晰图片作为全屏焦点图。</td>
          </tr>
          <tr>
          
            <td colspan="2" class="required"><label><?php echo '背景颜色'.$lang['nc_colon'];?></label></td>
          </tr>
          <tr class="noborder">
            <td class="vatop"><input id="screen_color" name="screen_pic[color]" value="" class="" type="text"></td>
            <td class="vatop tips">为确保显示效果美观，可设置首页全屏焦点图区域整体背景填充色用于弥补图片在不同分辨率下显示区域超出图片时的问题，可根据您焦点图片的基础底色作为参照进行颜色设置。</td>
          </tr>
          
        </tbody>
      </table>
      <div class="margintop"><a href="JavaScript:void(0);" onclick="$('#upload_screen_form').submit();" class="btn"><span><?php echo $lang['web_config_save'];?></span></a> 
        <a href="index.php?act=web_api&op=html_update&web_id=<?php echo $output['code_screen_list']['web_id'];?>" class="btn"><span><?php echo $lang['web_config_web_html'];?></span></a></div>
    </form>
    <form id="upload_focus_form" class="tab-content" name="upload_screen_form" enctype="multipart/form-data" method="post" action="index.php?act=web_api&op=focus_pic" target="upload_pic" style="display:none;">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="web_id" value="<?php echo $output['code_focus_list']['web_id'];?>">
      <input type="hidden" name="code_id" value="<?php echo $output['code_focus_list']['code_id'];?>">
      <div class="focus-trigeminy">
        <?php if (is_array($output['code_focus_list']['code_info']) && !empty($output['code_focus_list']['code_info'])) { ?>
        <?php foreach ($output['code_focus_list']['code_info'] as $key => $val) { ?>
        <div focus_id="<?php echo $key;?>" class="focus-trigeminy-group" title="<?php echo '可上下拖拽更改图片组显示顺序';?>"><a class="del" href="JavaScript:del_focus(<?php echo $key;?>);" title="<?php echo $lang['nc_del'];?>">X</a>
          <ul>
            <?php if (!empty($val['pic_list']) && is_array($val['pic_list'])) { ?>
            <?php foreach($val['pic_list'] as $k => $v) { ?>
            <li pic_id="<?php echo $k;?>" onclick="select_focus(<?php echo $key;?>,this);" title="<?php echo '可左右拖拽更改图片排列顺序';?>"><div class="focus-thumb"><img title="<?php echo $v['pic_name'];?>" src="<?php echo UPLOAD_SITE_URL.'/'.$v['pic_img'];?>"/></div>
              <input name="focus_list[<?php echo $key;?>][pic_list][<?php echo $v['pic_id'];?>][pic_id]" value="<?php echo $v['pic_id'];?>" type="hidden">
              <input name="focus_list[<?php echo $key;?>][pic_list][<?php echo $v['pic_id'];?>][pic_name]" value="<?php echo $v['pic_name'];?>" type="hidden">
              <input name="focus_list[<?php echo $key;?>][pic_list][<?php echo $v['pic_id'];?>][pic_url]" value="<?php echo $v['pic_url'];?>" type="hidden">
              <input name="focus_list[<?php echo $key;?>][pic_list][<?php echo $v['pic_id'];?>][pic_img]" value="<?php echo $v['pic_img'];?>" type="hidden">
            </li>
            <?php } ?>
            <?php } ?>
          </ul>
        </div>
        <?php } ?>
        <?php } ?>
        <div class="add-tab" id="btn_add_list"> <a class="btn-add-nofloat" href="JavaScript:add_focus();"><?php echo '新增广告组';?></a> <span class="s-tips"><i></i>小提示：可添加每组3张，最多5组联动广告图，单击图片为单张编辑，拖动排序，保存生效。</span></div>
      </div>
      <table id="upload_focus" class="table tb-type2" style="display:none;">
        <tbody>
          <tr>
            <td colspan="2" class="required"><?php echo '文字标题'.$lang['nc_colon'];?></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><input type="hidden" name="slide_id" value="">
              <input type="hidden" name="pic_id" value="">
              <input class="txt" type="text" name="focus_pic[pic_name]" value=""></td>
            <td class="vatop tips">图片标题文字将作为图片Alt形式显示。</td>
          </tr>
          <tr>
            <td colspan="2" class="required"><label><?php echo $lang['web_config_upload_url'].$lang['nc_colon'];?></label></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><input name="focus_pic[pic_url]" value="" class="txt" type="text"></td>
            <td class="vatop tips">输入图片要跳转的URL地址，正确格式应以"http://"开头，点击后将以"_blank"形式另打开页面。</td>
          </tr>
          <tr>
            <td colspan="2" class="required"><?php echo $lang['web_config_upload_adv_pic'].$lang['nc_colon'];?></td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform"><span class="type-file-box">
              <input type='text' name='textfield' id='textfield1' class='type-file-text' />
              <input type='button' name='button' id='button1' value='' class='type-file-button' />
              <input name="pic" id="pic" type="file" class="type-file-file" size="30">
              </span></td>
            <td class="vatop tips">为确保显示效果正确，请选择W:259px H:180px的清晰图片作为联动广告图组单图。</td>
          </tr>
        </tbody>
      </table>
      <a href="JavaScript:void(0);" onclick="$('#upload_focus_form').submit();" class="btn"><span><?php echo $lang['web_config_save'];?></span></a> 
      <a href="index.php?act=web_api&op=html_update&web_id=<?php echo $output['code_screen_list']['web_id'];?>" class="btn"><span><?php echo $lang['web_config_web_html'];?></span></a>
    </form>
  </div>
</div>
<iframe style="display:none;" src="" name="upload_pic"></iframe>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/colorpicker/evol.colorpicker.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/colorpicker/evol.colorpicker.min.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/web_config/web_focus.js"></script>