(function ($) {
	/*
		局部放大
	*/
	$.fn.showPartImg=function(options){
		if(!options)options={};
		var src=$(this).attr('bigsrc');
		src=src?src:$(this).attr('src');
		var imgW=$(this).width();
		var imgH=$(this).height();
		var width=options.width?options.width:(imgW+'px');
		var height=options.height?options.height:(imgH+'px');
		var x=options.x?options.x:($(this).width()+10+'px');
		var y=options.y?options.y:0;
		var imgoff=$(this).offset();
		var img=$(this);
	  
		img.wrap('<div style="position:relative;"></div>');
		img.parent().append('<div class="bigimgdiv" style="visibility:hidden;position:absolute;left:'+x+';top:'+y+';width:'+width+';height:'+height+';overflow:hidden;background:url('+src+') no-repeat;"><img style="display:none;width:auto;height:auto;" src="'+src+'"/></div>');
		img.parent().find('.bigimgdiv img').load(function(){
			var bigimgW=img.parent().find('.bigimgdiv img').width();
			var bigimgH=img.parent().find('.bigimgdiv img').height();
			var moveW=((parseInt)(width))*imgW/bigimgW;
			var moveH=((parseInt)(height))*imgH/bigimgH;
			img.parent().append('<div class="bigimgmove" style="left:0;top:0;border:1px solid #ccc;background-color:#eee;position:absolute;width:'+moveW+'px;height:'+moveH+'px;display:none;">&nbsp;</div>');
			img.parent().find('.bigimgmove').css({'opacity':'0.6','cursor':'pointer'});
			/* 构造原图完毕 */
	   
			img.parent().mouseover(function(){
				$(this).find('.bigimgmove').css('display','block');
				$(this).find('.bigimgdiv').css('visibility','visible');
	    
			});
			img.parent().mouseout(function(){
				$(this).find('.bigimgmove').css('display','none');
				$(this).find('.bigimgdiv').css('visibility','hidden');
			});
			img.parent().mousemove(function(e){
				var bigimgmove=$(this).find('.bigimgmove');
				bigimgmove.css('display','block');
				var offX=bigimgmove.width()/2;
				var offY=bigimgmove.height()/2;
				var left=(window.event||e).clientX+document.documentElement.scrollLeft;
				var top=(window.event||e).clientY+document.documentElement.scrollTop;
				var imgleft=imgoff.left;
				var imgtop=imgoff.top;
				var truex=((left-offX)-imgleft)>0&&left<((imgleft-offX)+imgW);
				var truey=((top-offY)-imgtop)>0&&top<((imgtop-offY)+imgH);
				var bigimgpos=bigimgmove.position();
				if(truex){
					bigimgmove.css('left',left-offX-imgleft+'px');
				}
				if(truey){
					bigimgmove.css('top',top-offY-imgtop+'px');
				}
				if(((left)-imgleft)<0||left>((imgleft)+imgW)||((top)-imgtop)<0||top>((imgtop)+imgH)){
					bigimgmove.css('display','none');
				}
				$(this).find('.bigimgdiv').css('background-position','-'+bigimgW*bigimgpos.left/imgW+'px -'+bigimgH*bigimgpos.top/imgH+'px');
			});
			img.parent().find('.bigimgdiv').mousemove(function(){
				$(this).css('visibility','hidden'); 
			});
		});
	}
})(jQuery);