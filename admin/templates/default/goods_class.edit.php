<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['goods_class_index_class'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=goods_class&op=goods_class"><span><?php echo $lang['nc_manage'];?></span></a></li>
        <li><a href="index.php?act=goods_class&op=goods_class_add"><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th class="nobg" colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
        <ul>
            <li><?php echo $lang['goods_class_edit_prompts_one'];?></li>
            <li><?php echo $lang['goods_class_edit_prompts_two'];?></li>
            <li><?php echo $lang['goods_class_edit_prompts_three'];?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form id="goods_class_form" name="goodsClassForm" enctype="multipart/form-data" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="gc_id" value="<?php echo $output['class_array']['gc_id'];?>" />
    <input type="hidden" name="gc_parent_id" id="gc_parent_id" value="<?php echo $output['class_array']['gc_parent_id'];?>" />
    <input type="hidden" name="old_type_id" value="<?php echo $output['class_array']['type_id'];?>">
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="gc_name validation" for="gc_name"><?php echo $lang['goods_class_index_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" maxlength="20" value="<?php echo $output['class_array']['gc_name'];?>" name="gc_name" id="gc_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <?php if ($output['class_array']['gc_parent_id'] == 0) {?>
        <tr>
          <td colspan="2"><label for="pic"><?php echo '分类图片';?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><span class="type-file-show"><img class="show_image" src="<?php echo ADMIN_TEMPLATES_URL;?>/images/preview.png">
            <div class="type-file-preview"><img src="<?php echo $output['class_array']['pic'];?>"></div>
            </span><span class="type-file-box"><input type='text' name='textfield' id='textfield1' class='type-file-text' />
                <input type='button' name='button' id='button1' value='' class='type-file-button' />
            <input name="pic" type="file" class="type-file-file" id="pic" size="30" hidefocus="true" nc_type="change_pic">
            </span></td>
          <td class="vatop tips"><?php echo '只有第一级分类可以上传图片，建议用16px * 16px，超出后自动隐藏';?></td>
        </tr>
        <?php } ?>   
        <tr>
          <td colspan="2" class="required" id="gcategory">
            <label for="gc_name"><?php echo $lang['goods_class_add_type'];?>:</label>
            <select class="class-select">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['gc_list'])){ ?>
              <?php foreach($output['gc_list'] as $k => $v){ ?>
              <?php if ($v['gc_parent_id'] == 0) {?>
              <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </select>
            <?php echo $lang['nc_quickly_targeted'];?>
          </td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          <input type="hidden" name="t_name" id="t_name" value="<?php echo $output['class_array']['type_name'];?>" />
          <input type="hidden" name="t_sign" id="t_sign" value="" />
          <div style="position:relative; max-height:240px; overflow: hidden;" id="type_div"><div>
          	<dl style="margin:10px 0;">
          	<dd style="display:inline-block; margin-right:10px;">
          	  <input type="radio" name="t_id" value="0" <?php if($output['class_array']['type_id'] == 0){?>checked="checked"<?php }?> />
              <span><?php echo $lang['goods_class_null_type'];?></span>
          	</dd>
          	</dl>
            <?php if(!empty($output['type_list'])){?>
            <?php foreach($output['type_list'] as $k=>$val){?>
            <?php if(!empty($val['type'])){?>
            <dl style="margin:10px 0;"><dt id="type_dt_<?php echo $k;?>" style="font-weight:600;"><?php echo $val['name']?></dt>
            <?php foreach($val['type'] as $v){?>
            <dd style="display:inline-block; margin-right:10px;">
              <input type="radio" name="t_id" value="<?php echo $v['type_id']?>" <?php if($output['class_array']['type_id'] == $v['type_id']){?>checked="checked"<?php }?> />
              <span><?php echo $v['type_name'];?></span>
            </dd>
            <?php }?>
            </dl>
            <?php }?>
            <?php }?>
            <?php }?>
          </div></div>
          <div class=" mtm"><input type="checkbox" name="t_associated" value="1" checked="checked" id="t_associated" /><label for="t_associated"><?php echo $lang['goods_class_edit_related_to_subclass'];?></label></div>
          </td>
          <td class="vatop tips"><?php echo $lang['goods_class_add_type_desc_one'];?><a onclick="window.parent.openItem('type,type,goods')" href="JavaScript:void(0);"><?php echo $lang['nc_type_manage'];?></a><?php echo $lang['goods_class_add_type_desc_two'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="gc_sort"><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['class_array']['gc_sort'] == ''?0:$output['class_array']['gc_sort'];?>" name="gc_sort" id="gc_sort" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['goods_class_add_update_sort'];?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot"><td colspan="15" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script>
$(document).ready(function(){
    $('#type_div').perfectScrollbar();
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#goods_class_form").valid()){
	     $("#goods_class_form").submit();
		}
	});

	$("#pic").change(function(){
		$("#textfield1").val($(this).val());
	});
	$('input[type="radio"][name="t_id"]').change(function(){
		// 标记类型时候修改 修改为ok
		var t_id = <?php echo $output['class_array']['type_id'];?>;
		if(t_id != $(this).val()){
			$('#t_sign').val('ok');
		}else{
			$('#t_sign').val('');
		}
			
		if($(this).val() == '0'){
			$('#t_name').val('');
		}else{
			$('#t_name').val($(this).next('span').html());
		}
	});
	
	$('#goods_class_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            gc_name : {
                required : true,
                remote   : {                
                url :'index.php?act=goods_class&op=ajax&branch=check_class_name',
                type:'get',
                data:{
                    gc_name : function(){
                        return $('#gc_name').val();
                    },
                    gc_parent_id : function() {
                        return $('#gc_parent_id').val();
                    },
                    gc_id : '<?php echo $output['class_array']['gc_id'];?>'
                  }
                }
            },
            gc_sort : {
                number   : true
            }
        },
        messages : {
             gc_name : {
                required : '<?php echo $lang['goods_class_add_name_null'];?>',
                remote   : '<?php echo $lang['goods_class_add_name_exists'];?>'
            },
            gc_sort  : {
                number   : '<?php echo $lang['goods_class_add_sort_int'];?>'
            }
        }
    });

    // 类型搜索
    $("#gcategory > select").live('change',function(){
    	type_scroll($(this));
    });
});
var typeScroll = 0;
function type_scroll(o){
	var id = o.val();
	if(!$('#type_dt_'+id).is('dt')){
		return false;
	}
	$('#type_div').scrollTop(-typeScroll);
	var sp_top = $('#type_dt_'+id).offset().top;
	var div_top = $('#type_div').offset().top;
	$('#type_div').scrollTop(sp_top-div_top);
	typeScroll = sp_top-div_top;
}
gcategoryInit('gcategory');
</script>