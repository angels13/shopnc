<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
    <div class="text-intro"><?php echo $lang['points_log_pointscount']; ?><?php echo $output['member_info']['member_points']; ?></div>
  </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name="act" value="member_points" />
      <tr><td></td>
        <th><?php echo $lang['points_stage'].$lang['nc_colon']; ?></th>
        <td class="w100"><select name="stage">
            <option value="" <?php if (!$_GET['stage']){echo 'selected=selected';}?>><?php echo $lang['nc_please_choose'];?></option>
            <option value="regist" <?php if ($_GET['stage'] == 'regist'){echo 'selected=selected';}?>><?php echo $lang['points_stage_regist']; ?></option>
            <option value="login" <?php if ($_GET['stage'] == 'login'){echo 'selected=selected';}?>><?php echo $lang['points_stage_login']; ?></option>
            <option value="comments" <?php if ($_GET['stage'] == 'comments'){echo 'selected=selected';}?>><?php echo $lang['points_stage_comments']; ?></option>
            <option value="order" <?php if ($_GET['stage'] == 'order'){echo 'selected=selected';}?>><?php echo $lang['points_stage_order']; ?></option>
            <option value="system" <?php if ($_GET['stage'] == 'system'){echo 'selected=selected';}?>><?php echo $lang['points_stage_system']; ?></option>
            <option value="pointorder" <?php if ($_GET['stage'] == 'pointorder'){echo 'selected=selected';}?>><?php echo $lang['points_stage_pointorder']; ?></option>
            <option value="app" <?php if ($_GET['stage'] == 'app'){echo 'selected=selected';}?>><?php echo $lang['points_stage_app']; ?></option>
          </select></td>
        <th><?php echo $lang['points_addtime'].$lang['nc_colon']; ?></th>
        <td class="w180"><input type="text" id="stime" name="stime" class="text" value="<?php echo $_GET['stime'];?>">
          &nbsp;<?php echo $lang['points_addtime_to']; ?>
          <input type="text" id="etime" name="etime" class="text" value="<?php echo $_GET['etime'];?>"></td>
        <th><?php echo $lang['points_pointsdesc'].$lang['nc_colon']; ?></th>
        <td class="w150"><input type="text" class="text" id="description" name="description" value="<?php echo $_GET['description'];?>"></td>
        <td class="w90 tc"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></td>
      </tr>
    </table>
  </form>
  <table class="ncu-table-style">
    <thead>
      <tr>
        <th class="w150"><?php echo $lang['points_addtime']; ?></th>
        <th class="w100"><?php echo $lang['points_pointsnum']; ?></th>
        <th class="w150"><?php echo $lang['points_stage']; ?></th>
        <th class="tl"><?php echo $lang['points_pointsdesc']; ?></th>
      </tr>
    </thead>
    <tbody>
      <?php  if (count($output['list_log'])>0) { ?>
      <?php foreach($output['list_log'] as $val) { ?>
      <tr class="bd-line">
        <td class="goods-time"><?php echo @date('Y-m-d',$val['pl_addtime']);?></td>
        <td class="goods-price"><?php echo ($val['pl_points'] > 0 ? '+' : '').$val['pl_points']; ?></td>
        <td><?php 
	              	switch ($val['pl_stage']){
	              		case 'regist':
	              			echo $lang['points_stage_regist'];
	              			break;
	              		case 'login':
	              			echo $lang['points_stage_login'];
	              			break;
	              		case 'comments':
	              			echo $lang['points_stage_comments'];
	              			break;
	              		case 'order':
	              			echo $lang['points_stage_order'];
	              			break;
	              		case 'system':
	              			echo $lang['points_stage_system'];
	              			break;
	              		case 'pointorder':
	              			echo $lang['points_stage_pointorder'];
	              			break;
	              		case 'app':
	              			echo $lang['points_stage_app'];
	              			break;
	              	}
	              ?></td>
        <td class="tl"><?php echo $val['pl_desc'];?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record']; ?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php  if (count($output['list_log'])>0) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script> 
<script language="javascript">
$(function(){
	$('#stime').datepicker({dateFormat: 'yy-mm-dd'});
	$('#etime').datepicker({dateFormat: 'yy-mm-dd'});
});
</script>