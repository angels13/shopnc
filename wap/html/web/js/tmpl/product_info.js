$(function (){
    $(".fixed-tab-wp .tab-item").click(function (){
      $(this).addClass("current").siblings().removeClass("current");
    });
});