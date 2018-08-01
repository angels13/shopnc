<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="wrap">
  <div class="tabmenu">
    <ul class="tab">
      <li class="active"><a href="javascript:void(0)"><?php echo $lang['inform_page_title']; ?></a></li>
    </ul>
  </div>
  <div class="ncu-form-style">
    <div id="warning"></div>
    <form id="add_form" enctype="multipart/form-data" method="post" action="index.php?act=member_inform&op=inform_save">
      <input name="inform_goods_id" type="hidden" value=<?php echo $output['goods_info']['goods_id']; ?> />
      <dl>
        <dt><?php echo $lang['inform_goods_name'].$lang['nc_colon'];?></dt>
        <dd> <a href="<?php echo urlShop('goods', 'index',array('goods_id'=>$output['goods_info']['goods_id']));?>" target="_blank"> <?php echo $output['goods_info']['goods_name']; ?> </a> </dd>
      </dl>
      <dl>
        <dt><?php echo $lang['inform_type'].$lang['nc_colon'];?></dt>
        <dd>
          <ul>
            <?php foreach($output['type_list'] as $inform_type) {?>
            <li>
              <p>
                <input type='radio' name="inform_subject_type"  
                value ="<?php echo $inform_type['inform_type_id'].','.$inform_type['inform_type_name'];?>">
                <?php echo $inform_type['inform_type_name'];?>
                </input>
              </p>
              <p class="hint"><?php echo $inform_type['inform_type_desc'];?></p>
            </li>
            <?php } ?>
          </ul>
        </dd>
      </dl>
      <dl>
        <dt><?php echo $lang['inform_subject'].$lang['nc_colon'];?></dt>
        <dd>
          <select id="inform_subject" name="inform_subject">
          </select>
        </dd>
      </dl>
      <dl>
        <dt><?php echo $lang['inform_content'].$lang['nc_colon'];?></dt>
        <dd>
          <textarea id="inform_content" name="inform_content" ></textarea>
        </dd>
      </dl>
      <dl class="noborder">
        <dt><?php echo $lang['inform_pic'].$lang['nc_colon'];?></dt>
        <dd>
          <p class="mb5">
            <input id="inform_pic1" name="inform_pic1" type="file"  />
          </p>
          <p class="mb5">
            <input id="inform_pic2" name="inform_pic2" type="file"  />
          </p>
          <p class="mb5">
            <input id="inform_pic3" name="inform_pic3" type="file"  />
          </p>
          <p class="hint"><?php echo $lang['inform_pic_error'];?></p>
        </dd>
      </dl>
      <dl class="bottom">
        <dt>&nbsp;</dt>
        <dd>
          <input id="btn_inform_submit" type="submit" class="submit" value="<?php echo $lang['nc_ok'];?>" />
        </dd>
      </dl>
    </form>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.selectboxes.pack.js" charset="utf-8" ></script> 
<script type="text/javascript">
$(document).ready(function(){
    $("#btn_inform_submit").attr('disabled',true);
    //默认选中第一个radio
    $(":radio").first().attr("checked",true);
    bindInformSubject($(":radio").first().val());
    $(":radio").click(function(){
        bindInformSubject($(this).val());
    });
    //页面输入内容验证
	$("#add_form").validate({
		errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
    	submitHandler:function(form){
    		ajaxpost('add_form', '', '', 'onerror') 
    	},
        rules : {
        	inform_content : {
                required : true,
                maxlength : 100
            },
        	inform_subject: {
                required : true
            },
            inform_pic1 : {
                accept : 'jpg|jpeg|gif|png' 
            },
            inform_pic2 : {
                accept : 'jpg|jpeg|gif|png' 
            },
            inform_pic3 : {
                accept : 'jpg|jpeg|gif|png' 
            }
        },
        messages : {
	    	inform_content : {
                required : '<?php echo $lang['inform_content_null'];?>',
                maxlength : '<?php echo $lang['inform_content_null'];?>'
            },
        	inform_subject: {
                required : '<?php echo $lang['inform_subject_select'];?>'
            },
            inform_pic1: {
                accept : '<?php echo $lang['inform_pic_error'];?>'
            },
            inform_pic2: {
                accept : '<?php echo $lang['inform_pic_error'];?>'
            },
            inform_pic3: {
                accept : '<?php echo $lang['inform_pic_error'];?>'
            }
        }
	});
    
});
function bindInformSubject(key) {
    type_id = key.split(",")[0];
    $("#inform_subject").empty();
    $.ajax({
        type:'POST',
        url:'index.php?act=member_inform&op=get_subject_by_typeid',
        cache:false,
        data:'typeid='+type_id,
        dataType:'json',
        success:function(type_list){
            $("#btn_inform_submit").attr('disabled',false);
            if(type_list.length >= 1) {
                $("#inform_subject").addOption('','<?php echo $lang['nc_please_choose'];?>');
                for(var i = 0; i < type_list.length; i++)
                {
                    $("#inform_subject").addOption(type_list[i].inform_subject_id+","+type_list[i].inform_subject_content,type_list[i].inform_subject_content);
                }
                $("#inform_subject").selectOptions('');
            }
            else {
                $("#btn_inform_submit").attr('disabled',true);
                alert("<?php echo $lang['inform_subject_null'];?>");
            }
            
        }
	});
}
</script> 
