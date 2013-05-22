/*
 * StringBuffer 实现高性能字符串连接
 * 2011-10-18 
 * oMeSe
 */
function StringBuffer() {
	this.__strings__ = new Array;
}
StringBuffer.prototype.append = function (str) {
	this.__strings__.push(str);
};
StringBuffer.prototype.toString = function () {
	return this.__strings__.join("");
};
/*
 * trim  实现IE兼容trim()方法
 * 2011-10-18
 * oMeSe
 */
String.prototype.trim=function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
String.prototype.ltrim=function(){
	return this.replace(/(^\s*)/g,"");
}
String.prototype.rtrim=function(){
	return this.replace(/(\s*$)/g,"");
}

/*
 * 类似PHP中explode方法
 * 2011-10-18 
 * oMeSe
*/
function explode(separators, inputstring, includeEmpties) {
	inputstring = new String(inputstring);
	separators = new String(separators);

	if (separators == "undefined") {
		separators = " :;";
	}

	fixedExplode = new Array(1);
	currentElement = "";
	count = 0;

	for (x = 0; x < inputstring.length; x++) {
		str = inputstring.charAt(x);
		if (separators.indexOf(str) != -1) {
			if (((includeEmpties <= 0) || (includeEmpties == false))
					&& (currentElement == "")) {
			} else {
				fixedExplode[count] = currentElement;
				count++;
				currentElement = "";
			}
		} else {
			currentElement += str;
		}
	}

	if ((!(includeEmpties <= 0) && (includeEmpties != false))
			|| (currentElement != "")) {
		fixedExplode[count] = currentElement;
	}
	return fixedExplode;
}


