<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['feedback_mange_title'];?></h3>
    </div>
  </div>
<div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">&nbsp;</div></th>
      </tr>
    </tbody>
  </table>
  <form method='post' id="form_link">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2 nobdb">
      <thead>
        <tr class="thead">
          <th>&nbsp;</th>
          <th><?php echo $lang['feedback_index_content'];?></th>
          <th><?php echo $lang['feedback_index_time'];?></th>
          <th><?php echo $lang['feedback_index_from'];?></th>
          <th class="align-center"><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td class="w24"><input type="checkbox" name="del_id[]" value="<?php echo $v['id'];?>" class="checkitem"></td>
          <td width="705px"><?php echo htmlspecialchars($v['content'],ENT_QUOTES);?></td>
          <td><?php echo date('Y-m-d H:i:s',$v['ftime']);?></td>
          <td><?php echo $v['type']==1?'MOBILE':'PC';?></td>
          <td class="w96 align-center"><a href="javascript:if(confirm('<?php echo $lang['nc_ensure_del'];?>'))window.location = 'index.php?act=feedback&op=del&id=<?php echo $v['id'];?>';"><?php echo $lang['nc_del'];?></a></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <tr class="tfoot" id="dataFuncs">
          <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16" id="batchAction"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp; <a href="JavaScript:void(0);" class="btn" onclick="if(confirm('<?php echo $lang['nc_ensure_del'];?>')){$('#form_link').submit();}"><span><?php echo $lang['nc_del'];?></span></a>
            <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
      </tfoot>
      <?php } ?>
    </table>
  </form>
</div>