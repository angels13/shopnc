$(function (){
    $(".invoice_list_wp li label").click(function (){
        if($(this).find(".invchk").is(":checked")){
            $(this).parent().addClass("current").siblings().removeClass("current");
        }
    });
});