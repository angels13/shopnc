$(function (){
    var memberHtml = '<a class="btn mr5" href="'+WapSiteUrl+'/tmpl/member/member.html?act=member">个人中心</a>';
    var act = GetQueryString("act");
    if(act && act == "member"){
        memberHtml = '<a class="btn mr5" id="logoutbtn" href="javascript:void(0);">注销账号</a>';
    }
    var tmpl = '<div class="footer">'
        +'<div class="footer-top">'
            +'<div class="footer-tleft">'+ memberHtml +'</div>'
            +'<a href="javascript:void(0);"class="gotop">'
                +'<span class="gotop-icon"></span>'
                +'<p>回顶部</p>'
            +'</a>'
        +'</div>'
        +'<div class="footer-content">'
            +'<p class="link">'
                +'<a href="'+SiteUrl+'" class="standard">标准版</a>'
                +'<a href="'+AndroidSiteUrl+'">下载Android客户端</a>'
            +'</p>'
            +'<p class="copyright">'
                +'版权所有 2007-2014 © 天津市网城天创科技有限责任公司'
            +'</p>'
        +'</div>'
    +'</div>';
	var render = template.compile(tmpl);
	var html = render();
	$("#footer").html(html);
    //回到顶部
    $(".gotop").click(function (){
        $(window).scrollTop(0);
    });
    var key = getcookie('key');
	$('#logoutbtn').click(function(){
		var username = getcookie('username');
		var key = getcookie('key');
		var client = 'wap';
		$.ajax({
			type:'get',
			url:ApiUrl+'/index.php?act=logout',
			data:{username:username,key:key,client:client},
			success:function(result){
				if(result){
					delCookie('username');
					delCookie('key');
					location.href = WapSiteUrl+'/tmpl/member/login.html';
				}
			}
		});
	});
});