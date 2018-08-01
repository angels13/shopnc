<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store_class'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store_class&op=store_class"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a href="index.php?act=store_class&op=store_class_export" ><span><?php echo $lang['export'];?></span></a></li>
        <li><a href="index.php?act=store_class&op=store_class_import" ><span><?php echo $lang['import'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="store_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="sc_name"><?php echo $lang['store_class_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" name="sc_name" id="sc_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['higher_class'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><select name="sc_parent_id" id="sc_parent_id">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['parent_list']) && is_array($output['parent_list'])){ ?>
              <?php foreach($output['parent_list'] as $k => $v){ ?>
              <option <?php if($output['sc_parent_id'] == $v['sc_id']){ ?>selected='selected'<?php } ?> value="<?php echo $v['sc_id'];?>"><?php echo $v['sc_name'];?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td class="vatop tips"><?php echo $lang['store_class_notice'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="sc_sort"><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="255" name="sc_sort" id="sc_sort" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['update_sort'];?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#store_class_form").valid()){
     $("#store_class_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#store_class_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
            sc_name : {
                required : true,
                remote   : {                
                url :'index.php?act=store_class&op=ajax&branch=check_class_name',
                type:'get',
                data:{
                    sc_name : function(){
                        return $('#sc_name').val();
                    },
                    sc_parent_id : function() {
                        return $('#sc_parent_id').val();
                    },
                    sc_id : ''
                  }
                }
            },
            sc_sort : {
                number   : true
            }
        },
        messages : {
            sc_name : {
                required : '<?php echo $lang['store_class_name_no_null'];?>',
                remote   : '<?php echo $lang['store_class_name_is_there'];?>'
            },
            sc_sort  : {
                number   : '<?php echo $lang['store_class_sort_only_number'];?>'
            }
        }
    });
});
</script>