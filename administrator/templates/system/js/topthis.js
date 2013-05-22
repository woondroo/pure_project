/*
	作者：omese
	日期：2011-12-02
	topthis 置顶功能
	对应每个组件内的 topthis()功能
*/
$(document).ready(function(){
		$(".top-this").css("opacity",0);
		$(".adminlist tr").hover(function(){
			$(this).find(".top-this").fadeTo(300,1);
		},function(){
			$(this).find(".top-this").fadeTo(0,0);
		});
});