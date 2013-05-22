
var switchers = new Array();
/**
 * horizontalSwitcher 用来初始化一个 Switcher，如果一个页面有用到多个 switcher 模块，那么每个模块的 switcher_name 都必须不一样，否则会造成冲突！
 * 
 * @param switcher_name switcher模块实例名
 * @param switcher_direction 动画移动方向，默认是 0 左右切换，可选值有 0 和 1，1 表示上下切换
 */
function horizontalSwitcher(switcher_name,switcher_direction) {
	/**
	 * 创建 Switcher 模块的一个实例
	 */
	var s = new Switcher();
	var key_switcher_name = switcher_name;
	/**
	 * switcher_name 参数如果没有，则默认为 default，最好传递一个独一无二的命名
	 */
	if (!switcher_name) {
		key_switcher_name = 'default';
	}
	switchers[key_switcher_name] = s;
	if (switcher_direction == null || switcher_direction == undefined) {
		switcher_direction = 1;
	}
	/**
	 * 初始化 Switcher 模块
	 */
	s.init(switcher_name,switcher_direction);
}

Switcher = function(){
	this.setting = {
		switcher_name : null,		// 图片切换模块的名称
		switcher_direction : 0,		// @param -> decoration 元素切换的方向，默认为 0 是左右切换，其他值为上下切换
		direction : 'left',			// 通过 switcher_direction 计算得出的切换方向，默认是 left 左右切换
		animway : 0,				// @param -> animway 获得图片的切换方式，默认 0 为移动切换，其他值比如 1 为渐显/渐隐切换，2 为覆盖地进行切换
		showCount : 0,				// @param -> show 每大页展示多少个元素
		fadein : 0,					// @param -> fadein 左右切换的时候是否出现渐显/渐隐效果
		pagebt : 0,					// @param -> pagebt 是否显示分页切换的按钮，比如首页 Banner 的按钮，非左右按钮
		hidebt : 1,					// @param -> hidebt 在切换到边界时是否自动隐藏左右切换按钮
		itemCount : 0,				// 图片切换组件中共有多少个可以切换的元素
		prePointer : 0,				// 上一个切换到的位置，比如切换到第二张，元素位置是 2，那么该属性就会被自动赋值为 1 或者上次切换到的位置
		currentPointer : 0,			// 当前切换到的位置，注意：不是从 0 开始，而是从 1 开始
		animate_show_time : 500,	// @param -> showtime 如果有大图预览区域，该参数控制预览区域渐显渐隐的时间
		animate_move_time : 500,	// @param -> movetime 控制切换列表左右切换、渐显渐隐的动画时间
		thisSwitcher : null,		// 整个图片切换模块的对象
		items : null,				// 整个图片切换模块下的可切换元素集合
		itemWidth : 0,				// 每个元素的宽度/高度
		barWidth : 0,				// 内部切换区域容器的宽度/高度
		moveableBar : null,			// 内部切换区域容器的对象
		leftButton : null,			// 向左切换的按钮对象
		rightButton : null,			// 向右切换的按钮对象
		pageButton : null,			// 分页切换按钮的对象集合
		preItem : null,				// 在左右切换的过程中，如果临界元素需要渐显渐隐，该变量就保存前一个与之临界的元素对象
		endItem : null,				// 在左右切换的过程中，如果临界元素需要渐显渐隐，该变量就保存后一个与之临界的元素对象
		lastLose : 0,				// 在分页中，如果一页显示多个元素，那么该属性就保存 总元素个数/每页显示个数 的到的余数，用于边界处理
		gotoPage : 0,				// 自动切换、点击分页按钮切换时，该属性用于保存切换到的页数
		pointer : 0,				// 自动切换、点击分页按钮切换时，该属性用于保存切换到某页所经过的整数个 showCount 的值
		moveLeft : 0,				// 在左右/上下切换时，该属性保存元素容器当前切换到距离左侧/顶部的偏移值，一直小于等于零
		isScrollRun : false,		// 滚动动画是否正在执行，如果为 true 则被触发的下一个滚动事件将不会被执行，需要等到执行完才能再次执行
		isShowRun : false,			// 展示动画是否正在执行，如果为 true 则被触发的下一个展示动画将不会被执行，需要等到执行完才能再次执行
		isAutoRun : 0,				// @param -> auto 动画是否自动播放
		autoRunTime : 3000,			// @param -> autotime 自动播放时，动画间隔时间
		autoTimeout : null,			// 自动播放的定时器
		timingTimeout : null,		// 动画停止时间计时器
		timingLastTime : 0,			// 动画停止计时器的剩余时间剩余时间小于等于0时，所有按钮事件都会响应，否则都不可响应
		autoActive : false,			// 是否启动切换时自动设置当前元素激活状态，自动触发上一个/下一个的点击事件，到达边界时再进行滚动
		lastTime : 3000,			// 在自动播放的时候进行倒计时的时间
		use100 : 0,					// 是否强制100%宽度全屏展示动画
		coverPos : 100,				// 从边缘覆盖到已有图片上的偏移距离，单位：px
		posCalculate : ['-=','+=','-=','+='],
									// 偏移动画的计算式
		posDirection : ['left','left','top','top']
									// 偏移动画的偏移方向
	};
	
	/**
	 * switcher.init() 该方法用于初始化 switcher 模块
	 * 
	 * @param switcher_name 是该模块的独特名称，一个页面若多次使用 Switcher 组件，则需要给每个组件命名
	 * 
	 */
	this.init = function(switcher_name) {
		/**
		 * 将 switcher 对象的全局变量赋值给临时变量 s，这样就不必每次调用全局变量就用 this.setting 属性去访问
		 */
		var s = this.setting;
		s.switcher_name = switcher_name;
		if (!s.switcher_name) {
			s.switcher_name = 'horizontal';
			if ($("."+s.switcher_name+"-switcher")[0] == undefined) {
				return;
			}
		} else if (s.switcher_name && $("."+s.switcher_name+"-switcher")[0] == undefined) {
			return;
		}
		s.thisSwitcher = $("."+s.switcher_name+"-switcher");
		if (s.thisSwitcher[0] != undefined) {
			/**
			 * 初始化参数
			 */
			s.fadein = parseInt(s.thisSwitcher.attr("fadein")) > 0 ? 1 : 0;
			s.pagebt = parseInt(s.thisSwitcher.attr("pagebt")) > 0 ? 1 : 0;
			s.hidebt = parseInt(s.thisSwitcher.attr('hidebt')) > 0 ? 1 : 0;
			s.showCount = parseInt(s.thisSwitcher.attr("show")) > 0 ? parseInt(s.thisSwitcher.attr("show")) : 1;
			s.isAutoRun = parseInt(s.thisSwitcher.attr('auto')) > 0 ? 1 : 0;
			s.animate_move_time = parseInt(s.thisSwitcher.attr('movetime')) > 0 ? parseInt(s.thisSwitcher.attr('movetime')) : s.animate_move_time;
			s.animate_show_time = parseInt(s.thisSwitcher.attr('showtime')) > 0 ? parseInt(s.thisSwitcher.attr('showtime')) : s.animate_show_time;
			s.autoRunTime = parseInt(s.thisSwitcher.attr('autotime')) > 0 ? parseInt(s.thisSwitcher.attr('autotime')) : s.autoRunTime;
			s.lastTime = s.autoRunTime+1000;
			s.switcher_direction = parseInt(s.thisSwitcher.attr('decoration')) > 0 ? 1 : 0;
			s.animway = parseInt(s.thisSwitcher.attr('animway'));
			s.direction = s.switcher_direction == 0 ? 'left' : 'top';
			s.autoActive = parseInt(s.thisSwitcher.attr('autoactive')) > 0 ? true : false;
			s.use100 = parseInt(s.thisSwitcher.attr('use100'));
			
			/**
			 * 左右切换按钮对象的获取
			 */
			s.leftButton = s.thisSwitcher.find(".left-button");
			s.rightButton = s.thisSwitcher.find(".right-button");
			
			/**
			 * 获得切换区域的所有元素对象
			 */
			s.items = s.thisSwitcher.find(".item");
			/**
			 * 获得切换元素的宽度/高度
			 */
			eval('s.itemWidth = s.items.outer'+(s.direction == 'left' ? 'Width' : 'Height')+'();');
			
			/**
			 * 动画是左右切换还是渐显渐隐，如果为渐显渐隐，则需要给元素绑定位置
			 */
			if (s.animway >= 1) {
				for (var i = 0; i < s.items.length; i++) {
					$(s.items[i]).css({
						position:'absolute',
						left:(s.switcher_direction == 0 ? i%s.showCount*s.itemWidth : 0),
						top:(s.switcher_direction == 0 ? 0 : i%s.showCount*s.itemWidth)
					});
				}
				
				/**
				 * 获得切换区域所有元素的整体宽度/高度
				 */
				s.barWidth = s.itemWidth * s.showCount;
			} else {
				s.barWidth = s.items.length * s.itemWidth;
			}
			
			/**
			 * 如果有设置强制使用 100% 宽度，则将切换区域强制设置为 100% 宽度
			 */
			if (s.use100 == 1) {
				s.barWidth = '100%';
			}
			
			/**
			 * 获取切换区域的移动框对象，然后给移动框设置好可以移动到宽度/高度
			 */
			s.moveableBar = s.thisSwitcher.find(".moveable");
			eval('s.moveableBar.css({'+s.direction+':0});\
					s.moveableBar.'+(s.direction == 'left' ? 'width' : 'height')+'(s.barWidth);');
			
			/**
			 * 获取切换元素的总个数
			 */
			s.itemCount = s.items.length;
			/**
			 * 计算除去分页的整数倍切换元素后剩余的切换元素个数
			 */
			s.lastLose = s.itemCount % s.showCount != 0 ? s.itemCount % s.showCount : 0;
			/**
			 * 是否隐藏左右切换按钮
			 */
			if (s.hidebt == 1) {
				if (s.leftButton[0] != undefined) s.leftButton.css({opacity:0});
				if (s.itemCount <= s.showCount && s.rightButton[0] != undefined) {
					s.rightButton.css({opacity:0});
				}
			}
			this.startSwitcher();
		}
	};
	
	/**
	 * switcher.startSwitcher() 运行 Switcher
	 */
	this.startSwitcher = function() {
		var temp_obj = this;
		var s = this.setting;
		/**
		 * 如果设置的动画方式不是左右移动,则需要给切换元素设置 position 属性为 absolute
		 */
		if (s.items.length > 0 && s.animway >= 1) {
			for (var i = 0; i < s.items.length; i++) {
				if (i < s.showCount) {
					$(s.items[i]).css({opacity:1,'z-index':2});
				} else {
					$(s.items[i]).css({opacity:0,'z-index':1});
				}
			}
		}
		/**
		 * 激活第一个切换元素
		 */
		if (s.items.length > 0 && $("."+s.switcher_name+"-scroll-area")[0] != undefined) {
			s.items.removeClass('active');
			$(s.items[0]).addClass('active');
			/**
			 * 给所有切换元素绑定点击事件
			 */
			s.items.click(function(){
				temp_obj.clickItem(this);
			});
		}
		/**
		 * 给右切换按钮绑定点击事件
		 */
		if (s.rightButton[0] != undefined) {
			s.rightButton.click(function(){
				temp_obj.clickRightButton(this);
			});
		}
		/**
		 * 给左切换按钮绑定点击事件
		 */
		if (s.leftButton[0] != undefined) {
			s.leftButton.click(function(){
				temp_obj.clickLeftButton(this);
			});
		}
		/**
		 * 给分页切换按钮绑定点击事件
		 */
		if (s.thisSwitcher.find(".bigpagination")[0] != undefined) {
			s.pageButton = s.thisSwitcher.find(".pagination-b");
			s.pageButton.click(function(){
				temp_obj.clickPageButton(this);
			});
		}
		/**
		 * 如果设置了自动切换,则需要执行自动切换方法,然后开启计时器
		 */
		if (s.isAutoRun && s.itemCount > 1 && s.pageButton != null) {
			s.gotoPage = 1;
			this.startAutoRun(this);
			this.startTiming(this);
		}
	};
	
	/**
	 * 自定义计时器，用来更灵活地对操作间隔进行倒计时。
	 * switcher.startTiming
	 */
	this.startTiming = function(obj) {
		if (this.setting.timingLastTime <= 0) {
			this.setting.timingLastTime = 0;
			this.setting.isScrollRun = false;
		} else {
			this.setting.timingLastTime -= 200;
		}
		this.setting.timingTimeout = setTimeout(function(){
			obj.startTiming(obj);
		},200);
	}
	
	/**
	 * switcher.startAutoRun() 启动自动切换
	 */
	this.startAutoRun = function(obj) {
		/**
		 * 对自动切换进行计时,确保执行两次切换事件之间的间隔是一定的
		 */
		obj.setting.lastTime -= 1000;
		if (obj.setting.lastTime <= 0) {
			if (!obj.setting.isScrollRun && !obj.setting.isShowRun) obj.autoRun();
			obj.setting.lastTime = obj.setting.autoRunTime;
		}
		this.setting.autoTimeout = setTimeout(function(){
			obj.startAutoRun(obj);
		},1000);
	}
	
	/**
	 * switcher.autoRun() 执行自动分页切换
	 * 一旦满足自动切换条件,则开始进行一次自动切换
	 */
	this.autoRun = function() {
		var s = this.setting;
		s.gotoPage++;
		if (s.gotoPage > Math.ceil(s.itemCount/s.showCount)) {
			s.gotoPage = 1;
		}
		
		/**
		 * 运行分页切换方法
		 */
		this.clickPageButton(s.gotoPage);
	};
	
	/**
	 * switcher.clickItem() 监听切换元素的点击事件
	 * 
	 * @param ele 点击切换的元素时，位元素改变当前状态，并在大预览区展示预览
	 * 
	 */
	this.clickItem = function(ele) {
		var s = this.setting;
		if (s.isShowRun == true) return;
		s.isShowRun = true;
		
		var temp_obj = this;
		s.prePointer = s.currentPointer;
		s.currentPointer = parseInt($(ele).attr('id'));
		
		if ($(ele).attr('class').indexOf('active') < 0) {
			/**
			 * 清除所有预览区域的状态,将不需要显示的预览元素进行隐藏
			 */
			$('.'+s.switcher_name+'-scroll-area .one-view').each(function(){
				if ($(this).attr('id') != s.currentPointer+'_item') {
					$(this).animate({opacity:0},s.animate_show_time,function(){
						$(this).hide();
					});
				}
			});
		
			/**
			 * 激活对应的预览区域
			 */
			if ($('#'+s.currentPointer+'_item')[0] != undefined) {
				$('#'+s.currentPointer+'_item').css({opacity:0}).show().animate({opacity:1},s.animate_show_time,function(){
					s.lastTime = s.autoRunTime;
					s.isShowRun = false;
				});
			} else {
				s.lastTime = s.autoRunTime;
				s.isShowRun = false;
			}
		} else {
			s.lastTime = s.autoRunTime;
			s.isShowRun = false;
		}
		
		/**
		 * 激活选中的缩略图
		 */
		s.items.removeClass('active');
		$(ele).addClass('active');
	};
	
	/**
	 * switcher.getActiveItem() 获得当前已经激活的切换元素
	 */
	this.getActiveItem = function() {
		var s = this.setting;
		var activeItem = s.thisSwitcher.find(".item.active");
		return activeItem[0] == undefined ? s.items[0] : activeItem;
	}
	
	/**
	 * switcher.clickPageButton() 监听左切换按钮事件
	 * 
	 * @param ele 左切换按钮的对象，暂未使用
	 * 
	 */
	this.clickLeftButton = function(ele) {
		var s = this.setting;
		var activeItem = this.getActiveItem();
		if (s.autoActive && activeItem == undefined) return;
		
		/**
		 * 计算切换区域距离左侧/顶部的移动距离
		 */
		eval('s.moveLeft = parseInt(s.moveableBar.css(\''+s.direction+'\')) == NaN ? 0 : parseInt(s.moveableBar.css(\''+s.direction+'\'))');
		
		var isGo = true;
		/**
		 * 如果允许自动激活当前的切换元素,则判断当前激活的切换元素边界问题,如果到达边界则需要对切换元素进行滚动/位置调整
		 */
		if (s.autoActive) isGo = this.setAutoActive(-1,activeItem);
		if (isGo) this.goIt(-1);
	};
	
	/**
	 * switcher.clickPageButton() 监听右切换按钮事件
	 * 
	 * @param ele 右切换按钮的对象，暂未使用
	 * 
	 */
	this.clickRightButton = function(ele) {
		var s = this.setting;
		var activeItem = this.getActiveItem();
		if (s.autoActive && activeItem == undefined) return;
		
		/**
		 * 计算切换区域距离左侧/顶部的移动距离
		 */
		eval('s.moveLeft = parseInt(s.moveableBar.css(\''+s.direction+'\')) >= 0 ? 0 : parseInt(s.moveableBar.css(\''+s.direction+'\'))');
		
		var isGo = true;
		if (s.autoActive) isGo = this.setAutoActive(1,activeItem);
		if (isGo) this.goIt(1);
	};
	
	/**
	 * switcher.setAutoActive() 边界判断,是否需要对切换区域进行移动
	 * 
	 * @param where 该参数控制切换的方向
	 * 
	 */
	this.setAutoActive = function(where,activeItem) {
		var s = this.setting;
		if (s.isShowRun == true) return false;
		
		var activeId = parseInt($(activeItem).attr('id'));
		/**
		 * 临时保存前一个激活的元素的ID编号
		 */
		var pre_activeId = activeId;
		var return_val = true;
		/**
		 * 根据where参数判断是切换到上一个还是下一个,然后执行++/--操作
		 */
		if (where == 1 && activeId < s.items.length-1) {
			activeId++;
		} else if (where == -1 && activeId > 0) {
			activeId--;
		}
		
		/**
		 * 如果当前激活的切换元素有产生变化,则将当前应该激活的元素进行激活
		 */
		if (pre_activeId != activeId) this.clickItem(s.items[activeId]);
		/**
		 * 如果选择的是左右移动切换,而且当前激活元素还在可视范围内,则不允许切换区域进行移动
		 */
		if (s.animway == 0 && (Math.abs(s.moveLeft)+(s.showCount-1)*s.itemWidth) >= activeId*s.itemWidth && Math.abs(s.moveLeft) <= activeId*s.itemWidth) {
			return_val = false;
		} else {
			/**
			 * 如果选择的是渐变相关的切换,则临界判断需要重新计算
			 * 
			 * 要点:左右移动切换是根据切换区域距离左侧的偏移来算的,而显隐切换则是根据切换元素的绝对定位坐标来算的
			 * get_cur 属性保存的是当前需要激活的页，需要根据此属性进行分页切换
			 */
			var get_cur = (activeId+1)/s.showCount;
			/**
			 * 去掉小数点尾数，只取整
			 */
			var floor_get_cur = Math.floor(get_cur);
			if (get_cur == floor_get_cur) floor_get_cur -= 1;
			
			/**
			 * 获得切换区域需要移动到的位置，然后将临界切换元素边界与切换区域边界对齐
			 */
			s.currentPointer = get_cur <= Math.floor(s.itemCount/s.showCount) ? floor_get_cur*s.showCount : floor_get_cur*s.showCount-(s.lastLose > 0 ? s.showCount-s.lastLose : 0);
			
			/**
			 * 如果不是左右切换，则需要重新计算切换区域需要移动到的位置
			 */
			if (s.animway >= 1) s.currentPointer = floor_get_cur*s.showCount;
			if (s.animway >= 2) {
				for (var i = 0; i < s.items.length; i++) {
					if ($(s.items[i]).css('z-index') == '2') {
						floor_get_cur = Math.floor(parseInt($(s.items[i]).attr('id'))/s.showCount);
						break;
					}
				}
				if (activeId >= floor_get_cur*s.showCount && activeId < (floor_get_cur+1)*s.showCount) {
					return_val = false;
				}
			}
			where > 0 ? s.currentPointer-- : s.currentPointer++;
		}
		return return_val;
	}
	
	/**
	 * switcher.clickPageButton() 监听分页按钮点击事件
	 * 
	 * @param ele 切换到哪一页，可以是具体页数，也可以是分页按钮元素的对象
	 * 
	 */
	this.clickPageButton = function(ele) {
		var s = this.setting;
		if (s.isScrollRun == true) return;
		
		s.prePointer = s.currentPointer;
		s.gotoPage = !isNaN(parseInt(ele)) ? ele : parseInt($(ele).attr("title"));
		s.pointer = (s.gotoPage-1)*s.showCount;
		
		/**
		 * 如果是自动播放/分页切换，则需要重新对切换区域需要移动到的位置进行计算
		 */
		var page_to_where = s.gotoPage < Math.ceil(s.itemCount/s.showCount) || s.lastLose == 0;
		s.currentPointer = page_to_where ? s.pointer : (s.itemCount - s.pointer < s.showCount ? s.pointer-(s.showCount-s.lastLose) : s.pointer);
		if (s.animway >= 1) s.currentPointer = s.pointer;
		
		this.goIt(0,'pagebt');
	};
	
	/**
	 * switcher.goIt() 响应按钮事件进行切换
	 * 
	 * @param where 该参数控制切换的方向
	 * 
	 */
	this.goIt = function(where) {
		var s = this.setting;
		
		var other_arg = '';
		if (arguments.length > 1) other_arg = arguments[1];
		
		if (other_arg == 'pagebt') {
			if (s.isScrollRun == true || s.isShowRun == true) return;
		} else if (s.isScrollRun == true) {
			return;
		}
		s.isScrollRun = true;
		s.timingLastTime = s.animate_move_time;
		
		if (s.autoActive && s.pagebt) s.moveLeft = -s.currentPointer*s.itemWidth;
		
		/**
		 * 执行移动的条件比较多，注意到 animway 有两个可选值，0 表示左右切换，1 表示显隐切换即可轻松理解
		 */
		var is_go = false;
		if (other_arg == 'pagebt' && where == 0) {
			is_go = true;
		} else if (s.animway == 0 && s.moveLeft < 0 && where == -1) {
			is_go = true;
		} else if (s.animway == 0 && s.moveLeft > -s.itemWidth * (s.itemCount - s.showCount) && where == 1) {
			is_go = true;
		} else if (s.animway >= 1 && s.currentPointer > 0 && where == -1) {
			is_go = true;
		} else if (s.animway >= 1 && s.currentPointer < s.itemCount - 1 && where == 1) {
			is_go = true;
		}

		if (is_go) {
			this.goMove(where);
		} else {
			s.lastTime = s.autoRunTime;
		}
	};
	
	/**
	 * switcher.goMove() 执行元素移动/显隐
	 * 
	 * @param where 该参数控制切换的方向
	 * 
	 */
	this.goMove = function(where) {
		var temp_obj = this;
		var s = this.setting;
		
		/**
		 * curabs 变量用来对移动距离的值进行反向计算，比如切换区域向做移动了200px，那么实际的 left 值是-200px，所以需要进行一个移动方向的记录
		 */
		var curabs = -1;
		if (s.autoActive) {
			curabs *= s.currentPointer;
		} else {
			eval('curabs = parseInt(s.moveableBar.css(\''+s.direction+'\'))/s.itemWidth');
		}
		
		if (where != 0 && s.animway == 0 && !s.autoActive) {
			s.prePointer = s.currentPointer;
			s.currentPointer = Math.floor(Math.abs(curabs));
		}
		
		curabs = curabs == 0 ? -1 : curabs/Math.abs(curabs);
		where > 0 ? s.currentPointer++ : (where < 0 ? s.currentPointer-- : s.currentPointer);
		if (s.currentPointer > s.itemCount-1) s.currentPointer = s.itemCount-1;
		if (s.currentPointer < 0) s.currentPointer = 0;
		//$('#search_key_words').val(s.currentPointer);
		/**
		 * 根据不同的动画切换方式进行不同的处理，如果 animway >= 1 则又分为两种情况，
		 * 但他们都属于显隐切换，所以有共同的判断逻辑
		 */
		if (s.animway >= 1) {
			var start = s.currentPointer;
			var end = s.currentPointer + s.showCount;
			var anim_items = [];
			for (var k = start; k < end; k++) {
				anim_items.push(k);
			}

			for (var i = 0; i < s.items.length; i++) {
				var find_item = anim_items.indexOf(i) > -1;
				if (s.animway == 1) {
					$(s.items[i]).animate({'opacity':find_item ? 1 : 0,'z-index':find_item ? 2 : 1},s.animate_move_time,function(){
						s.lastTime = s.autoRunTime;
						temp_obj.setItemActive();
						temp_obj.setPageActive();
					});
				} else {
					if (find_item) {
						eval('var item_pos = parseInt($(s.items[i]).css(\''+s.posDirection[s.animway-2]+'\'));');
						var item_to_pos = item_pos;
						eval('item_to_pos'+s.posCalculate[s.animway-2]+'s.coverPos;\
							$(s.items[i]).css({\'opacity\':0,\'display\':\'block\',\'z-index\':2,\''+s.posDirection[s.animway-2]+'\':item_to_pos+\'px\'});\
							$(s.items[i]).animate({\'opacity\':1,\''+s.posDirection[s.animway-2]+'\':item_pos+\'px\'},s.animate_move_time,function(){\
								s.lastTime = s.autoRunTime;\
								temp_obj.setItemActive();\
								temp_obj.setPageActive();\
							});');
					} else {
						$(s.items[i]).css({'z-index':1});
						$(s.items[i]).animate({'opacity':1},s.animate_move_time,function(){
							$(this).css({'display':'none','opacity':'0'});
						});
					}
				}
			}
		} else {
			eval('s.moveableBar.animate({\
					'+s.direction+':curabs*s.itemWidth*s.currentPointer+"px"\
				},s.animate_move_time,\'easeOutCirc\',function(){\
					s.lastTime = s.autoRunTime;\
					temp_obj.setItemActive();\
					temp_obj.setPageActive();\
				});');
			temp_obj.fadeIt(where);
		}
	};
	
	/**
	 * switcher.fadeIt() 在左右切换过程中处理临界元素的显隐
	 * 
	 * @param where 该参数控制切换的方向，方法内可通过此参数控制临界元素的显隐
	 * 
	 */
	this.fadeIt = function(where) {
		var s = this.setting;
		if (s.fadein == 1 && where != 0 && s.animway == 0) {
			s.preItem = s.moveableBar.find(".item:eq("+(s.currentPointer-(where > 0 ? 1 : 0))+")");
			s.endItem = s.moveableBar.find(".item:eq("+(s.currentPointer+(where > 0 ? s.showCount-1 : s.showCount))+")");
			s.preItem.css("opacity",where > 0 ? 1 : 0);
			s.preItem.fadeTo(s.animate_move_time,where > 0 ? 0 : 1);
			s.endItem.css("opacity",where > 0 ? 0 : 1);
			s.endItem.fadeTo(s.animate_move_time,where > 0 ? 1 : 0);
		} else {
			s.items.css({opacity:1});
		}
	};
	
	/**
	 * switcher.setActive() 自动设置当前切换到的位置的各种状态
	 */
	this.setItemActive = function() {
		var s = this.setting;
		if (s.hidebt == 1 && s.animway == 0) {
			eval('s.moveLeft = parseInt(s.moveableBar.css(\''+s.direction+'\')) == NaN ? 0 : parseInt(s.moveableBar.css(\''+s.direction+'\'));');
			if (s.leftButton[0] != undefined) {
				if(s.moveLeft < 0) {
					s.leftButton.fadeTo(s.animate_move_time,1);
				} else {
					s.leftButton.fadeTo(s.animate_move_time,0);
				}
			}
			if (s.rightButton[0] != undefined) {
				if (s.moveLeft > -s.itemWidth * (s.itemCount - s.showCount)) {
					s.rightButton.fadeTo(s.animate_move_time,1);
				} else {
					s.rightButton.fadeTo(s.animate_move_time,0);
				}
			}
		} else if (s.hidebt == 1 && s.animway >= 1) {
			if (s.leftButton[0] != undefined) {
				if (s.currentPointer > 1) {
					s.leftButton.fadeTo(s.animate_move_time,1);
				} else {
					s.leftButton.fadeTo(s.animate_move_time,0);
				}
			}
			if (s.rightButton[0] != undefined) {
				if (s.currentPointer < s.itemCount - s.showCount + 1) {
					s.rightButton.fadeTo(s.animate_move_time,1);
				} else {
					s.rightButton.fadeTo(s.animate_move_time,0);
				}
			}
		}
	};
	
	/**
	 * switcher.setPageActive() 自动设置当前切换到的分页的各种状态
	 */
	this.setPageActive = function() {
		var s = this.setting;
		if (s.pageButton != null && s.pageButton != undefined) {
			s.pageButton.removeClass("active");
			$(".bigpagination a[title="+Math.ceil(s.currentPointer/s.showCount+1)+"]").addClass("active");
		}
	};
}

