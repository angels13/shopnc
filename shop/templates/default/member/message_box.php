<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <ul class="tab">
      <?php if(is_array($output['member_menu']) and !empty($output['member_menu'])) {
	foreach ($output['member_menu'] as $key => $val) {
		$classname = 'normal';
		if($val['menu_key'] == $output['menu_key']) {
			$classname = 'active';
		}
		if ($val['menu_key'] == 'message'){
			echo '<li class="'.$classname.'"><a href="'.$val['menu_url'].'">'.$val['menu_name'].'(<span style="color: red;">'.$output['newcommon'].'</span>)</a></li>';
		}elseif ($val['menu_key'] == 'system'){
			echo '<li class="'.$classname.'"><a href="'.$val['menu_url'].'">'.$val['menu_name'].'(<span style="color: red;">'.$output['newsystem'].'</span>)</a></li>';
		}elseif ($val['menu_key'] == 'close'){
			echo '<li class="'.$classname.'"><a href="'.$val['menu_url'].'">'.$val['menu_name'].'(<span style="color: red;">'.$output['newpersonal'].'</span>)</a></li>';
		}else{
			echo '<li class="'.$classname.'"><a href="'.$val['menu_url'].'">'.$val['menu_name'].'</a></li>';
		}
	}
}?>
    </ul>
    <?php if ($output['isallowsend']){?>
    <a href="index.php?act=home&op=sendmsg" class="ncu-btn3" title="<?php echo $lang['home_message_send_message'];?>"><?php echo $lang['home_message_send_message'];?></a>
    <?php }?>
  </div>
  <table class="ncu-table-style">
    <thead>
      <tr>
        <th class="w30"></th>
        <th class="w150"><?php
                if ($output['drop_type'] == 'msg_seller'){
                	echo $lang['home_message_storename'];
                }else {
                	echo $lang['home_message_sender'];
                }?></th>
        <th class="tl"><?php echo $lang['home_message_content'];?></th>
        <th class="w150"><?php echo $lang['home_message_last_update'];?></th>
        <th class="w90"><?php echo $lang['home_message_command'];?></th>
      </tr>
      <?php if (!empty($output['message_array'])) { ?>
      <tr>
        <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
        <td colspan="20"><label for="all"><?php echo $lang['home_message_select_all'];?></label>
          <?php if ($output['drop_type'] == 'msg_list'){?>
          <a href="javascript:void(0)" class="ncu-btn1" uri="index.php?act=home&op=dropcommonmsg&drop_type=<?php echo $output['drop_type']; ?>" name="message_id" confirm="<?php echo $lang['home_message_delete_confirm'];?>?" nc_type="batchbutton"><span><?php echo $lang['home_message_delete'];?></span></a>
          <?php }?>
          <?php if ($output['drop_type'] == 'msg_system' || $output['drop_type'] == 'msg_seller'){?>
          <a href="javascript:void(0)" class="ncu-btn1" uri="index.php?act=home&op=dropbatchmsg&drop_type=<?php echo $output['drop_type']; ?>" name="message_id" confirm="<?php echo $lang['home_message_delete_confirm'];?>?" nc_type="batchbutton"><span><?php echo $lang['home_message_delete'];?></span></a>
          <?php }?>
          <?php }?></td>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($output['message_array'])) { ?>
      <?php foreach($output['message_array'] as $k => $v){ ?>
      <tr class="bd-line">
        <td class="tc"><input type="checkbox" class="checkitem" value="<?php echo $v['message_id']; ?>"/></td>
        <td><?php if ($output['drop_type'] == 'msg_seller'){?>
          <?php echo $v['from_member_name']; ?>
          <?php }else { echo $v['from_member_name']; }?></td>
        <td class="link2<?php if($v['message_open'] == 0){?> font_bold<?php }?> tl"><?php echo parsesmiles($v['message_body']); ?></td>
        <td><?php echo @date("Y-m-d H:i:s",$v['message_update_time']); ?></td>
        <td><?php if ($output['drop_type'] == 'msg_list'){?>
          <a href="index.php?act=home&op=showmsgcommon&drop_type=<?php echo $output['drop_type']; ?>&message_id=<?php echo $v['message_id']; ?><?php if($v['message_parent_id']>0) echo '#'.$v['message_id']; ?>"><?php echo $lang['home_message_view_detail'];?></a> <a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['home_message_delete_confirm'];?>?', 'index.php?act=home&op=dropcommonmsg&drop_type=<?php echo $output['drop_type']; ?>&message_id=<?php echo $v['message_id']; ?>');" class="ncu-btn2 mt5"><?php echo $lang['home_message_delete'];?></a>
          <?php }?>
          <?php if ($output['drop_type'] == 'msg_system' || $output['drop_type'] == 'msg_seller'){?>
          <a href="index.php?act=home&op=showmsgbatch&drop_type=<?php echo $output['drop_type']; ?>&message_id=<?php echo $v['message_id']; ?><?php if($v['message_parent_id']>0) echo '#'.$v['message_id']; ?>"><?php echo $lang['home_message_view_detail'];?></a> <a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['home_message_delete_confirm'];?>?', 'index.php?act=home&op=dropbatchmsg&drop_type=<?php echo $output['drop_type']; ?>&message_id=<?php echo $v['message_id']; ?>');" class="ncu-btn2 mt5"><?php echo $lang['home_message_delete'];?></a>
          <?php }?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="19" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if (!empty($output['message_array'])) { ?>
      <tr>
        <td class="tc"><input id="all2" type="checkbox" class="checkall" /></td>
        <td colspan="22"><label for="al2"><?php echo $lang['home_message_select_all'];?></label>
          <?php if ($output['drop_type'] == 'msg_list'){?>
          <a href="javascript:void(0)" class="ncu-btn1" uri="index.php?act=home&op=dropcommonmsg&drop_type=<?php echo $output['drop_type']; ?>" name="message_id" confirm="<?php echo $lang['home_message_delete_confirm'];?>?" nc_type="batchbutton"><span><?php echo $lang['home_message_delete'];?></span></a>
          <?php }?>
          <?php if ($output['drop_type'] == 'msg_system' || $output['drop_type'] == 'msg_seller'){?>
          <a href="javascript:void(0)" class="ncu-btn1" uri="index.php?act=home&op=dropbatchmsg&drop_type=<?php echo $output['drop_type']; ?>" name="message_id" confirm="<?php echo $lang['home_message_delete_confirm'];?>?" nc_type="batchbutton"><span><?php echo $lang['home_message_delete'];?></span></a>
          <?php }?>
        <div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
