$(function() {
    $(".head-invoice").click(function (){
        $(this).parent().find(".inv-tlt-sle").prop("checked",true);
    });
    $(".buys1-edit-address").click(function(){
        var self = this;
        hideDetail(self);
    });
    $(".buys1-edit-invoice").click(function(){
        var self = this;
        hideDetail(self);
    });
    $(".save-address").click(function (){
        var self = this;
        //ajax 提交添加发票信息
        //.....
        showDetail(self);
    });
    $(".save-invoice").click(function (){
        var self = this;
        //ajax 提交添加发票信息
        //.....
        showDetail(self);
    });
    $(".no-invoice").click(function (){
        var self = this;
        showDetail(self);
    });
    function hideDetail(self){
        $(self).hide();
        $(self).parents(".buys1-cnt").find(".buys1-hide-detail").addClass("hide");
        $(self).parents(".buys1-cnt").find(".buys1-hide-list").removeClass("hide");
    }
    function showDetail(self){
        $(self).parents(".buys1-cnt").find(".buys1-edit-btn").show();
        $(self).parents(".buys1-cnt").find(".buys1-hide-list").addClass("hide");
        $(self).parents(".buys1-cnt").find(".buys1-hide-detail").removeClass("hide");
    }
});