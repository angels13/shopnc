$(function (){
    $(".head-invoice").click(function (){
        $(this).parent().find(".inv-tlt-sle").prop("checked",true);
    });
});