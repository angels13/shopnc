<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="sidebar">
<?php include template('sns/sns_sidebar_visitor');?>
<?php include template('sns/sns_sidebar_messageboard');?>
</div>
<div class="left-content"> 
  <div class="tabmenu">
    <ul class="tab">
      <li class="active"><a href="javascript:void(0)"><?php echo $lang['sns_share_of_fresh_news'];?></a></li>
    </ul>
  </div>
  <!-- 动态列表 -->
  <div id="friendtrace"></div>
</div>
<div class="clear"></div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxdatalazy.js" charset="utf-8"></script> 
<script type="text/javascript">
document.onclick = function(){ $("#smilies_div").html(''); $("#smilies_div").hide();};
$(function(){
	//加载好友动态分页
	$('#friendtrace').lazyinit();
	$('#friendtrace').lazyshow({url:"index.php?act=member_snshome&op=tracelist&mid=<?php echo $output['member_info']['member_id'];?>&page=1",'iIntervalId':true});
});
</script>