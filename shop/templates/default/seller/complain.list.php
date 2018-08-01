<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php">
  <table class="search-form">
    <input type="hidden" name='act' value='store_complain' />
    <tr>
      <td>&nbsp;</td>
      <td class="w100 tr"><select name="select_complain_state">
          <option value="0" <?php if (empty($_GET['select_complain_state'])){echo 'selected=true';}?>> <?php echo $lang['complain_state_all'];?> </option>
          <option value="1" <?php if ($_GET['select_complain_state'] == '1'){echo 'selected=true';}?>> <?php echo $lang['complain_state_inprogress'];?> </option>
          <option value="2" <?php if ($_GET['select_complain_state'] == '2'){echo 'selected=true';}?>> <?php echo $lang['complain_state_finish'];?> </option>
        </select></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
  </table>
</form>
<table class="ncsc-table-style">
  <thead>
    <tr>
      <th class="w10"></th>
      <th class="tl"><?php echo $lang['complain_accuser'];?></th>
      <th class="tl"><?php echo $lang['complain_subject_content'];?></th>
      <th><?php echo $lang['complain_datetime'];?></th>
      <th><?php echo $lang['complain_state'];?></th>
      <th class="w100"><?php echo $lang['nc_handle'];?></th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($output['list'])>0) { ?>
    <?php foreach($output['list'] as $val) { ?>
    <tr class="bd-line">
      <td></td>
      <td class="tl"><?php echo $val['accuser_name'];?></td>
      <td class="tl"><?php echo $val['complain_subject_content'];?></td>
      <td class="goods-time"><?php echo date("Y-m-d H:i:s",$val['complain_datetime']);?></td>
      <td><?php
				if(intval($val['complain_state'])===10) echo $lang['complain_state_new'];
				if(intval($val['complain_state'])===20) echo $lang['complain_state_appeal'];
				if(intval($val['complain_state'])===30) echo $lang['complain_state_talk'];
				if(intval($val['complain_state'])===40) echo $lang['complain_state_handle'];
				if(intval($val['complain_state'])===99) echo $lang['complain_state_finish'];
				?></td>
      <td class="nscs-table-handle"><span><a href="index.php?act=store_complain&op=complain_show&complain_id=<?php echo $val['complain_id'];?>" target="_blank" class="btn-blue"><i class="icon-edit"></i>
        <p><?php echo $lang['complain_text_detail'];?></p>
        </a></span>
        </td>
    </tr>
    <?php }?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (count($output['list'])>0) { ?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page'];?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