/**
 * jQuery 缓动方法扩展
 */
if (typeof jQuery != 'undefined') {
	jQuery.easing['jswing'] = jQuery.easing['swing'];
	jQuery.extend( jQuery.easing,
	{
		def: 'easeOutQuad',
		swing: function (x, t, b, c, d) {
			return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
		},
		easeOutQuad: function (x, t, b, c, d) {
			return -c *(t/=d)*(t-2) + b;
		},
		easeOutCirc: function (x, t, b, c, d) {
			return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
		}
	});
}

/**
 * Array 类的 indexOf 方法扩展
 */
Array.prototype.indexOf = function(el){
	for(var i=this.length-1; i>=0; i--){
		if(this[i]==el) return i;
	}
    return -1;
}

String.prototype.trim = function(){
	return this.replace(/^\s\s*/,'').replace(/\s\s*$/,'');
};

/**
 * validateform() 用于检查招聘以及其他表单提交时的必填字段验证
 */
function validateform() {
	var isvalid = true;
	$(".required-input").each(function(){
		if($(this).val()=="") {
			isvalid = false;
		}
	});
	
	if (isvalid == false) {
		alert(language.required_mess);
		return isvalid;
	}
	
	var emailreg = /^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_\-])+((\.[a-zA-Z0-9_\-]{2,3}){1,3})$/;
	var email = $("#r-email").val();
	if(!emailreg.test(email))
	{
		alert(language.required_email_mess);
		return false;
	}
	document.leavemessage.submit();
}

