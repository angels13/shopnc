<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form id="add_form" action="<?php echo urlShop('store_account_group', 'group_save');?>" method="post">
    <?php if(!empty($output['group_info'])) { ?>
    <input name="group_id" type="hidden" value="<?php echo $output['group_info']['group_id'];?>" />
    <?php } ?>
    <dl>
      <dt><i class="required">*</i>组名称<?php echo $lang['nc_colon'];?></dt>
      <dd><input class="w120 text" name="seller_group_name" type="text" id="seller_group_name" value="<?php if(!empty($output['group_info'])) {echo $output['group_info']['group_name'];};?>" />
          <span></span>
        <p class="hint"></p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>权限<?php echo $lang['nc_colon'];?></dt>
      <dd><input id="btn_select_all" type="checkbox" />
          <label for="btn_select_all">全选</label>
        <?php if(!empty($output['menu']) && is_array($output['menu'])) {?>
        <?php foreach($output['menu'] as $key => $value) {?>
        <p>
          <input id="<?php echo $key;?>" nctype="btn_select_module" type="checkbox" />
          <label for="<?php echo $key;?>"><b><?php echo $value['name'];?></b></label>
          <?php $submenu = $value['child'];?>
          <?php if(!empty($submenu) && is_array($submenu)) {?>
          <?php foreach($submenu as $submenu_value) {?>
          <input id="<?php echo $submenu_value['act'];?>" name="limits[]" value="<?php echo $submenu_value['act'];?>" <?php if(!empty($output['group_limits'])) {if(in_array($submenu_value['act'], $output['group_limits'])) { echo 'checked'; }}?> type="checkbox" />
          <label for="<?php echo $submenu_value['act'];?>"><?php echo $submenu_value['name'];?></label>
          <?php } ?>
          <?php } ?>
        </p>
        <?php } ?>
        <?php } ?>
        <p class="hint"></p>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border">
        <input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>">
      </label>
    </div>
  </form>
</div>
<script>
$(document).ready(function(){
    $('#btn_select_all').on('click', function() {
        if($(this).prop('checked')) {
            $(this).parents('dd').find('input:checkbox').prop('checked', true);
        } else {
            $(this).parents('dd').find('input:checkbox').prop('checked', false);
        }
    });
    $('[nctype="btn_select_module"]').on('click', function() {
        if($(this).prop('checked')) {
            $(this).parents('p').find('input:checkbox').prop('checked', true);
        } else {
            $(this).parents('p').find('input:checkbox').prop('checked', false);
        }
    });
    $('#add_form').validate({
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
    	submitHandler:function(form){
    		ajaxpost('add_form', '', '', 'onerror');
    	},
        rules : {
            seller_group_name: {
                required: true,
                maxlength: 50 
            }
        },
        messages: {
            seller_group_name: {
                required: '<i class="icon-exclamation-sign"></i>组名称不能为空',
                maxlength: '<i class="icon-exclamation-sign"></i>组名最多50个字' 
            }
        }
    });

    // 商品相关权限关联选择
    $('#store_goods_add,#store_goods_online,#store_goods_offline').on('click', function() {
        if($(this).prop('checked')) {
            store_goods_select(true);
        } else {
            store_goods_select(false);
        }
    });

    function store_goods_select(state) {
        $('#store_goods_add').prop('checked', state);
        $('#store_goods_online').prop('checked', state);
        $('#store_goods_offline').prop('checked', state);
    }
});
</script> 
