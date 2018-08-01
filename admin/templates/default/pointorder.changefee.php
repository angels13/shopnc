<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_pointprod'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=pointprod&op=pointprod" ><span><?php echo $lang['admin_pointprod_list_title'];?></span></a></li>
        <li><a href="index.php?act=pointprod&op=prod_add" ><span><?php echo $lang['admin_pointprod_add_title'];?></span></a></li>
        <li><a href="index.php?act=pointorder&op=pointorder_list"><span><?php echo $lang['admin_pointorder_list_title'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['admin_pointorder_changefee'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <?php if (is_array($output['order_info']) && count($output['order_info'])>0){ ?>
  <form id="cfee_form" method="post" name="cfee_form" action="index.php?act=pointorder&op=order_changfee&id=<?php echo $_GET['id']; ?>">
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
          <td colspan="2" class="required"><label> <?php echo $lang['admin_pointorder_shippingfee']; ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="shippingfee" name="shippingfee" class="txt" value="<?php echo $output['order_info']['point_shippingfee']; ?>"></td><td class="vatop tips"></td>
        </tr></tbody>
        <tfoot>
         <tr class="tfoot">
          <td colspan="15">
          <a href="JavaScript:void(0);" class="btn" id="submitBtn" onclick="document.cfee_form.submit()"><span><?php echo $lang['nc_submit'];?></span></a>
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
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
.dialogdiv th {
	text-align:right;
	padding-right:10px;
	width:20%;
}
.msgdiv {
	text-align:center;
	padding:40px 0px;
}
</style>
<script type="text/javascript">
$(function(){
    $('#cfee_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        rules : {
            shippingfee  : {
                required : true,
                number 	 : true
            }
        },
        messages : {
            shippingfee  : {
                required : '<?php echo $lang['admin_pointorder_changefee_freightpricenull']; ?>',
                number   : '<?php echo $lang['admin_pointorder_changefee_freightprice_error']; ?>'
            }
        }
    });
});
</script>
