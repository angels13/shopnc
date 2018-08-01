<?php defined('InShopNC') or exit('Access Invalid!');?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_pointprod'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=pointprod&op=pointprod" ><span><?php echo $lang['admin_pointprod_list_title'];?></span></a></li>
        <li><a href="index.php?act=pointprod&op=prod_add" ><span><?php echo $lang['admin_pointprod_add_title'];?></span></a></li>
        <li><a href="index.php?act=pointorder&op=pointorder_list"><span><?php echo $lang['admin_pointorder_list_title'];?></span></a></li>
        <?php if ($output['order_info']['point_orderstate'] == 20) {?>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['admin_pointorder_ship_title'];?></span></a></li>
        <?php } ?>
        <?php if ($output['order_info']['point_orderstate'] == 30) {?>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['admin_pointorder_ship_modtip'];?></span></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <?php if (is_array($output['order_info']) && count($output['order_info'])>0){ ?>
  <form id="ship_form" method="post" name="ship_form" action="index.php?act=pointorder&op=order_ship&id=<?php echo $_GET['id']; ?>">
    <input type="hidden" name="form_submit" value="ok"/>
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label><?php echo $lang['admin_pointorder_membername']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['order_info']['point_buyername']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label> <?php echo $lang['admin_pointorder_ordersn']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['order_info']['point_ordersn']; ?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label> <?php echo $lang['admin_pointorder_shipping_code']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="shippingcode" name="shippingcode" class="txt" value="<?php echo $output['order_info']['point_shippingcode']; ?>"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label> <?php echo $lang['admin_pointorder_shipping_time']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="shippingtime" name="shippingtime" class="txt date" value="<?php if ($output['order_info']['point_shippingtime']){ echo @date('Y-m-d',$output['order_info']['point_shippingtime']);} ?>">
            </td>
          <td class="vatop tips"><?php echo $lang['admin_pointorder_shipping_timetip']; ?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['admin_pointorder_shipping_description']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><textarea name="shippingdesc" rows="6'" class="tarea" id="shippingdesc"><?php echo $output['order_info']['point_shippingdesc']; ?></textarea></td>
          <td class="vatop tips"></td>
        </tr>
        <tfoot>
        <tr class="tfoot">
          <td colspan="2" >
          <a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
  <?php } else { ?>
  <div class='msgdiv'> <?php echo $output['errormsg']; ?> <br>
    <br>
    <a class="forward" href="index.php?act=pointorder&amp;op=pointorder_list"><?php echo $lang['admin_pointorder_gobacklist']; ?></a> </div>
  <?php } ?>
</div>
<script type="text/javascript">
$(function(){
	$('#shippingtime').datepicker({dateFormat: 'yy-mm-dd'});
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#ship_form").valid()){
	     $("#ship_form").submit();
		}
	});
	$('#ship_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        rules : {
            shippingcode  : {
                required : true
            }
        },
        messages : {
            shippingcode  : {
                required : '<?php echo $lang['admin_pointorder_ship_code_nullerror']; ?>'
            }
        }
    });
});
</script>