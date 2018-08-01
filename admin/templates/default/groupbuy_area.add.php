<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['groupbuy_index_manage'];?></h3>
      <ul class="tab-base">
          <?php   foreach($output['menu'] as $menu) {  if($menu['menu_type'] == 'text') { ?>
          <li><a href="JavaScript:void(0);" class="current"><span><?php echo $menu['menu_name'];?></span></a></li>
          <?php }  else { ?>
          <li><a href="<?php echo $menu['menu_url'];?>" ><span><?php echo $menu['menu_name'];?></span></a></li>
          <?php  } }  ?>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="index.php?act=groupbuy&op=area_save">
      <input name="area_id" type="hidden" value="<?php echo $output['area_info']['area_id'];?>" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label for="input_area_name" class="validation"><?php echo $lang['groupbuy_area_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['area_info']['area_name'];?>" name="input_area_name" id="input_area_name" class="txt"></td>
          <td class="vatop tips"><?php echo $lang[''];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="input_parent_id"><?php echo $lang['groupbuy_parent_area'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><select name="input_parent_id" id="input_parent_id">
              <option value="0"><?php echo $lang['groupbuy_root_area'];?></option>
              <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
              <?php foreach($output['list'] as $val){ ?>
              <option <?php if($output['parent_id'] == $val['area_id']){ ?>selected='selected'<?php } ?> value="<?php echo $val['area_id'];?>"><?php echo str_repeat('&nbsp;&nbsp;',intval($val['deep'])).$val['area_name'];?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td class="vatop tips"><?php echo $lang['groupbuy_parent_area_add_tip'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="<?php echo empty($output['area_info'])?'0':$output['area_info']['area_sort'];?>" name="input_area_sort" id="input_area_sort" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['sort_tip'];?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2"><a id="submit" href="JavaScript:void(0);" class="btn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#submit").click(function(){
        $("#add_form").submit();
    });
	$('#add_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            input_area_name : {
                required : true
            },
            input_area_sort : {
                number   : true
            }
        },
        messages : {
            input_area_name: {
                required : '<?php echo $lang['area_name_error'];?>'
            },
            input_area_sort: {
                number   : '<?php echo $lang['sort_error'];?>'
            }
        }
    });
});
</script>
