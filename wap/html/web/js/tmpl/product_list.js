$(function (){
    $(".page-warp").click(function (){
        $(this).find(".pagew-size").toggle();
    });
    $(".product-filter a").click(function (){
        $(this).addClass("current").siblings().removeClass("current");
    });
});