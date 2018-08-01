<div class="eject_con">
  <div id="warning"></div>
  <form id="post_form" method="post" action="index.php?act=member_complain&op=complain_add_pic&complain_id=<?php echo $output['complain_info']['complain_id']; ?>" enctype="multipart/form-data">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['complain_evidence_upload'];?><span class="error">(<?php echo $lang['complain_pic_error'];?>)</dt>
      <dd>&nbsp;</dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <p>
          <input id="input_complain_pic1" name="input_complain_pic1" type="file" />
        </p>
        <p>
          <input id="input_complain_pic2" name="input_complain_pic2" type="file" />
        </p>
        <p>
          <input id="input_complain_pic3" name="input_complain_pic3" type="file" />
        </p>
      </dd>
    </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
        <input type="submit" class="submit" id="confirm_button" value="<?php echo $lang['complain_text_submit'];?>" />
      </dd>
    </dl>
  </form>
</div>
<script type="text/javascript">
$(function(){
    $('#cancel_button').click(function(){
        DialogManager.close('complain_add_pic');
    });
    $('#post_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
    	submitHandler:function(form){
    		ajaxpost('post_form', '', '', 'onerror') 
    	},
            rules : {
                input_complain_pic1 : {
                    accept : 'jpg|jpeg|gif|png' 
                },
                input_complain_pic2 : {
                    accept : 'jpg|jpeg|gif|png' 
                },
                input_complain_pic3 : {
                    accept : 'jpg|jpeg|gif|png' 
                }
            },
                messages : {
                    input_complain_pic1: {
                        accept : '<?php echo $lang['complain_pic_error'];?>'
                    },
                    input_complain_pic2: {
                        accept : '<?php echo $lang['complain_pic_error'];?>'
                    },
                    input_complain_pic3: {
                        accept : '<?php echo $lang['complain_pic_error'];?>'
                    }
                }
    });
});
</script> 
