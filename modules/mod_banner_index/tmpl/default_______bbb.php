<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_banner_index
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mldb.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mlstring.php' );
jimport('mulan.mlhtml');
?>
<div class="banner-index">
	<div class="frame960">
		<div class="banner-img relative">
			<?php foreach( $items as $o){?>
			<div class="leftimg left1" 
					moveInDerection="<?php echo $o->left_in_derc?>"
					moveInSpeed="<?php echo $o->left_in_movespeed?>"
					moveInType="<?php echo $o->left_in_movetype?>"
					fadeInSpeed="<?php echo $o->left_in_fadespeed?>"
					fadeInType="<?php echo $o->left_in_fadetype?>"
					
					moveOutDerection="<?php echo $o->left_out_derc?>"
					moveOutSpeed="<?php echo $o->left_out_movespeed?>"
					moveOutType="<?php echo $o->left_out_movetype?>"
					fadeOutSpeed="<?php echo $o->left_out_fadespeed?>"
					fadeOutType="<?php echo $o->left_out_fadetype?>"
			>
				<img src="<?php echo $o->leftimg?>" alt="<?php echo $o->title?>"/>
			</div>
			<div class="rightimg right1"
					moveInDerection="<?php echo $o->right_in_derc?>"
					moveInSpeed="<?php echo $o->right_in_movespeed?>"
					moveInType="<?php echo $o->right_in_movetype?>"
					fadeInSpeed="<?php echo $o->right_in_fadespeed?>"
					fadeInType="<?php echo $o->right_in_fadetype?>"
					
					moveOutDerection="<?php echo $o->right_out_derc?>"
					moveOutSpeed="<?php echo $o->right_out_movespeed?>"
					moveOutType="<?php echo $o->right_out_movetype?>"
					fadeOutSpeed="<?php echo $o->right_out_fadespeed?>"
					fadeOutType="<?php echo $o->right_out_fadetype?>"
			>
				<img src="<?php echo $o->rightimg?>" alt="<?php echo $o->title?>"/>
			</div>
			<?php }?>
		</div>
		<ul class="banner-bt">
			<li>
				<a class="pointer" target="1">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="2">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="3">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="4">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="5">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="6">
					<span class="ico"></span>
				</a>
			</li>
			<li>
				<a class="pointer" target="7">
					<span class="ico"></span>
				</a>
			</li>
		</ul>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var animateDone = true;	
		function getInDirectionTween(e,moveDirecrion,moveInSpeed,moveInType)
		{
			var tweenObject = null;
			tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('top',ps);
						e.css('left',ps);
					}
			//switch(moveDirecrion)
			//{
				//case 0:tweenObject = tween('0px','0px',moveInSpeed,moveInType);break;
				//case 1:tweenObject = tween('-460px','0px',moveInSpeed,moveInType);break;
				/*case 2:
				
					tweenObject = tween('460px','0px',moveInSpeed,moveInType);//срио
					tweenObject.run = function(ps)
					{
						e.css('top',ps);
						e.css('left',ps);
					}
				break;
				case 3:
				
					tweenObject = tween('460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('left',ps);
					}
				break;
				case 4:
				
					tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('bottom',ps);
						e.css('right',ps);
					}
				break;
				case 5:
				
					tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('bottom',ps);
					}
				break;
				case 6:
				
					tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('bottom',ps);
						e.css('left',ps);
					}
				break;
				case 7:
					tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('left',ps);
					}
				break;
				case 8:
				
					tweenObject = tween('-460px','0px',moveInSpeed,moveInType);//
					tweenObject.run = function(ps)
					{
						e.css('top',ps);
						e.css('left',ps);
					}
				break;*/
			//}
		}
		function fadeInTween(fadeInSpeed,fadeInType)
		{
			var animateOpacity =  tween(0,1,fadeInSpeed,fadeInType);
			animateOpacity.run = function(ps)
			{
				e.css('opacity',ps);
			}
		}
		function fadeOutTween(e,fadeOutSpeed,fadeOutType)
		{
			var animateOpacity =  tween(1,0,fadeOutSpeed,fadeOutType);
			animateOpacity.run = function(ps)
			{
				e.css('opacity',ps);
			}
		}
		function rightIn(e)
		{
			var moveInDerection = e.attr('moveInDerection');
			var moveInSpeed = e.attr('moveInSpeed');
			var moveInType = e.attr('moveInType');
			var fadeInSpeed = e.attr('fadeInSpeed');
			var fadeInType = e.attr('fadeInType');
			getInDirectionTween(e,moveInDerection,moveInSpeed,moveInType);
			fadeInTween(e,fadeInSpeed,fadeInType);
		}
		function rightOut(e){
			
		}
		function leftIn(e)
		{
			var moveInDerection = e.attr('moveInDerection');
			var moveInSpeed = e.attr('moveInSpeed');
			var moveInType = e.attr('moveInType');
			var fadeInSpeed = e.attr('fadeInSpeed');
			var fadeInType = e.attr('fadeInType');
			getInDirectionTween(e,moveInDerection,moveInSpeed,moveInType);
			fadeInTween(e,fadeInSpeed,fadeInType);
		}
		function leftOut(e)
		{
			
		}
		
		function animateOut(objectLeft,objectRight)
		{
			rightOut(objectRight);
			leftOut(objectLeft)
		}
		function animateIn(objectLeft,objectRight)
		{
			rightIn(objectRight);
			leftIn(objectLeft);
		}
		function getMaxFromArray(arr)
		{
			var max=arr[0];
            for(i=0;i<arr.length;i++)   
            {   
				if(arr[i]>max)   
				max=arr[i];   
			}
			return max;
		}
	
		$(".pointer").click(function(){
			var currentClick = false;
			var current = $(".banner-bt").find(".active");
			var pretarget = current.attr("target");
			var target = $(this).attr("target");
			if(pretarget==target)currentClick=true;
			if(!currentClick)
			{
				if(animateDone)
				{
					current.removeClass("active");
					$(this).addClass("active");
					animateDone = false;
					var preObjectLeft = $(".left"+pretarget);
					var preObjectRight = $(".right"+pretarget);
					var objectLeft = $(".left"+target);
					var objectRight = $(".right"+target);
					var speedArray = [	objectLeft.attr('moveInSpeed'),
									objectLeft.attr('fadeInSpeed'),
									objectRight.attr('moveInSpeed'),
									objectRight.attr('fadeInSpeed'),
								];
					var animateTime = getMaxFromArray(speedArray) * 1000 ;
					animateOut(preObjectLeft,preObjectRight);
					animateIn(objectLeft,objectRight);
					setTimeout(animateDoneFunction,animateTime);
				}
			}
			
		});
		function animateDoneFunction()
		{
			animateDone = true;
		}
		function initilAnimate()
		{
			$($(".pointer")[0]).click();
		}
		initilAnimate();
	});
	
</script>