(function ($) {
	/*
	    判断#a是否存在
		$('#a').isExist() 
	*/
	$.fn.isExist=function(){
		if($(this).length==0)return false;
		else return true;
	};
	
	/*
	单选框和复选框是否选中
	*/
	$.fn.isChecked=function(){
		var checked=false;
		$(this).each(function(){
			if($(this).attr('checked')){
				checked=true;
				return false;
			}
		});
		return checked;
	};

	/*
	弹出层效果layerdivClick:点击layerdiv层是否关闭alertdiv true为不关
	jquerydiv.setLightBox({layoverBg:'#000',layoverOpa:'0.4',alertdivBg:'#fff',alertdivWidth:'540px',alertdivHeight:'310px',sureBut:'#suersub',sureFn:saveAlbumToSearve,suerPara:xml,closeSelfBut:'#colseself',closeBut:'#closeit,#closebut',layerdivClick:true          ,canNotDrag:true        ,dragbody:'#alerttitle'   ,multil:true})
	对应参数                  mask层的背景色      mask层的透明度        弹出层的背景色           弹出层的宽               弹出层的高                 确定按钮，可多个         确定后执行的回调函数             确定的回调函数的参数 关闭弹出层的按钮，可多个  	       关闭全部层的按钮，可多个		         点击mask层是否关闭全部，true为不关闭    不能被拖动，true为不能被拖动     拖动什么区域拖动弹出层   	       此弹出层是否与先前的层共存，即是否能有多个弹出层存在
	inWindow:true	=>	单独弹出图片时，图片过大时，只在可见区																								
	*/	
	jQuery.fn.setLightBox=function(options){
		var layoverBg=options.layoverBg?options.layoverBg:'#fff';
		var layoverOpa=options.layoverOpa?options.layoverOpa:'0.5';
		var alertdivWidth=options.alertdivWidth?options.alertdivWidth:'auto';
		var alertdivHeight=options.alertdivHeight?options.alertdivHeight:'auto';
		var alertdivBg=options.alertdivBg?options.alertdivBg:'#fff';
		var overflow=options.overflow?'overflow:'+options.overflow:'';
		var closebut=options.closeBut;
		var layerdiv,alertdiv;var isfirst=true;
		jQuery('.layoverdiv').remove();
		jQuery('.alertdiv').remove();
		if(true||jQuery('.layoverdiv').length==0){
			layerdiv=jQuery('<div class="layoverdiv" style="position:absolute;width:100%;height:100%;z-index:9998;left:0;top:0;display:none;"></div>');
			alertdiv=jQuery('<div class="alertdiv" style="'+overflow+';position:absolute;background:transparent;top:50%;left:50%;display:none;z-index:9999;"></div>');
			jQuery('body').append(alertdiv).append(layerdiv);
		}else{
			isfirst=false;
			layerdiv=jQuery('.layoverdiv');
			if(options.multil){
				off=jQuery('.alertdiv').length*3+50+'%';
				alertdiv=jQuery('<div class="alertdiv alertmultil" style="'+overflow+'position:absolute;background:transparent;top:'+off+';left:'+off+';display:none;z-index:9999;"></div>');
				jQuery('body').append(alertdiv);
			}else{
				jQuery('.alertmultil').remove();
				alertdiv=jQuery('.alertdiv');
			}
		}
		layerdiv.css('background',layoverBg);
		layerdiv.width(jQuery('body').outerWidth(true));
		layerdiv.height(jQuery('body').outerHeight(true));
		alertdiv.css('background',alertdivBg);
		alertdiv.css({'width':alertdivWidth,'height':alertdivHeight}).html('').append(jQuery(this));
		
		if(!options.multil){
			layerdiv.css('opacity','0').css('display','block').fadeTo(500,layoverOpa);
		}
		var mtop = -(alertdiv.outerHeight()/2);
		var clientH = document.documentElement.clientHeight;
		var clientW = document.documentElement.clientWidth; 
		var stop = clientH/2+document.documentElement.scrollTop;
		if(stop+mtop<0){
			stop = 20;
			mtop = 0;
		}
		var inwindow_padding = 15;
		alertdiv.css('top',stop+'px');
		alertdiv.css({'margin-left':'-'+(alertdiv.outerWidth()/2+'px'),'margin-top':mtop+'px'}); 
			jQuery(this).add(jQuery(this).find('img')).load(function(){
				if(options.inWindow){
					if(($(this).height())>clientH){
						$(this).height(clientH-inwindow_padding*2);
					}
					if((false&&$(this).width())>clientH){
						$(this).width(clientW-inwindow_padding*2);
					}
				}
				stop = clientH/2+document.documentElement.scrollTop;
				mtop = -(alertdiv.outerHeight()/2);
				if(stop+mtop<0){
					stop = 20;
					mtop = 0;
					alertdiv.css('top',stop+'px');
				}
				alertdiv.css('top',stop+'px');
				alertdiv.css({'margin-left':'-'+(alertdiv.outerWidth()/2+'px'),'margin-top':mtop+'px'});
			});
			alertdiv.hide();
			alertdiv.fadeIn();
			if(!options.canNotDrag){
				var dragbodys=options.dragbody?alertdiv.find(options.dragbody):alertdiv;
				if(alertdiv.find('img').length!=0){
					//当没有关闭按钮时mouseupFn的作用是，拖动时不关闭弹出窗口，点击时才关闭
					if(options.closeBut){
						dragbodys.drag({dragbody: alertdiv,opacity: '0.8',preventEvent:true});
					}else{
						dragbodys.drag({dragbody: alertdiv,opacity: '0.8',preventEvent:true,mouseupFn:function(){if(!draged){$.ml.closeLightBox();}}});
					}
				}else{
					if(options.closeBut){
						dragbodys.drag({dragbody: alertdiv,opacity: '0.8'});
					}else{
						dragbodys.drag({dragbody: alertdiv,opacity: '0.8',mouseupFn:function(){if(!draged){$.ml.closeLightBox();}}});
					}
				}
			}
		if(!options.layerdivClick&&!options.multil){
			layerdiv.click(function(){
				if(!options.closeFn(options.closePara));
				$.ml.closeLightBox();
			});
		}
		if(closebut!=undefined){
			jQuery(closebut).click(function(){
				if(alertdiv.find('#bigR').attr("title")!=undefined){
					if(!options.closeFn(options.closePara));
					$.ml.closeLightBox();
				}
				else
				{
					$.ml.closeLightBox();
				}
				
			});
		}
		if(options.sureBut!=undefined){
			//,sureFn:saveAlbumToSearve,suerPara:xml
			jQuery(options.sureBut).click(function(){
				if(!options.sureFn(options.suerPara))return;
				$.ml.closeLightBox();
			});
		}
		if(options.closeSelfBut!=undefined){
			alertdiv.find(options.closeSelfBut).click(function(){
				alertdiv.remove();
			});
		}
	};
	
	$.ml = {butShowTab:function(buts,tabs,tabs2,num,targe,backfn,startfn,speed){
		if(num>=0){
			$(tabs.join()).css('display','none');
			$(tabs.join()).eq(num).css('display','block');
			$(tabs2.join()).css('display','none');
			$(tabs2.join()).eq(num).css('display','block');
		}
		$(buts.join()).each(function(i){
			eval('$(this).'+targe+'(function(){'+
				'if(!startfn||(startfn(i)!=false)){'+
				//'$(buts.join()).addClass("imagenone");'+
				'if(speed!=0){$(tabs.join()).stop(null,true).fadeTo(speed,0.1,function(){$(tabs.join()).css("display","none");$(tabs.join()).eq(i).css("display","block");});'+
				'$(tabs.join()).eq(i).stop(null,true).fadeTo(speed,1,function(){backfn?backfn(i):";"});}else{'+
				'$(tabs.join()).css("display","none");'+
				//'$(buts.join()).eq(i).removeClass("imagenone");'+
				'$(tabs.join()).eq(i).css("display","block");'+
				'backfn?backfn(i):";"}'+
				'}});');
			eval('$(this).'+targe+'(function(){'+
				'if(!startfn||(startfn(i)!=false)){'+
				//'$(buts.join()).addClass("imagenone");'+
				'if(speed!=0){$(tabs2.join()).stop(null,true).fadeTo(speed,0.1,function(){$(tabs2.join()).css("display","none");$(tabs2.join()).eq(i).css("display","block");});'+
				'$(tabs2.join()).eq(i).stop(null,true).fadeTo(speed,1,function(){backfn?backfn(i):";"});}else{'+
				'$(tabs2.join()).css("display","none");'+
				//'$(buts.join()).eq(i).removeClass("imagenone");'+
				'$(tabs2.join()).eq(i).css("display","block");'+
				'backfn?backfn(i):";"}'+
				'}});');
		});
	},closeLightBox:function(options){
		alertdiv=$('.alertdiv,.layoverdiv').fadeOut();
	}};
})(jQuery);