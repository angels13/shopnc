  var screen_max = 5;//大图数
  var focus_max = 5;//小图组数
  var pic_max = 3;//组内小图数
  var screen_obj = {};
  var focus_obj = {};
$(function(){
    $('#screen_color').colorpicker({showOn:'both'});
    $('#screen_color').parent().css("width",'');
    $('#screen_color').parent().addClass("color");
	$(".type-file-file").change(function() {//初始化图片上传控件
		$(this).prevAll(".type-file-text").val($(this).val());
	});
	$("#homepageFocusTab .tab-menu li").click(function() {//切换
	    var pic_form = $(this).attr("form");
	    $('form').hide();
	    $("#homepageFocusTab li").removeClass("current");
	    $('#'+pic_form).show();
	    $(this).addClass("current");
	});
	screen_obj = $("#upload_screen_form");
	screen_obj.find("ul").sortable({ items: 'li' });
	focus_obj = $("#upload_focus_form");
	focus_obj.find(".focus-trigeminy").sortable({ items: 'div[focus_id]' });
	focus_obj.find("ul").sortable({ items: 'li' });
});

//焦点区切换大图上传
function add_screen() {//增加图片
	for (var i = 1; i <= screen_max; i++) {//防止数组下标重复
		if (screen_obj.find("li[screen_id='"+i+"']").size()==0) {//编号不存在时添加
    	    var text_input = '';
    	    text_input += '<input name="screen_list['+i+'][pic_id]" value="'+i+'" type="hidden">';
    	    text_input += '<input name="screen_list['+i+'][pic_name]" value="" type="hidden">';
    	    text_input += '<input name="screen_list['+i+'][pic_url]" value="" type="hidden">';
    	    text_input += '<input name="screen_list['+i+'][color]" value="" type="hidden">';
    	    text_input += '<input name="screen_list['+i+'][pic_img]" value="" type="hidden">';
			var add_html = '';
			add_html = '<li screen_id="'+i+'" onclick="select_screen('+i+');" title="可上下拖拽更改显示顺序"><a class="del" href="JavaScript:del_screen('+i+
			');" title="删除">X</a><div class="focus-thumb" title="点击编辑选中区域内容"><img src="" /></div>'+text_input+'</li>';
			screen_obj.find("ul").append(add_html);
			select_screen(i);
			break;
		}
    }
}
function screen_pic(pic_id,pic_img) {//更新图片
	if (pic_img!='') {
	    var color = screen_obj.find("input[name='screen_pic[color]']").val();
	    var pic_name = screen_obj.find("input[name='screen_pic[pic_name]']").val();
	    var pic_url = screen_obj.find("input[name='screen_pic[pic_url]']").val();
	    var obj = screen_obj.find("li[screen_id='"+pic_id+"']");
	    obj.find("img").attr("src",UPLOAD_SITE_URL+'/'+pic_img);
        obj.find("input[name='screen_list["+pic_id+"][pic_name]']").val(pic_name);
        obj.find("input[name='screen_list["+pic_id+"][pic_url]']").val(pic_url);
        obj.find("input[name='screen_list["+pic_id+"][color]']").val(color);
        obj.find("input[name='screen_list["+pic_id+"][pic_img]']").val(pic_img);
	    obj.css("background-color",color);
	}
}
function select_screen(pic_id) {//选中图片
    var obj = screen_obj.find("li[screen_id='"+pic_id+"']");
    var pic_name = obj.find("input[name='screen_list["+pic_id+"][pic_name]']").val();
    var pic_url = obj.find("input[name='screen_list["+pic_id+"][pic_url]']").val();
    var color = obj.find("input[name='screen_list["+pic_id+"][color]']").val();
    screen_obj.find("li").removeClass("selected");
    $("input[name='screen_id']").val(pic_id);
    $("input[name='screen_pic[pic_name]']").val(pic_name);
    $("input[name='screen_pic[pic_url]']").val(pic_url);
    $("input[name='screen_pic[color]']").val(color);
    screen_obj.find(".type-file-file").val('');
    screen_obj.find(".type-file-text").val('');
    screen_obj.find("#upload_screen").show();
    screen_obj.find('.evo-pointer').css("background-color",color);
    obj.addClass("selected");
}
function del_screen(pic_id) {//删除图片
    if (screen_obj.find("li").size()<2) {
         return;//保留一个
    }
	screen_obj.find("li[screen_id='"+pic_id+"']").remove();
	var slide_id = screen_obj.find("input[name='screen_id']").val();
	if (pic_id==slide_id) {
    	screen_obj.find("input[name='screen_id']").val('');
    	screen_obj.find("#upload_screen").hide();
	}
}

