/*
 jquery 拖动插件
 *Copyright (c) 2009 MULAN-lsw
 */
//是否拖动了标志move
var draged=false;
(function($) {
/**
  拖动方法 参数 dragbody: '#outerId',opacity: '0.6', grid:[30,30],exchange:true;spring:true;......
  dragbody,将要移动的元素集slector；opacity,移动过程中被移动元素的透明过为多少；grid，元素移动为grid[0]grid[1]的整数倍；exchange，设置元素被移动到的位置处有同类元素是否交换位置
  spring,定义触发按下this中的第几个;ctrl设置按下ctrl不移动
  clone:是否CLONE一个新的组件。
  mouseupFn：鼠标弹起来运行的方法//fnPara:前一个函数的参数
  mousedownFn: 鼠标按下去的回调方法
  mousemoveFn: 鼠标移动的回调方法
 preventEvent:是否阻止MOUSEDOWN时的系统默认事件 默认是阻止
 selffixed: 本身是否静止不动(用其它的方法来体现drag)
 onlyxy:x|y 拖动限定为X/Y
*/
$.fn.drag=function(options){
    //初始化left top 因为可能left top不设定，在IE中为auto
    $.lsw.alertLeftTop($(options.dragbody));
    //$('html').unbind('mousemove');
    //$('html').unbind('mouseup');
    $(this).each(function(){
    //unbind是为了以前调用和当前调用的冲突
    //$(this).unbind('mousedown');
    //addClickEvent(options.opacity,this);
    //$(this).unbind('mousedown');
        //拖动物体的标志
        var the=$(this);
        var dragit=false;
        //可以指定鼠标移出后拖动结束(目前设计成不结束)
        //鼠标按下去时，拖动标志记为trues,记下当前鼠标的绝对坐标,记下被拖动物体的原始透明度
        $(this).mousedown(function(event){
            draged=false;
            //假如dragit为true即为按下, 这情况是由于当在拖动过程中，由于人为因素HTML非正常失去焦点，当再回到焦点时还是为TRUE，即按下状态。
            //alert(event.target.id);
            if(dragit){
                return false;
            }
            if(typeof(options.mousedownFn)=='function'){
                options.mousedownFn(eval(options.fnPara));
            }
            if((options.preventEvent)&&event.preventDefault){
                event.preventDefault();
            }
            event.stopPropagation();
                dragit=true;
                oldX=event.clientX;
                oldY=event.clientY;
                allOffX=0;
                allOffY=0;
                clickX=oldX;
                clickY=oldY;
            $(this).css('z-index',99999);
            //$(this).css('opacity',options.opacity);
            //return true;
        });
        //鼠标up时为每个独立的dragbody恢复透明度,还有ZINDEX
            $(options.dragbody).each(function(){
                var opacity=1;////($(this).css('opacity')==0)?1:$(this).css('opacity');
                var zindex=$(this).css('zIndex');
                var thisjq=$(this);
                $('html').mouseup(function(){
                	thisjq.css('z-index',zindex);
                    thisjq.css('opacity',opacity);
                });
            });
        //鼠标弹起，拖动结束
        $('html').mouseup(function(event){
            //the.css('z-index',0);
            if(!dragit){
                dragit=false;
                return;
            }
            dragit=false;
            if(typeof(options.mouseupFn)=='function'){
                options.mouseupFn(eval(options.fnPara));
            }
            if(!options.grid)return;
           //if(typeof(allOffX)=='undefined')return;
            var left,top,mustX,mustY;
            var allOffXLt0=allOffX<0;
            var allOffYLt0=allOffY<0;
            if(options.grid[0]>0){
                //总应该移动的距离X
                mustX=parseInt(((allOffX<0?-allOffX:allOffX)+options.grid[0]/2)/options.grid[0])*options.grid[0];
                //鼠标放开后再移动多少
                allOffX=allOffX<0?(mustX+allOffX):(allOffX-mustX);
            }
            if(options.grid[1]>0) {
                //总应该移动多少距离Y
                mustY=parseInt(((allOffY<0?-allOffY:allOffY)+options.grid[1]/2)/options.grid[1])*options.grid[1];
                //鼠标放开后再移动多少
                allOffY=allOffY<0?(mustY+allOffY):(allOffY-mustY);
            }    
            $(options.dragbody).each(function(){
                left=parseInt($(this).css('left').match(/^(.+)(px)?$/)[1])-allOffX+'px';
                top=parseInt($(this).css('top').match(/^(.+)(px)?$/)[1])-allOffY+'px';
                
                $(this).css('left',left);
                $(this).css('top',top);
                //如果参数 exchange为true，则在拖动过程中做被害人拖动的元素要放的位置有没有同类元素(位置，class一样),有则交换位置
                if(options.exchange){
                    var thiscss=$(this).attr('class');
                    var thejq=$(this);
                    $('.'+thiscss).each(function(){
                        var theLeft=$(this).css('left');
                        var theTop=$(this).css('top')
                        if(thejq.get(0)!=$(this).get(0)&&$(this).attr('class')==thiscss&&theLeft==left&&theTop==top){
                            //内部递归交换方法
                            function change(the,thiscss,left,top){
                                var conflict=false;
                                if(allOffXLt0){
                                    left=parseInt(left.match(/^(.+)px?$/)[1])+mustX+'px';
                                }else{
                                    left=parseInt(left.match(/^(.+)px?$/)[1])-mustX+'px';
                                }
                                if(allOffYLt0){
                                    top=parseInt(top.match(/^(.+)px?$/)[1])+mustY+'px';
                                }else{
                                    top=parseInt(top.match(/^(.+)px?$/)[1])-mustY+'px';
                                }
                                $('.'+thiscss).each(function(){
                                    var thisLeft=$(this).css('left');
                                    var thisTop=$(this).css('top');
                                    //位置有冲突，继续查找
                                    if($(this).attr('class')==thiscss&&thisLeft==left&&thisTop==top){
                                        conflict=true;
                                        change(the,thiscss,left,top);
                                    }
                                });
                                //此位置没有冲突则交换位置
                                if(!conflict){
                                    $(the).css('left',left);
                                    $(the).css('top',top);
                                }
                            }
                            change(this,thiscss,theLeft,theTop);
                        }
                    })
                }
            });
            //销毁变量
            //allOffX=undefined;
            if((options.preventEvent)&&event.preventDefault){
                event.preventDefault();
            }
            return false;
        });
        //鼠标移动拖动物体
        $('html').mousemove(function(e){
            //鼠标是否已经按下，否则返回不拖动
            if(!dragit||(options.ctrl?e.ctrlKey:false)){
                return;//return false;
            }else{
                if(e.preventDefault){
                    e.preventDefault();
                }
                draged=true;
                newX=e.clientX;
                newY=e.clientY;
                offX=newX-oldX;
                offY=newY-oldY;
                oldX=newX;
                oldY=newY;
                allOffX=parseInt(offX?offX:0)+allOffX;
                allOffY=parseInt(offY?offY:0)+allOffY;
                //移动指定要移动的元素 参数分别为：移动元素选择器，移动时的透明度，移动Ｘ距离，移动Y距离
                $.lsw.moveThey(options,offX,offY,e);
                return;//return false;
            }
        });
        
        
    });
    if(options.spring&&$(this).length!=0){
        $(this).eq(0).mousedown();
    }
};

$.lsw={
    //移动元素
    moveThey: function(options,offX,offY,e){
        if(isNaN(offX)||isNaN(offY))return;
		if(options.mousemoveFn){
			if(typeof(options.mousemoveFn)=='function'){
				var setvalue={'offX':offX,'offY':offY};
				if(!options.mousemoveFn(options.fnPara,e,setvalue))return;
			}
		}
		if(options.selffixed){
            return;
        }
        $(options.dragbody).each(function(){
            if(options.clone){
                var jq=$(this).clone();
                var opt=owl.util.copy(options);
                opt.dragbody=jq;
                if(opt.fnPara)opt.fnPara=jq;
                jq.insertBefore(this);
                jq.drag(opt);
                options.clone=false;
            }

            $(this).css('opacity',options.opacity);
			var pos=$(this).position();
            if(options.onlyxy=='y')$(this).css('top',offY+parseInt($(this).css('top').match(/^(.+)(px)?$/)[1])+'px');
            if(options.onlyxy=='x')$(this).css('left',offX+parseInt($(this).css('left').match(/^(.+)(px)?$/)[1])+'px');
            if((options.onlyxy!='y'&&(options.onlyxy!='x'))){
            	$(this).css('top',offY+parseInt($(this).css('top').match(/^(.+)(px)?$/)[1])+'px');
            	$(this).css('left',offX+parseInt($(this).css('left').match(/^(.+)(px)?$/)[1])+'px');
            }
            
            //如果元素先本身有RIGHT BOTTOM属性则会冲突
            $(this).css('right','auto').css('bottom','auto');
        });
    },
    //假如某元素未初始化left top 初始之
    alertLeftTop: function(jq){
        jq.each(function(){
            if($(this).css('position')!='relative'&&$(this).css('position')!='absolute'&&$(this).css('position')!='fixed'){
                $(this).css('position','relative');
            }
            if(!$(this).css('left').match(/^[\d\.-]+(px)?$/)){
                if($(this).css('position')=='relative'){
                        $(this).css('left','0px');return;
                }
                $(this).css('left',$(this).position().left+'px');
            }
            if(!$(this).css('top').match(/^[\d\.-]+(px)?$/)){
                if($(this).css('position')=='relative'){
                    $(this).css('top','0px');
                }
                $(this).css('top',$(this).position().top+'px');
            }
        })
    }
};
    
})(jQuery);