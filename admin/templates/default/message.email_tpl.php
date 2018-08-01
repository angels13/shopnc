<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_message_set'];?></h3>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form name='form1' method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="submit_type" id="submit_type" value="" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"><?php echo $lang['nc_list'];?></th>
        </tr>
        <tr class="thead">
          <th>&nbsp;</th>
          <th><?php echo $lang['mailtemplates_index_desc'];?></th>
          <th class="align-center"><?php echo $lang['open'];?></th>
          <th class="align-center"><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(is_array($output['templates_list']) && !empty($output['templates_list'])){?>
        <?php foreach($output['templates_list'] as $k => $v){?>
        <tr class="hover">
          <td class="w24"><input type="checkbox" name="del_id[]" value="<?php echo $v['code'];?>" class="checkitem"></td>
          <td class="w50pre"><?php echo $v['name']; ?></td>
          <td class="align-center power-onoff"><?php echo $v['mail_switch'] == '0' ? $lang['nc_no'] : $lang['nc_yes']; ?></td>
          <td class="w60 align-center"><a href="index.php?act=message&op=email_tpl_edit&code=<?php echo $v['code']; ?>"><?php echo $lang['nc_edit'];?></a></td>
        </tr>
        <?php } ?>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="$('#submit_type').val('mail_switchON');go();"><span><?php echo $lang['open'];?></span></a><a href="JavaScript:void(0);" class="btn" onclick="$('#submit_type').val('mail_switchOFF');go();"><span><?php echo $lang['close'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script> 
<script type="text/javascript">
function go(){
	var url="index.php?act=message&op=email_tpl_ajax";
	document.form1.action = url;
	document.form1.submit();
}
</script>