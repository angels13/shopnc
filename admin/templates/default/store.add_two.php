<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.d_inline {
	display:inline;
}
</style>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store&op=store" ><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a href="index.php?act=store&op=store_audit" ><span><?php echo $lang['pending'];?></span></a></li>
        <li><a href="index.php?act=store&op=store_auth" ><span><?php echo $lang['store_auth_verify'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="store_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="step" value="two" />
    <input type="hidden" name="member_name" value="<?php echo $output['member_array']['member_name'];?>" />
    <input type="hidden" name="member_id" value="<?php echo $output['member_array']['member_id'];?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label><?php echo $lang['store_user_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $output['member_array']['member_name'];?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="store_owner_card"><?php echo $lang['store_user_id'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="store_owner_card" name="store_owner_card" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="store_name"><?php echo $lang['store_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="store_name" name="store_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['belongs_class'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><select name="sc_id">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(is_array($output['class_list'])){ ?>
              <?php foreach($output['class_list'] as $k => $v){ ?>
              <option value="<?php echo $v['sc_id']; ?>"><?php echo $v['sc_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation"> <?php echo $lang['location'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><div class="select_add change-select-2" id="region">
              <input type="hidden" name="area_id" value="" class="area_ids" />
              <input type="hidden" name="area_info" value="" class="area_names " />
              <select class="d_inline">
              </select>
            </div></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="store_address"><?php echo $lang['details_address'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="store_address" name="store_address" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="store_zip"><?php echo $lang['zip'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="store_zip" name="store_zip" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="store_tel"><?php echo $lang['tel_phone'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="store_tel" name="store_tel" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="grade_id"><?php echo $lang['belongs_level'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><select id="grade_id" name="grade_id">
              <?php if(is_array($output['grade_list'])){ ?>
              <?php foreach($output['grade_list'] as $k => $v){ ?>
              <option value="<?php echo $v['sg_id']; ?>"><?php echo $v['sg_name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="end_time"><?php echo $lang['period_to'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="end_time" name="end_time" class="txt date"></td>
          <td class="vatop tips"><?php echo $lang['formart'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="state"><?php echo $lang['state'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff"><label for="store_state1" class="cb-enable selected"><span><?php echo $lang['open'];?></span></label>
            <label for="store_state0" class="cb-disable" ><span><?php echo $lang['close'];?></span></label>
            <input id="store_state1" name="store_state" checked="" onclick="$('#tr_store_close_info').hide();" value="1" type="radio">
            <input id="store_state0" name="store_state" onclick="$('#tr_store_close_info').show();" value="0" type="radio"></td>
        </tr>
      </tbody>
      <tbody id="tr_store_close_info">
        <tr>
          <td colspan="2" class="required"><label for=""><?php echo $lang['close_reason'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><label for="store_close_info"></label>
            <textarea rows="6" class="tarea" id="store_close_info" name="store_close_info"></textarea></td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tbody>
        <tr>
          <td colspan="2" class="required"><label> <?php echo $lang['certification'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><ul class="nofloat">
              <li>
                <label for="name_auth">
                  <input type="checkbox" value="1" id="name_auth" name="name_auth">
                  <?php echo $lang['owner_certification'];?></label>
              </li>
              <li>
                <label for="material">
                  <input type="checkbox" id="store_auth" value="1" name="store_auth">
                  <?php echo $lang['entity_store_certification'];?></label>
              </li>
            </ul></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['recommended'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff"><label for="store_recommend1" class="cb-enable selected" ><span><?php echo $lang['nc_yes'];?></span></label>
            <label for="store_recommend0" class="cb-disable" ><span><?php echo $lang['nc_no'];?></span></label>
            <input id="store_recommend1" name="store_recommend" checked="" value="1" type="radio">
            <input id="store_recommend0" name="store_recommend" value="0" type="radio"></td>
          <td class="vatop tips"><?php echo $lang['recommended_tips'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="0" id="store_sort" name="store_sort" class="txt"></td>
          <td class="vatop tips"></td>
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
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/i18n/zh-CN.js";?>" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript">

var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(function(){
	$('#end_time').datepicker();
	regionInit("region");	
	$("#tr_store_close_info").hide();
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#store_form").valid()){
     $("#store_form").submit();
	}
	});
});
//	
	$('#store_form').validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parentsUntil('tr').parent().prev().find('td:first'));
        },

		rules : {
			store_name: {
				required : true
			},
			area_id: {
				required: true,
				checkarea : true
			},
			end_time	: {
				date	: false
			}
		},
		messages : {
			store_name: {
				required: '<?php echo $lang['please_input_store_name'];?>'
			},
      area_id: {
        required: '<?php echo $lang['please_input_address'];?>',
        checkarea: '<?php echo $lang['please_input_address'];?>'
			}
		}
	});
});
</script>
