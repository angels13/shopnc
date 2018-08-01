<style>
.error {
	color:red;
}
</style>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/area_array.js" charset="utf-8"></script>
<div>
  <input type="hidden" name="form_submit" value="ok"/>
  <ul class="fill_in_content">
    <li>
      <p class="title"><span>*</span><?php echo $lang['pointcart_step1_receiver_name'];?></p>
      <p class="fill_in">
        <input type="text" name="consignee" maxlength="20" id="consignee" class="text1 formvalidatedata" value="<?php echo $output['address_info']['true_name']?$output['address_info']['true_name']:''; ?>"/>
        <span class="field_message explain"> <span class="field_notice"></span>
        <label class="error" generated="true"></label>
        </span> </p>
    </li>
    <li>
      <p class="title"><span>*</span><?php echo $lang['pointcart_step1_area'];?></p>
      <p class="fill_in"> <span id="region" class="group_validate">
        <select class="formvalidatedata">
        </select>
        <input type="hidden" value="0" name="city_id" id="city_id">
        <input type="hidden" name="areaid_hidden" id="areaid_hidden" value="0" class="formvalidatedata"/>
        <span style="font-weight:normal;" class="field_message explain"> <span class="field_notice"></span>
        <label class="error" generated="true"></label>
        </span>
        <input type="hidden" name="area_info" id="area_info"/>
        </span></p>
    </li>
    <li>
      <p class="title"><span>*</span><?php echo $lang['pointcart_step1_whole_address'];?></p>
      <p class="fill_in">
        <input type="text" name="address" id="address" maxlength="80" class="text1 formvalidatedata" style="width:300px;" value="<?php echo $output['address_info']['address']?$output['address_info']['address']:''; ?>"/>
        <span class="field_message explain"><span class="field_notice"><?php echo $lang['pointcart_step1_true_address'];?></span>
        <label class="error" generated="true"></label>
        </span></p>
    </li>
    <li>
      <p class="title"><?php echo $lang['pointcart_step1_phone_num'];?></p>
      <p class="fill_in">
        <input type="text" name="phone_tel" id="phone_tel" maxlength="20" class="text1 formvalidatedata" value="<?php echo $output['address_info']['tel_phone']?$output['address_info']['tel_phone']:''; ?>"/>
        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lang['cart_step1_mobile_num'];?>
        <input type="text" id="phone_mob" name="phone_mob" maxlength="15" class="text1 formvalidatedata" value="<?php echo $output['address_info']['mob_phone']?$output['address_info']['mob_phone']:''; ?>"/>
        <span class="field_message explain"> <span class="field_notice"><?php echo $lang['pointcart_step1_telphone'];?></span>
        <label class="error" generated="true"></label>
        </span> </p>
    </li>
  </ul>
  <div class="clear"></div>
  <div class="submit">
    <button class="btn" onclick="javascript:submitaddress();"><?php echo $lang['pointcart_step1_addnewaddress_submit'];?></button>
  </div>
</div>
<script type="text/javascript">
//<!CDATA[
$(function(){	
	regionInit("region");
	//输入信息改变时隐藏错误信息
	$(".formvalidatedata").live('change',function(){
		var _message_box = $(this).parent().find('.field_message');
	    _message_box.find('.field_notice').show();
	    _message_box.find('.error').hide();
	});	
	<?php if (!empty($output['address_info']['area_info_arr'])){?>
		<?php foreach ($output['address_info']['area_info_arr'] as $k=>$v){?>
			var key = parseInt(<?php echo $k;?>)+1;
			$("#region > select:nth-child("+key+")").find("option:contains('<?php echo $v;?>')").attr("selected","selected");
			$("#region > select:nth-child("+key+")").trigger("change");
		<?php }?>
	<?php }?>
});

