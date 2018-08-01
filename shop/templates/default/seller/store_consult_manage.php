<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<table class="ncsc-table-style">
  <thead>
    <tr>
      <th class="w30"></th>
      <th><?php echo $lang['store_consult_reply'];?></th>
      <th class="w30"></th>
      <th class="w110"><?php echo $lang['nc_handle'];?></th>
    </tr>
    <?php if (count($output['list_consult'])>0) { ?>
    <tr>
      <td class="tc"><input id="all" type="checkbox" class="checkall" /></td>
      <td colspan="20"><label for="all"><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" class="ncsc-btn-mini" nc_type="batchbutton" uri="index.php?act=store_consult&op=drop_consult" name="id" confirm="<?php echo $lang['nc_ensure_del'];?>"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a></td>
    </tr>
    <?php }?>
  </thead>
  <tbody>
    <?php if (count($output['list_consult'])>0) { ?>
    <?php foreach($output['list_consult'] as $consult){?>
    <tr>
      <th colspan="20" class="tl"><input type="checkbox"  value="<?php echo $consult['consult_id'];?>" class="checkitem ml10 mr10" />
        <span><a href="index.php?act=goods&goods_id=<?php echo $consult['goods_id'];?>" target="_blank"><?php echo $consult['cgoods_name'];?></a></span><span class="ml20"><?php echo $lang['store_consult_list_consult_member'].$lang['nc_colon'];?></span>
        <?php if($consult['member_id'] == "0"){ echo $lang['nc_guest']; } else { echo $consult['isanonymous'] == 1?str_cut($consult['cmember_name'],2).'***':$consult['cmember_name']; }?>
        <span class="ml20"><?php echo $lang['store_consult_list_consult_time'].$lang['nc_colon'];?><em class="goods-time"><?php echo date("Y-m-d H:i:s",$consult['consult_addtime']);?></em></span></th>
    </tr>
    <tr>
      <td rowspan="2"></td>
      <td class="tl"><strong><?php echo $lang['store_consult_list_consult_content'].$lang['nc_colon'];?></strong><span class="gray"><?php echo nl2br($consult['consult_content']);?></span></td>
      <td rowspan="2"></td>
      <td rowspan="2" class="nscs-table-handle vt"><?php if($consult['consult_reply'] == ''){?>
        <span><a href="javascript:void(0);" class="btn-acidblue" nc_type="dialog" dialog_id="my_qa_reply" dialog_title="<?php echo $lang['store_consult_list_reply_consult'];?>" dialog_width="460" uri="index.php?act=store_consult&op=reply_consult&id=<?php echo $consult['consult_id'];?>"><i class="icon-comments-alt "></i><p><?php echo $lang['store_consult_list_reply'];?></p></a></span>
        <?php }else{?>
        <span><a href="javascript:void(0);" nc_type="dialog" dialog_id="my_qa_edit_reply" dialog_title="<?php echo $lang['store_consult_list_reply_consult'];?>" dialog_width="480" uri="index.php?act=store_consult&op=reply_consult&id=<?php echo $consult['consult_id'];?>" class="btn-blue"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <?php }?>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', 'index.php?act=store_consult&op=drop_consult&id=<?php echo $consult['consult_id'];?>');" class="btn-red"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a> </span></td>
    </tr>
    
    <tr><?php if($consult['consult_reply']!=""){?>
      <td class="tl"><strong><?php echo $lang['store_consult_list_my_reply'].$lang['nc_colon'];?></strong><span class="gray"><?php echo nl2br($consult['consult_reply']);?></span><span class="ml10 goods-time">(<?php echo date("Y-m-d H:i:s",$consult['consult_reply_time']);?>)</span></td>
    <?php }?></tr>
    
    <?php }?>
    <?php }else{?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php }?>
  </tbody>
  <tfoot>
    <?php if (count($output['list_consult'])>0) { ?>
    <tr>
      <th class="tc"><input id="all" type="checkbox" class="checkall" /></th>
      <th colspan="20"><label for="all2"><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" class="ncsc-btn-mini" nc_type="batchbutton" uri="index.php?act=store_consult&op=drop_consult" name="id" confirm="<?php echo $lang['nc_ensure_del'];?>"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a></th>
    </tr>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php }?>
  </tfoot>
</table>