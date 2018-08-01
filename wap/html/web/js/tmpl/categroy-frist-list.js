$(function() {
	var tmpl = '<ul class="categroy-list">' + '<% for(var i = 0;i<class_list.length;i++){ %>' + '<li class="category-item" gc_id=<%=class_list[i].gc_id %> >' + '<div class="ci-fcategory-name"><%=class_list[i].gc_name %></div>' + '<div class="ci-fcategory-text"><%=class_list[i].text %></div>' + '<span class="grayrightarrow"></span>' + '</li>' + '<% } %>' + '</ul>';
	var data = {};
	$.ajax({
		url:"http://mobile.shopnc.v2014/index.php?act=goods_class",
		type:'get',
		jsonp:'callback',
		dataType:'jsonp',
		success:function(result){
			var data = result.datas;
			var render = template.compile(tmpl);
			var html = render(data);
			$("#categroy-cnt").html(html);
		}
	});
	$(document).on('click',".category-item",function (){
		window.location.href ="product_second_categroy.html";
	});
});