<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <table class="ncu-table-style">
    <tbody>
      <?php if ($output['fan_list']) { ?>
      <tr>
        <td colspan="2"><ul class="friend-list">
            <?php foreach($output['fan_list'] as $k => $v){ ?>
            <li id="recordone_<?php echo $v['friend_frommid']; ?>">
              <div class="friend-img"> <span class="thumb size100"> <a href="index.php?act=member_snshome&mid=<?php echo $v['friend_frommid'];?>" target="_blank" data-param="{'id':<?php echo $v['friend_frommid'];?>}" nctype="mcard"><img src="<?php if ($v['member_avatar']!='') { echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.DS.$v['member_avatar']; } else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); } ?>" alt="<?php echo $v['friend_frommname']; ?>" onload="javascript:DrawImage(this,100,100);"/></a> </span> </div>
              <dl>
                <dt>
                	<a href="index.php?act=member_snshome&mid=<?php echo $v['friend_frommid'];?>"  class="friend-name" title="<?php echo $v['friend_tomname']; ?>" target="_blank" data-param="{'id':<?php echo $v['friend_frommid'];?>}" nctype="mcard"><?php echo $v['friend_frommname']; ?></a>
                	<a href="index.php?act=home&op=sendmsg&member_id=<?php echo $v['friend_frommid']; ?>" target="_blank" class="message" title="<?php echo $lang['nc_message'] ;?>">&nbsp;</a>
                	<em class="<?php echo $v['sex_class'];?>"></em>
                </dt>
                <dd class="area"><?php echo $v['member_areainfo'];?></dd>
                <dd nc_type="signmodule"> <span nc_type="mutualsign" style="<?php echo $v['friend_followstate']!=2?'display:none;':'';?>"><?php echo $lang['snsfriend_follow_eachother']?></span> <a href="javascript:void(0)" class="ncu-btn2" nc_type="followbtn" data-param='{"mid":"<?php echo $v['friend_frommid'];?>"}' style="<?php echo $v['friend_followstate']==2?'display:none;':'';?>"><?php echo $lang['snsfriend_followbtn'];?></a> </dd>
              </dl>
            </li>
            <?php } ?>
          </ul></td>
      </tr>
      <?php } else { ?>
      <tr>
        <td class="norecord" colspan="2"><i></i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if ($output['fan_list']) { ?>
    <tfoot>
      <tr>
        <td><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
      </tr>
    </tfoot>
    <?php } ?>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns_friend.js"></script>