function checkQuickForm(the){
	var isvalid = true;
	$(the).find(".required-input").each(function(){
		if($(this).val()=="" && isvalid) {
			eval('alert(language.required_'+$(this).attr('name')+'_mess);');
			$(this)[0].focus();
			isvalid = false;
		}
	});
	
	if (isvalid == false) {
		return isvalid;
	}
	
	var e = f.find('.email:first');
	var reg = /^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_\-])+((\.[a-zA-Z0-9_\-]{2,3}){1,3})$/;
	if(!reg.test(e.val())){
		alert(language.required_email_mess);
		e.select();return false;		
	}
	return true;
}

/**
 * 请求刷新验证码
 * 
 * url 传递验证码获取所在位置的url
 * ele 可以为验证码不本身对象或者验证码刷新链接对象，
 *     会根据此对象查找父级节点/父级的父级节点下的验证码图片对象，然后更新
 */
function changeCode(url,ele){
	var src=url+'/index.php?option=com_users&task=displaycaptcha&ran='+(new Date().getTime().toString(36))+'';
	var checkImage=$(ele).parent().find('.check_code').attr('class')==undefined?$(ele).parent().parent().find('.check_code'):$(ele).parent().find('.check_code');
	if($(checkImage).attr('class')!=undefined){
		$(checkImage).attr('src',src);
	}
}

