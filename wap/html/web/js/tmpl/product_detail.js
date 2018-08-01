$(function (){
     // pure JS
    var elem = $("#mySwipe")[0];
    window.mySwipe = Swipe(elem, {
      continuous: true,
      disableScroll: true,
      stopPropagation: true,
      callback: function(index, element) {
        $(".pds-cursize").html(index+1);
      }
    });
    $(".pddcp-arrow").click(function (){
      $(this).parents(".pddcp-one-wp").toggleClass("current");
    });
    $(".pddc-stock a,.fixed-tab-wp .tab-item").click(function (){
      $(this).addClass("current").siblings().removeClass("current");
    });
});