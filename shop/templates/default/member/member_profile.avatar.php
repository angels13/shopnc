<?php defined('InShopNC') or exit('Access Invalid!');?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.js"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" id="cssfile2" />
<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
  <?php if ($output['newfile'] == ''){?>
  <form action="index.php?act=home&op=upload" enctype="multipart/form-data" id="form_avaster" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncu-form-style">
      <dl>
        <dt><?php echo $lang['home_member_avatar_thumb'].$lang['nc_colon'];?></dt>
        <dd>
          <div class="member-avatar-thumb"><img src="<?php echo getMemberAvatar($output['member_avatar']).'?'.microtime(); ?>" alt="" nc_type="avatar" /></div>
          <p class="hint mt5"><?php echo $lang['nc_member_avatar_hint'];?></p>
        </dd>
      </dl>
      <dl>
        <dt><?php echo $lang['home_member_change_avatar'].$lang['nc_colon'];?></dt>
        <dd>
          <div class="upload-btn"> <a href="javascript:void(0);"> <span>
            <input type="file" name="pic" id="pic" multiple=""  file_id="0" class="file" size="1" hidefocus="true" maxlength="0" />
            </span>
            <div class="upload-button"><?php echo $lang['home_member_avatar_upload'];?></div>
            <input id="submit_button" style="display:none" type="button" value="&nbsp;" onClick="submit_form($(this))" />
            </a></div>
        </dd>
      </dl>
    </div>
  </form>
  <?php }else{?>
  <form action="index.php?act=home&op=cut" id="form_cut" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" id="x1" name="x1" />
    <input type="hidden" id="x2" name="x2" />
    <input type="hidden" id="w" name="w" />
    <input type="hidden" id="y1" name="y1" />
    <input type="hidden" id="y2" name="y2" />
    <input type="hidden" id="h" name="h" />
    <input type="hidden" id="newfile" name="newfile" value="<?php echo $output['newfile'];?>" />
    <div class="pic-cut-120">
      <div class="work-title"><?php echo $lang['nc_comm_workarea'];?></div>
      <div class="work-layer">
        <p><img id="nccropbox" src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_AVATAR.DS.$output['newfile'].'?'.microtime(); ?>"/></p>
      </div>
      <div class="thumb-title"><?php echo $lang['nc_comm_cut_view'];?></div>
      <div class="thumb-layer">
        <p><img id="preview" src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_AVATAR.DS.$output['newfile'].'?'.microtime();?>"/></p>
      </div>
      <div class="cut-help">
        <h4><?php echo $lang['nc_comm_op_help'];?></h4>
        <p><?php echo $lang['nc_comm_op_help_tip'];?></p>
      </div>
      <div class="cut-btn">
        <input type="button" id="ncsubmit" class="submit" value="<?php echo $lang['nc_submit'];?>" />
      </div>
    </div>
  </form>
  <?php }?>
</div>

<script type="text/javascript">
$(function(){
<?php if ($output['newfile'] != ''){?>
	function showPreview(coords)
	{
		if (parseInt(coords.w) > 0){
			var rx = 120 / coords.w;
			var ry = 120 / coords.h;
			$('#preview').css({
				width: Math.round(rx * <?php echo $output['width'];?>) + 'px',
				height: Math.round(ry * <?php echo $output['height'];?>) + 'px',
				marginLeft: '-' + Math.round(rx * coords.x) + 'px',
				marginTop: '-' + Math.round(ry * coords.y) + 'px'
			});
		}
		$('#x1').val(coords.x);
		$('#y1').val(coords.y);
		$('#x2').val(coords.x2);
		$('#y2').val(coords.y2);
		$('#w').val(coords.w);
		$('#h').val(coords.h);
	}
    $('#nccropbox').Jcrop({
	aspectRatio:1,
	setSelect: [ 0, 0, 120, 120 ],
	minSize:[50, 50],
	allowSelect:0,
	onChange: showPreview,
	onSelect: showPreview
    });
	$('#ncsubmit').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			$('#form_cut').submit();
		}
	});
<?php }else{?>
	$('#pic').change(function(){
		var filepatd=$(this).val();
		var extStart=filepatd.lastIndexOf(".");
		var ext=filepatd.substring(extStart,filepatd.lengtd).toUpperCase();		
		if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
			alert("file type error");
			$(this).attr('value','');
			return false;
		}
		if ($(this).val() == '') return false;
		$("#form_avaster").submit(); 
	});
<?php }?>
});	
</script>