/**
 * 分享到，可以添加到收藏夹，或者分享到各大网站
 * 
 * ele 参数是分享到按钮的对象
 * stype 参数用来识别是何种操作，比如收藏、分享到微博等等
 */
function doShare(ele,stype,surl){
	var sitetitle = document.title.split('-');
	sitetitle = sitetitle[sitetitle.length-1];
	if (sitetitle != undefined && sitetitle != '') sitetitle = sitetitle.trim();
	
	var stitle = $(ele).attr("stitle") + ' - ' + sitetitle;
	var simg = $(ele).attr("simg");
	
	surl = window.location.protocol+'//'+window.location.host+surl;
	
	u = encodeURIComponent(surl);
	t = encodeURIComponent(stitle);
	p = encodeURIComponent(simg);
	switch(stype){
		case "fav":
			if (document.all){
				window.external.addFavorite(surl,stitle);
			} else if (window.sidebar) {
				window.sidebar.addPanel(stitle,surl, "");	
			} else {
				alert("当前浏览器不支持此操作，请按住鼠标左键，将此链接拖动到浏览器的书签即可。");
			}
			break;
		case "sina":
			window.open('http://www.jiathis.com/send/?webid=tsina&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "tqq":
			window.open('http://www.jiathis.com/send/?webid=tqq&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');x
			break;
		case "qzone":
			window.open('http://www.jiathis.com/send/?webid=qzone&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "renren":
			window.open('http://www.jiathis.com/send/?webid=renren&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "douban":
			window.open('http://www.jiathis.com/send/?webid=douban&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "kaixin001":
			window.open('http://www.jiathis.com/send/?webid=kaixin001&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "tsohu":
			window.open('http://www.jiathis.com/send/?webid=tsohu&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
		case "tieba":
			window.open('http://www.jiathis.com/send/?webid=tieba&url='+u+'&title='+t+(p != 'undefined' && p != undefined ? '&pic='+p : ''),'_blank');
			break;
	}
	return false;
}

$(document).ready(function(){
	if ($('#l-submit')[0] != undefined) {
		$('#l-submit').click(function(){
			validateform();
		});
	}
	if ($('#l-reset')[0] != undefined) {
		$('#l-reset').click(function(){
			document.leavemessage.reset();
		});
	}
});