function submitaddress(){
	var result = true;
	var consignee = $.trim($("#consignee").val());
	if (consignee == ''){
		showerror($("#consignee"),'<?php echo $lang['pointcart_step1_input_true_name'];?>');
		if(result == true){
			result = false;
		}
	}
	//判断地址是否选到了最后一级和是否选择了地址
	var areaid_hidden = $("#areaid_hidden").val();
	var city_id = $('#city_id').val();
	if (typeof(nc_a[areaid_hidden]) == 'object' && nc_a[areaid_hidden].length > 0){
		showerror($("#areaid_hidden"),'<?php echo $lang['pointcart_step1_choose_area'];?>');
		if(result == true){
			result = false;
		}
	}
	var area_info = $.trim($("#area_info").val());
	var address = $.trim($("#address").val());
	if (address == 0){
		showerror($("#address"),'<?php echo $lang['pointcart_step1_true_address'];?>');
		if(result == true){
			result = false;
		}
	}
	var phone_tel = $.trim($("#phone_tel").val());
	var phone_mob = $.trim($("#phone_mob").val());
	if(phone_tel == '' && phone_mob == ''){
		showerror($("#phone_mob"),'<?php echo $lang['pointcart_step1_telphone'];?>');
		if(result == true){
			result = false;
		}
	}
	if(phone_mob != '' && phone_mob.length != 11){
		showerror($("#phone_mob"),'<?php echo $lang['pointcart_step1_telphone'];?>');
		if(result == true){
			result = false;
		}
	}
	if(result == true){
		//提交表单
		var datastr = {'consignee':consignee,'areaid':areaid_hidden,'city_id':city_id,'area_info':area_info,'address':address,'phone_tel':phone_tel,'phone_mob':phone_mob,'form_submit':'ok'}
		$.getJSON('index.php?act=pointcart&op=newaddress',datastr, function(data){
            if (data.done)
            {
                //添加成功后关闭弹出框
            	//删除已有地址选中样式
            	$("#addresslist > ul").removeClass('selected_address');
            	$("#addresslist").find(":checked").removeAttr("checked");
            	var address_one = $("#addressone_model > ul").clone();
            	var sp = '&nbsp;&nbsp;';
            	var content = '<input type="radio" value="'+data.id+'" city_id="'+city_id+'" name="address_options" checked="checked" id="address_'+data.id+'">'+sp;
            	content += area_info+'\t'+address+'\t'+consignee+"<?php echo $lang['cart_step1_receiver_shou'];?>"+sp;
            	if(phone_mob != ''){
            		content += phone_mob;
            	}else{
            		content += phone_tel;
            	}
            	address_one.children().eq(1).html(content);
            	address_one.children().eq(1).attr('address',area_info+'&nbsp;&nbsp;'+address);
            	if (phone_mob =='') phone_mob = phone_tel;
            	address_one.children().eq(1).attr('buyer',consignee+'&nbsp;&nbsp;'+phone_mob);
				$('#addresslist').find('.goto').html('&nbsp;');
            	address_one.addClass('selected_address');
            	$("#addresslist").append(address_one);
            	$("#chooseaddressid").val(data.id);
            	DialogManager.close('newaddressform');
            	$('.address_item').last().click();
            }
            else
            {
            	//添加失败
                alert(data.msg);
            }
        });
	}
}
function showerror(obj,msg){
	var _message_box = $(obj).parent().find('.field_message');
    _message_box.find('.field_notice').hide();
    _message_box.find('.error').show();
    _message_box.find('.error').html(msg);
}
//添加新地址
function regionChange()
{
    //删除后面的select
    $(this).nextAll("select").remove();    
    //计算当前选中的地区id和name追加进入names数组
    var selects = $(this).siblings("select").andSelf();//获得select的同胞元素及其本身
    var id = 0;
    var names = new Array();    
    for (i = 0; i < selects.length; i++)
    {
        sel = selects[i];
        if (sel.value > 0)
        {
            id = sel.value;
            name = sel.options[sel.selectedIndex].text;
            names.push(name);
        }
    }
    //将最后一级地区信息保存进隐藏域
    $("#areaid_hidden").val(id);
    //将全部地区信息保存进隐藏域
    $("#area_info").val(names.join("\t"));
    //将市级ID放入隐藏域
    $('#city_id').val($('select[class="formvalidatedata"]').eq(1).val());
    //请求下级地区
    if (this.value > 0){
        var area_id = this.value;
        if(typeof(nc_a[area_id]) == 'object' && nc_a[area_id].length > 0){//数组存在
        	$("<select class='formvalidatedata'></select>").change(regionChange).insertAfter(this);
        	areaInit($(this).next("select"),area_id);//初始化地区
        }
    }
}
//]]>
</script>