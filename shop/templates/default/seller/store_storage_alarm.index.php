<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form method="post"  action="index.php?act=store_storage_alarm" id="my_store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt>库存警报<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <input class="text w60" name="store_storage_alarm" maxlength="10" type="text"  id="store_storage_alarm" value="<?php echo $output['store_storage_alarm'];?>" />
        <p class="hint">请填写0~255之间的数字。</p>
        <p class="hint">为0表示不警报。在出售中的商品列表页，如商品库存不足库存会显示为红字。</p>
      </dd>
    </dl>
    <div class="bottom">
        <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_common_button_save'];?>" /></label>
      </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(function(){
	$('#my_store_form').validate({
    	submitHandler:function(form){
    		ajaxpost('my_store_form', '', '', 'onerror')
    	},
		rules : {
		    store_storage_alarm: {
                required : true,
                digits : true,
                max : 255
			}
        },
        messages : {
            store_storage_alarm: {
				required : '请填写数字',
				digits : '请正确数字',
				max : '不能超过255'
			}
        }
    });    
    
});
</script> 
