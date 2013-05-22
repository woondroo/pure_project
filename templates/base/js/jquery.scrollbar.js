$(document).ready(function(){
    $('.m-list-frame').each(function(){

		$(this).wrapInner('<div class="scrollcontent"></div>');
		var height=$(this).css('height');
		var scroll=$('<div style="height:'+height+';padding:12px 0 0 0;position:absolute;" class="jsscrollbar"><div class="scroll-bar" ></div></div>');
		$(this).css('position','relative');
		$(this).append(scroll);
		var contentH=$(this).find('.scrollcontent').height();
		var scrollH=$(this).height();
		if(scrollH>contentH)return;
		$(this).find('div.scroll-bar').drag({
			dragbody: $(this).find('div.scroll-bar'),
			fnPara:$(this).find('div.scroll-bar'),
			mousemoveFn:moveScrollbar,
			opacity: '1',
			onlyxy:'y',
			preventEvent:true
		});
		$(this).bind('mousewheel',
			function(e){//mousewheel DOMMouseScroll
				mouseScroll(e,this);
			}
		).bind('DOMMouseScroll',
			function(e){//mousewheel DOMMouseScroll
				mouseScroll(e,this);
			}
		);
    });
});

function mouseScroll(e,textarea){
    e.preventDefault();
    var offH=12;
    if(e.wheelDelta <= 0 || e.detail > 0){
		mouseWhell(true,offH,textarea);
    }else{
		mouseWhell(false,offH,textarea);
    }
}
function mouseWhell(opt,offH,textarea){
    var but=$(textarea).find('div.scroll-bar');
    var content=$(but).parent().parent().find('.scrollcontent');
    var scrollbutTop=$(but).position().top;
    var contentTop=$(content).position().top;
    var scrollbutH=$(but).height();
    var scrollbarH=$(but).parent().outerHeight(true);
    var contentH=$(content).outerHeight( true )-scrollbarH;
    if(opt&&(scrollbutTop+scrollbutH)>=scrollbarH){
		$(but).css('top',scrollbarH-scrollbutH);
		$(content).css('top','-'+contentH+'px');
		return ;
    }
    if(!opt&&scrollbutTop<=0){
		$(but).css('top',0);
		$(content).css('top',0);
		return ;
    }
    if(opt){
		if((contentTop-offH)<=(-contentH))offH=contentH+contentTop;
			$(content).css('top',contentTop-offH);
			$(but).css('top',-($(content).position().top)*(scrollbarH-scrollbutH)/contentH+'px');
	}else{
		if((contentTop-offH)>=0)offH=-contentTop;
		$(content).css('top',contentTop+offH);
		$(but).css('top',-($(content).position().top)*(scrollbarH-scrollbutH)/contentH+'px');
	}
}
function moveScrollbar(but,e,getvalue){
    var scrollbarH=$(but).parent().outerHeight(true);
    var scrollbutH=$(but).height();
    if(getvalue.offY>0&&(parseInt($(but).css('top'))+scrollbutH)>=scrollbarH){
	$(but).css('top',scrollbarH-scrollbutH);
	return false;
    }
    if(getvalue.offY<0&&parseInt($(but).css('top'))<=0){
	$(but).css('top',0);
	return false;
    }
    var scrollbarH=$(but).parent().outerHeight(true);
    var contentH=$(but).parent().parent().find('.scrollcontent').outerHeight( true )-scrollbarH;
    var barScrolled=$(but).position().top;
    $(but).parent().parent().find('.scrollcontent').css('top',-barScrolled*contentH/(scrollbarH-scrollbutH)+'px');
    return true;
}