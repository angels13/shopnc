//首页焦点广告图切换
(function($) {
	$.fn.jfocus = function(settings) {
		var defaults = {
			time: 5000
		};
		var settings = $.extend(defaults, settings);
		return this.each(function(){
			var $this = $(this);
			var sWidth = $this.width();
			var len = $this.find("ul li").length;
			var index = 0;
			var picTimer;
			var btn = "<div class='pagination'>";
			for (var i = 0; i < len; i++) {
				btn += "<span></span>";
			}
			btn += "</div><div class='arrow pre'></div><div class='arrow next'></div>";
			$this.append(btn);
			$this.find(".pagination span").css("opacity", 0.4).mouseenter(function() {
				index = $this.find(".pagination span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseenter");
			$this.find(".arrow").css("opacity", 0.0).hover(function() {
				$(this).stop(true, false).animate({
					"opacity": "0.5"
				},
				300);
			},
			function() {
				$(this).stop(true, false).animate({
					"opacity": "0"
				},
				300);
			});
			$this.find(".pre").click(function() {
				index -= 1;
				if (index == -1) {
					index = len - 1;
				}
				showPics(index);
			});
			$this.find(".next").click(function() {
				index += 1;
				if (index == len) {
					index = 0;
				}
				showPics(index);
			});
			$this.find("ul").css("width", sWidth * (len));
			$this.hover(function() {
				clearInterval(picTimer);
			},
			function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if (index == len) {
						index = 0;
					}
				},
				settings.time);
			}).trigger("mouseleave");
			function showPics(index) {
				var nowLeft = -index * sWidth;
				$this.find("ul").stop(true, false).animate({
					"left": nowLeft
				},
				300);
				$this.find(".pagination span").stop(true, false).animate({
					"opacity": "0.4"
				},
				300).eq(index).stop(true, false).animate({
					"opacity": "1"
				},
				300);
			}
		});
	}
//首页标准模块中间多图广告鼠标触及凸显
	$.fn.jfade = function(settings) {
		var defaults = {
			start_opacity: "1",
			high_opacity: "1",
			low_opacity: ".1",
			timing: "500"
		};
		var settings = $.extend(defaults, settings);
		settings.element = $(this);
		//set opacity to start
		$(settings.element).css("opacity", settings.start_opacity);
		//mouse over
		$(settings.element).hover(
		//mouse in
		function() {
			$(this).stop().animate({
				opacity: settings.high_opacity
			},
			settings.timing); //100% opacity for hovered object
			$(this).siblings().stop().animate({
				opacity: settings.low_opacity
			},
			settings.timing); //dimmed opacity for other objects
		},
		//mouse out
		function() {
			$(this).stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); //return hovered object to start opacity
			$(this).siblings().stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); // return other objects to start opacity
		});
		return this;
	}
})(jQuery);
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".time-remain").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
                var days = Math.floor(tms / (1 * 60 * 60 * 24));
                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
                var minutes = Math.floor(tms / (1 * 60)) % 60;
                var seconds = Math.floor(tms / 1) % 60;
                
                if (days < 0) days = 0;
                if (hours < 0) hours = 0;
                if (minutes < 0) minutes = 0;
                if (seconds < 0) seconds = 0;
                obj.find("[time_id='d']").html(days);
                obj.find("[time_id='h']").html(hours);
                obj.find("[time_id='m']").html(minutes);
                obj.find("[time_id='s']").html(seconds);
                obj.attr("count_down",tms);
	        }
	    });
	}
	function DOTCHANGE() {
		var changenow = $(this).index();
		$('#fullScreenSlides li').eq(nownow).css('z-index', '900');
		$('#fullScreenSlides li').eq(changenow).css({
			'z-index': '800'
		}).show();
		pagination.eq(changenow).addClass('current').siblings('li').removeClass('current');
		$('#fullScreenSlides li').eq(nownow).fadeOut(400,
		function() {
			$('#fullScreenSlides li').eq(changenow).fadeIn(500);
		});
		nownow = changenow;
	}
	function ADDLI() {
		for (var i = 0; i <= numpic; i++) {
			ulcontent += '<li>' + '<a href="#">' + (i + 1) + '</a>' + '</li>';
		}
		$('#fullScreenSlides').after(ulstart + ulcontent + ulend);
	}
	function GOGO() {
		var NN = nownow + 1;
		if (inout == 1) {} else {
			if (nownow < numpic) {
				$('#fullScreenSlides li').eq(nownow).css('z-index', '900');
				$('#fullScreenSlides li').eq(NN).css({
					'z-index': '800'
				}).show();
				pagination.eq(NN).addClass('current').siblings('li').removeClass('current');
				$('#fullScreenSlides li').eq(nownow).fadeOut(400,
				function() {
					$('#fullScreenSlides li').eq(NN).fadeIn(500);
				});
				nownow += 1;
			} else {
				NN = 0;
				$('#fullScreenSlides li').eq(nownow).css('z-index', '900');
				$('#fullScreenSlides li').eq(NN).stop(true, true).css({
					'z-index': '800'
				}).show();
				$('#fullScreenSlides li').eq(nownow).fadeOut(400,
				function() {
					$('#fullScreenSlides li').eq(0).fadeIn(500);
				});
				pagination.eq(NN).addClass('current').siblings('li').removeClass('current');
				nownow = 0;
			}
		}
		TT = setTimeout(GOGO, SPEED);
	}
    //首页焦点区满屏背景广告切换
	var numpic = $('#fullScreenSlides li').size() - 1;
	var nownow = 0;
	var inout = 0;
	var TT = 0;
	var SPEED = 5000;
	$('#fullScreenSlides li').eq(0).siblings('li').css({
		'display': 'none'
	});
	var ulstart = '<ul id="SlidesPagination" class="full-screen-slides-pagination">',
	ulcontent = '',
	ulend = '</ul>';
	ADDLI();
	var pagination = $('#SlidesPagination li');
	var paginationwidth = $('#SlidesPagination').width();
	$('#SlidesPagination').css('margin-left', (372 - paginationwidth))

	pagination.eq(0).addClass('current')
	pagination.on('click', DOTCHANGE)
	pagination.mouseenter(function() {
		inout = 1;
	})
	pagination.mouseleave(function() {
		inout = 0;
	})
	TT = setTimeout(GOGO, SPEED);
$(function(){
	setTimeout("takeCount()", 1000);
    //首页Tab标签卡滑门切换
    $(".tabs-nav > li > h3").bind('mouseover', (function(e) {
    	if (e.target == this) {
    		var tabs = $(this).parent().parent().children("li");
    		var panels = $(this).parent().parent().parent().children(".tabs-panel");
    		var index = $.inArray(this, $(this).parent().parent().find("h3"));
    		if (panels.eq(index)[0]) {
    			tabs.removeClass("tabs-selected").eq(index).addClass("tabs-selected");
    			panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
    		}
    	}
    }));

	$('.jfocus-trigeminy > ul > li > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "200"
	});
	$('.fade-img > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});
	$('.middle-goods-list > ul > li').jfade({
		start_opacity: "0.9",
		high_opacity: "1",
		low_opacity: ".25",
		timing: "500"
	});	
	$('.recommend-brand > ul > li').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});
	$(".jfocus-trigeminy").jfocus();
	$(".right-side-focus").jfocus();
	$(".groupbuy").jfocus({time:8000});
	$("#saleDiscount").jfocus({time:8000});
})