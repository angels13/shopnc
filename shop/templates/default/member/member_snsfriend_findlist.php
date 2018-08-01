<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <div class="friendfind"> 
    <!-- 搜索好友start -->
    <form method="post" id="search_form" action="index.php?act=member_snsfriend&op=findlist">
	    <div class="search-form">
	      <div class="pt5 pb5">
	      <span class="pl50"></span>
	      <input type="text" class="text" style="width:340px;" name="searchname" id="searchname" value="<?php echo $_GET['searchname'];?>" placeholder="<?php echo $lang['snsfriend_find_keytip'];?>" >
	      <a class="submit" nctype="search_submit"><?php echo $lang['snsfriend_search'];?>"</a>
	      <a href="javascript:void(0);" nctype="advanced_search"><?php echo $lang['snsfriend_advanced_search'];?></a>
	      </div>
	      <div class="pb5" style="display:none;" nctype="advanced_search">
	        <span class="pl50"></span>
	        <select nctype="area" name="provinceid" id="provinceid">
	        </select>
	        <select nctype="area" name="cityid" id="cityid">
	          <option><?php echo $lang['snsfriend_city'];?></option>
	        </select>
	        <select nctype="area" name="areaid" id="areaid">
	          <option><?php echo $lang['snsfriend_county'];?></option>
	        </select>
	        <select name="age" id="age">
	          <option value="0"><?php echo $lang['snsfriend_age'];?></option>
	          <option value="1"><?php echo $lang['snsfriend_age_less_than_18'];?></option>
	          <option value="2"><?php echo $lang['snsfriend_age_between_18_to_24'];?></option>
	          <option value="3"><?php echo $lang['snsfriend_age_between_25_to_30'];?></option>
	          <option value="4"><?php echo $lang['snsfriend_age_between_31_to_35'];?></option>
	          <option value="5"><?php echo $lang['snsfriend_age_more_than_35'];?></option>
	        </select>
	        <select name="sex" id="sex">
	          <option value=""><?php echo $lang['snsfriend_sex'];?></option>
	          <option value="0"><?php echo $lang['snsfriend_man'];?></option>
	          <option value="1"><?php echo $lang['snsfriend_woman'];?></option>
	        </select>
	      </div>
	    </div>
    </form>
    <!-- 粉丝列表start -->
    <div class="fanlist">
      <table class="ncu-table-style">
        <thead>
          <tr>
            <th class="tc"><h3><?php echo $lang['snsfriend_searchresult'];?></h3></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="20" id="searchlist">
				<div id="lazymodule"> 
				  <!-- 搜索列表start -->
				  <table class="ncu-table-style">
				    <tbody>
				      <?php if ($output['memberlist']) { ?>
				      <tr>
				        <td colspan="2"><ul class="friend-list">
				            <?php foreach($output['memberlist'] as $k => $v){ ?>
				            <li id="recordone_<?php echo $v['member_id']; ?>">
				              <div class="friend-img"> <span class="thumb size100"> <a href="index.php?act=member_snshome&mid=<?php echo $v['member_id'];?>" target="_blank"><img src="<?php if ($v['member_avatar']!='') { echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.DS.$v['member_avatar']; } else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait'); } ?>" alt="<?php echo $v['member_name']; ?>" onload="javascript:DrawImage(this,100,100);"  data-param="{'id':<?php echo $v['member_id'];?>}" nctype="mcard" /></a> </span> </div>
				              <dl>
				                <dt> <a href="index.php?act=member_snshome&mid=<?php echo $v['member_id'];?>"  class="friend-name" title="<?php echo $v['friend_tomname']; ?>" target="_blank" data-param="{'id':<?php echo $v['member_id'];?>}" nctype="mcard"><?php echo $v['member_name']; ?></a> <a href="index.php?act=home&op=sendmsg&member_id=<?php echo $v['member_id']; ?>" target="_blank" class="message" title="<?php echo $lang['nc_message'] ;?>">&nbsp;</a> <em class="<?php echo $v['sex_class']; ?>"></em> </a> </dt>
				                <dd class="area"><?php echo $v['member_areainfo'];?></dd>
				                <dd nc_type="signmodule"> <span nc_type="mutualsign" style="<?php echo $v['followstate']!=2?'display:none;':'';?>"><?php echo $lang['snsfriend_follow_eachother'];?></span> <span nc_type="followsign" style="<?php echo $v['followstate']!=1?'display:none;':'';?>"><?php echo $lang['snsfriend_followed'];?></span> <a href="javascript:void(0)" class="ncu-btn2" nc_type="followbtn" data-param='{"mid":"<?php echo $v['member_id'];?>"}'style="<?php echo $v['followstate']!=0?'display:none;':'';?>"><?php echo $lang['snsfriend_followbtn'];?></a> </dd>
				              </dl>
				            </li>
				            <?php } ?>
				          </ul></td>
				      </tr>
				      <?php } else{?>
				      <tr>
				        <td colspan="2" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
				      </tr>
				      <?php }?>
				    </tbody>
				    <tfoot>
				      <tr>
				        <td colspan="20"></td>
				      </tr>
				    </tfoot>
				  </table>
				  <?php if ($output['hasmore']){?>
				  <div id="lazymore"></div>
				  <?php }?>
				  <!-- 搜索列表end --> 
				</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- 粉丝列表end -->
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns_friend.js" charset="utf-8"></script> 
<script type="text/javascript">
$(function(){
	$('a[nctype="search_submit"]').click(function(){
		// 验证用户名是否为空
		if($('#searchname').val() != ''){
		    $('#search_form').submit();
		}else{
			$('#searchname').addClass('error').focus();
		}
	});
	
	// 高级搜索显示与隐藏
	$('a[nctype="advanced_search"]').click(function(){
		$('div[nctype="advanced_search"]').toggle('slow');
	});

	// 地区
	areaInit($('select[nctype="area"]:first'),0);
	$('select[nctype="area"]').change(function(){
		$(this).nextAll('select[nctype="area"]').html('');
		if($(this).next().attr('nctype') == 'area' && !isNaN(parseInt($(this).val()))){
			$('#area_ids').val($(this).val());
			areaInit($(this).next(), $(this).val());
		}
	});
});
</script>
