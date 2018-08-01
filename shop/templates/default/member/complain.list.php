<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <form method="get" action="index.php">
    <table class="search-form">
      <input type="hidden" name='act' value='member_complain' />
      <tr>
        <td>&nbsp;</td>
        <td class="w100 tr"><select name="select_complain_state">
            <option value="0" <?php if (empty($_GET['select_complain_state'])){echo 'selected=true';}?>> <?php echo $lang['complain_state_all'];?> </option>
            <option value="1" <?php if ($_GET['select_complain_state'] == '1'){echo 'selected=true';}?>> <?php echo $lang['complain_state_inprogress'];?> </option>
            <option value="2" <?php if ($_GET['select_complain_state'] == '2'){echo 'selected=true';}?>> <?php echo $lang['complain_state_finish'];?> </option>
          </select></td>
        <td class="w90 tc"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></td>
      </tr>
    </table>
  </form>
  <table class="ncu-table-style">
    <thead>
      <tr>
      	<th class="w10"></th>
        <th class="tl"><?php echo $lang['complain_accused'];?></th>
        <th class="tl"><?php echo $lang['complain_subject_content'];?></th>
        <th><?php echo $lang['complain_datetime'];?></th>
        <th><?php echo $lang['complain_state'];?></th>
        <th class="w90"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php  if (count($output['list'])>0) { ?>
      <?php foreach($output['list'] as $val) { ?>
      <tr class="bd-line">
      	<td></td>
        <td class="tl"><?php echo $val['accused_name'];?></td>
        <td class="tl"><?php echo $val['complain_subject_content'];?></td>
        <td class="goods-time"><?php echo date("Y-m-d H:i:s",$val['complain_datetime']);?></td>
        <td><?php
				if(intval($val['complain_state'])===10) echo $lang['complain_state_new'];
				if(intval($val['complain_state'])===20) echo $lang['complain_state_appeal'];
				if(intval($val['complain_state'])===30) echo $lang['complain_state_talk'];
				if(intval($val['complain_state'])===40) echo $lang['complain_state_handle'];
				if(intval($val['complain_state'])===99) echo $lang['complain_state_finish'];
				?></td>
        <td><p><a href="index.php?act=member_complain&op=complain_show&complain_id=<?php echo $val['complain_id'];?>" target="_blank"><?php echo $lang['complain_text_detail'];?></a></p>
          <?php
				if(intval($val['complain_state'])==10) {
				?>
          <p><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['complain_cancel_confirm'];?>','index.php?act=member_complain&op=complain_cancel&complain_id=<?php echo $val['complain_id']; ?>')" class="ncu-btn2 mt5"><?php echo $lang['nc_cancel']; ?></a></p>
          <?php } ?></td>
      </tr>
      <?php }?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php  if (count($output['list'])>0) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page'];?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
