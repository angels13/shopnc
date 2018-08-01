$(function (){
	function addPaginat(){
		var swipeSpan= '<span class="swipe-paginat-switch current"></span>';
		var swipeItem = $("#mySwipe .swipe-item");
		for(var i = 1;i< swipeItem.length;i++){
			swipeSpan += '<span class="swipe-paginat-switch"></span>';
		}
		$(".swipe-paginat").html(swipeSpan);
	}
	function mySwipe(){
		// pure JS
		var elem = $("#mySwipe")[0];
		window.mySwipe = Swipe(elem, {
		  auto: 3000,
		  continuous: true,
		  disableScroll: true,
		  stopPropagation: true,
		  callback: function(index, element) {
		  	var paginat = $(".swipe-paginat-switch");
		  	paginat.eq(index).addClass("current").siblings().removeClass("current");
		  }
		});
	}
	$.ajax({
		url:ApiUrl+"/index.php?act=index",
		type:'get',
		dataType:'json',
		success:function(result){
			var rData =  result.datas;
			rData.WapSiteUrl = WapSiteUrl;
			var home2 = rData.home2;
			var homeMap = [];
			for(var i=0;2*i<home2.length;i++){
				homeMap.push([home2[2*i],home2[2*i+1]]);
			}
			rData.homeMap = homeMap;
			var html = template.render('home_body',rData);
			$("#home-cnt-wp").html(html);
			
			addPaginat();
			mySwipe();
			
			$('.home1').click(function(){
				var keyword = encodeURIComponent($(this).attr('keyword'));
				location.href = WapSiteUrl+'/tmpl/product_list.html?keyword='+keyword;
			});
			
			$('.home2').click(function(){
				var keyword = encodeURIComponent($(this).attr('keyword'));
				location.href = WapSiteUrl+'/tmpl/product_list.html?keyword='+keyword;
			});
		}
	});
	
	$('.search-btn').click(function(){
		var keyword = encodeURIComponent($('#keyword').val());
		location.href = WapSiteUrl+'/tmpl/product_list.html?keyword='+keyword;
	});
});