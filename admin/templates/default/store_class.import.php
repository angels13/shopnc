<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['import_data'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store_class&op=store_class"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=store_class&op=store_class_add" ><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a href="index.php?act=store_class&op=store_class_export" ><span><?php echo $lang['export'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['import'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" enctype="multipart/form-data" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="charset" value="gbk" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label><?php echo $lang['sel_file'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><span class="type-file-box">
            <input type="file" name="csv" id="csv" class="type-file-file"  size="30"  />
            </span></td>
          <td class="vatop tips"><?php echo $lang['if_import_speed_slow'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['file_format'];?>:</label>
            <a href="<?php echo RESOURCE_SITE_URL;?>/examples/store_class.csv" class="btns"><span><?php echo $lang['click_download_import_file'];?></span></a></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><table border="1" cellpadding="3" cellspacing="3" bordercolor="#CCC">
              <tbody>
                <tr>
                  <td bgcolor="#EFF8F8"><?php echo $lang['nc_sort'];?></td>
                  <td bgcolor="#FFFFEC"><?php echo $lang['one_store_class'];?></td>
                  <td bgcolor="#FFFFEC">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#EFF8F8"><?php echo $lang['nc_sort'];?></td>
                  <td bgcolor="#FFFFEC">&nbsp;</td>
                  <td bgcolor="#FFFFEC"><?php echo $lang['two_store_class'];?></td>
                </tr>
                <tr>
                  <td bgcolor="#EFF8F8"><?php echo $lang['nc_sort'];?></td>
                  <td bgcolor="#FFFFEC">&nbsp;</td>
                  <td bgcolor="#FFFFEC"><?php echo $lang['two_store_class'];?></td>
                </tr>
                <tr>
                  <td bgcolor="#EFF8F8"><?php echo $lang['nc_sort'];?></td>
                  <td bgcolor="#FFFFEC"><?php echo $lang['one_store_class'];?></td>
                  <td bgcolor="#FFFFEC"></td>
                </tr>
              </tbody>
            </table></td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:document.form1.submit();" class="btn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
	$(function(){
    var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='' class='type-file-button' />"
	$(textButton).insertBefore("#csv");
	$("#csv").change(function(){
	$("#textfield1").val($("#csv").val());
	});
});
</script> 