//焦点区切换小图上传
function add_focus() {//增加
	for (var i = 1; i <= focus_max; i++) {//防止数组下标重复
		if (focus_obj.find("div[focus_id='"+i+"']").size()==0) {//编号不存在时添加
			var add_html = '';
			var text_append = '';
			for (var pic_id = 1; pic_id <= pic_max; pic_id++) {
			    text_append += '<li pic_id="'+pic_id+'" onclick="select_focus('+i+',this);" title="可左右拖拽更改图片排列顺序">';
				text_append += '<div class="focus-thumb">';
			    text_append += '<img title="" src=""/>';
				text_append += '</div>';
        	    text_append += '<input name="focus_list['+i+'][pic_list]['+pic_id+'][pic_id]" value="'+pic_id+'" type="hidden">';
        	    text_append += '<input name="focus_list['+i+'][pic_list]['+pic_id+'][pic_name]" value="" type="hidden">';
        	    text_append += '<input name="focus_list['+i+'][pic_list]['+pic_id+'][pic_url]" value="" type="hidden">';
        	    text_append += '<input name="focus_list['+i+'][pic_list]['+pic_id+'][pic_img]" value="" type="hidden">';
			    text_append += '</li>';
			}
			add_html = '<div focus_id="'+i+'" class="focus-trigeminy-group" title="可上下拖拽更改显示顺序"><a class="del" href="JavaScript:del_focus('+i+');" title="删除">X</a>'+
    			'<ul>'+text_append+'</ul></div>';
			
			focus_obj.find("#btn_add_list").before(add_html);
			focus_obj.find("div ul").sortable({ items: 'li' });
			focus_obj.find("div[focus_id='"+i+"'] li[pic_id='1']").trigger("click");//默认选中第一个图片
			break;
		}
	}
}
function select_focus(focus_id,pic) {//选中图片
    var obj = $(pic);
    var pic_id = obj.attr("pic_id");
    var pic_name = obj.find("input[name*='[pic_name]']").val();
    var pic_url = obj.find("input[name*='[pic_url]']").val();
    focus_obj.find("li").removeClass("selected");
    focus_obj.find("input[name='slide_id']").val(focus_id);
    focus_obj.find("input[name='pic_id']").val(pic_id);
    focus_obj.find("input[name='focus_pic[pic_name]']").val(pic_name);
    focus_obj.find("input[name='focus_pic[pic_url]']").val(pic_url);
    focus_obj.find(".type-file-file").val('');
    focus_obj.find(".type-file-text").val('');
    focus_obj.find("#upload_focus").show();
    obj.addClass("selected");
}
function focus_pic(pic_id,pic_img) {//更新图片
	if (pic_img!='') {
	    var focus_id = focus_obj.find("input[name='slide_id']").val();
	    var pic_name = focus_obj.find("input[name='focus_pic[pic_name]']").val();
	    var pic_url = focus_obj.find("input[name='focus_pic[pic_url]']").val();
	    var obj = focus_obj.find("div[focus_id='"+focus_id+"'] li[pic_id='"+pic_id+"']");
	    obj.find("img").attr("src",UPLOAD_SITE_URL+'/'+pic_img);
        obj.find("input[name*='[pic_name]']").val(pic_name);
        obj.find("input[name*='[pic_url]']").val(pic_url);
        obj.find("input[name*='[pic_img]']").val(pic_img);
    }
}
function del_focus(focus_id) {//删除切换组
    if (focus_obj.find("div[focus_id]").size()<2) {
         return;//保留一个
    }
	focus_obj.find("div[focus_id='"+focus_id+"']").remove();
	var slide_id = focus_obj.find("input[name='slide_id']").val();
	if (focus_id==slide_id) {
    	focus_obj.find("input[name='slide_id']").val('');
    	focus_obj.find("#upload_focus").hide();
	}
}