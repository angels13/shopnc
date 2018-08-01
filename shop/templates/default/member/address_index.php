<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
    <a href="javascript:void(0)" class="ncu-btn3" nc_type="dialog" dialog_title="<?php echo $lang['member_address_new_address'];?>" dialog_id="my_address_add"  uri="index.php?act=member&op=address&type=add" dialog_width="550" title="<?php echo $lang['member_address_new_address'];?>"><?php echo $lang['member_address_new_address'];?></a></div>
  <table class="ncu-table-style" >
    <thead>
      <tr>
      	<th class="w10"></th>
        <th class="tl"><?php echo $lang['member_address_receiver_name'];?></th>
        <th class="tl"><?php echo $lang['member_address_location'];?></th>
        <th class="tl"><?php echo $lang['member_address_address'];?></th>
        <th class="tl"><?php echo $lang['member_address_phone'];?>/<?php echo $lang['member_address_mobile'];?></th>
        <th class="w90"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <?php if(!empty($output['address_list']) && is_array($output['address_list'])){?>
    <tbody>
      <?php foreach($output['address_list'] as $key=>$address){?>
      <tr class="bd-line">
      	<td></td>
        <td class="tl"><?php echo $address['true_name'];?></td>
        <td class="tl"><?php echo $address['area_info'];?></td>
        <td class="tl"><?php echo $address['address'];?></td>
        <td class="tl"><span class="tel"><?php echo $address['tel_phone'];?></span> <br/>
          <span class="mob"><?php echo $address['mob_phone']; ?></span></td>
        <td><p><a href="javascript:void(0);" dialog_id="my_address_edit" dialog_width="550" dialog_title="<?php echo $lang['member_address_edit_address'];?>" nc_type="dialog" uri="index.php?act=member&op=address&type=edit&id=<?php echo $address['address_id'];?>"><?php echo $lang['member_address_edit_address'];?></a></p><p><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', 'index.php?act=member&op=address&id=<?php echo $address['address_id'];?>');" class="ncu-btn2 mt5"><?php echo $lang['nc_del_&nbsp'];?></a></p></td>
      </tr>
      <?php }?>
      <?php }else{?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php }?>
    </tbody>
    <tfoot>
      <?php if(!empty($output['address_list']) && is_array($output['address_list'])){?>
      <tr>
        <td colspan="20">&nbsp;</td>
      </tr>
      <?php }?>
    </tfoot>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>