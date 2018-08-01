<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="wrap pr">
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
  </div>
  <div class="ncu-form-style">
    <form method="post" id="send_form" action="index.php?act=home&op=savemsg">
      <input type="hidden" name="form_submit" value="ok" />
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_message_reveiver'].$lang['nc_colon'];?></dt>
        <dd>
          <p>
            <input type="text" class="w400 text" name="to_member_name" value="<?php echo $output['member_name']; ?>" <?php if (!empty($output['member_name'])){echo "readonly";}?>/></p>
          <p class ="hint"><?php echo $lang['home_message_separate'];?></p>
          <p><input type="radio" value="2" name="msg_type" checked="checked" /><?php echo $lang['home_message_open'];?>&nbsp;&nbsp;<input type="radio" name="msg_type" value="0" /><?php echo $lang['home_message_close'];?></p>
        </dd>
      </dl>
      <dl>
        <dt class="required"><em class="pngFix"></em><?php echo $lang['home_message_content'].$lang['nc_colon'];?></dt>
        <dd>
          <textarea name="msg_content" rows="3" class="w400"/></textarea>
          <p class ="error"></p>
        </dd>
      </dl>
      <dl class="bottom">
        <dt>&nbsp;</dt>
        <dd>
          <input type="submit" class="submit" value="<?php echo $lang['home_message_ensure_send'];?>" />
        </dd>
      </dl>
    </form>
  </div>
  <?php if ($output['member_name'] == ''){?>
  <div class="message-friend-list">
    <h3><?php echo $lang['home_message_friend'];?><em class="gray ml5">(<?php echo intval(count($output['friend_list'])); ?>)</em></h3>
    <ul>
      <?php if ($output['friend_list'] != '') { ?>
      <?php foreach ($output['friend_list'] as $val) { ?>
      <li><a href="javascript:void(0);" id="<?php echo $val['friend_tomname']; ?>" nc_type="to_member_name"><?php echo $val['friend_tomname']; ?></a></li>
      <?php } ?>
      <?php } else { ?>
      <li class="nomessage">&nbsp;<?php echo $lang['home_message_no_friends'];?></li>
      <?php } ?>
    </ul>
  </div>
  <?php }?>
</div>
<script type="text/javascript">
$(function(){
    $('a[nc_type="to_member_name"]').click(function (){
        var str = $('input[name="to_member_name"]').val();
        var id = $(this).attr('id');
        if(str.indexOf(id+',') < 0){
            doFriend(id+',', 'add');
        }else{
            doFriend(id, 'delete');
        }
    });
});
$(function(){
  $('#send_form').validate({
        errorPlacement: function(error, element){
            $(element).next('p').html(error);
        },
        submitHandler:function(form){
            ajaxpost('send_form', '', '', 'onerror') 
        },   
        rules : {
            to_member_name : {
                required   : true
            },
            msg_content : {
                required   : false
            }
        },
        messages : {
            to_member_name : {
                required : '<?php echo $lang['home_message_receiver_null'];?>.'
            },
            msg_content : {
                required   : '<?php echo $lang['home_message_content_null'];?>.'
            }
        }
    });
});
function doFriend(user_name, action){
    var input_name = $("input[name='to_member_name']").val();
    var key, i = 0;
    var exist = false;
    var arrOld = new Array();
    var arrNew = new Array();
    input_name = input_name.replace(/\uff0c/g,',');
    arrOld     = input_name.split(',');
    for(key in arrOld){
        arrOld[key] = $.trim(arrOld[key]);
        if(arrOld[key].length > 0){
            arrOld[key] == user_name &&  action == 'delete' ? null : arrNew[i++] = arrOld[key]; //剔除好友
            arrOld[key] == user_name ? exist = true : null; //判断好友是否已选
        }
    }
    if(action == 'delete' && arrNew !=''){
    	arrNew = arrNew+',';
    }
    if(!exist && action == 'add'){
        arrNew[i] = user_name;
    }
    $("input[name='to_member_name']").val(arrNew);
}
</script> 
