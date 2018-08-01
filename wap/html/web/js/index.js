$(function (){
	addPaginat();
	function addPaginat(){
		var swipeSpan= '<span class="swipe-paginat-switch current"></span>';
		var swipeItem = $("#mySwipe .swipe-item");
		for(var i = 1;i< swipeItem.length;i++){
			swipeSpan += '<span class="swipe-paginat-switch"></span>';
		}
		$(".swipe-paginat").html(swipeSpan);
	}
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
});