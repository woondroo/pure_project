<?php
/**
 * @version		$Id: default.php 21726 2011-07-02 05:46:46Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_toback
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('mulan.mldb');
jimport('mulan.mltools');

$cateid = JRequest::getVar('id');
$com = JRequest::getVar('option');
$view = JRequest::getVar('view');
$itemid = JRequest::getVar('Itemid');
$pid = JRequest::getVar('pid');

$listLink = 'index.php?option='.$com.'&view='.$view.'s&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid;

$document = JFactory::getDocument();
$base = JURI::base();
$isie6 = MulanHTMLUtil::isIE6();

$ishover = $params->get('ishover');
$listway = $params->get('listway');
$showlist = $params->get('showlist');
$listbgimg = $params->get('listbgimg');
$listbgimg_pos = MulanToolsUtil::getMaterialPos($listbgimg);
$listbgimg = $listbgimg_pos['img'];
$listwidth = $params->get('listwidth', '20px');
$listheight = $params->get('listheight', '100px');
$listmb = $params->get('listmb', '140px');
$listpos = $params->get('listpos', 1);

$showtop = $params->get('showtop');
$topishide = $params->get('topishide');
$topbgimg = $params->get('topbgimg');
$topbgimg_pos = MulanToolsUtil::getMaterialPos($topbgimg);
$topbgimg = $topbgimg_pos['img'];
$topwidth = $params->get('topwidth', '20px');
$topheight = $params->get('topheight', '100px');
$topmb = $params->get('topmb', '20px');
$toppos = $params->get('toppos', 1);

$list_m = ($toppos == 1 ? intval($topwidth) : intval($topheight)) + intval($topmb) + intval($listmb);
$list_bottom = 0;
if ($listway) {
	$list_bottom = 0;
	if ($toppos == 3) {
		$list_bottom = $listpos == 3 && $pid ? $list_m : intval($listmb);
	} else if ($toppos == 4) {
		$list_bottom = $listpos == 4 && $pid ? $list_m : intval($listmb);
	}
	
	$right_1 = ($toppos == 1 && $pid ? $list_m : intval($listmb)).'px';
	$right_2 = -1*intval($listwidth);
	$rignt_3 = $right_2;
	$right_4 = $right_2;
	$bottom_1 = 0;
	$bottom_2 = ($toppos == 2 && $pid ? $list_m : intval($listmb)).'px';
	$bottom_3 = !$isie6 ? 'bottom:'.($toppos == 3 && $pid ? $list_m : intval($listmb)).'px;' : '';
	$bottom_4 = !$isie6 ? 'bottom:'.($toppos == 4 && $pid ? $list_m : intval($listmb)).'px;' : '';
} else {
	if ($listpos == 3 || $listpos == 4) $list_bottom = $listmb;
	
	$right_1 = $listmb;
	$right_2 = -1*intval($listwidth);
	$rignt_3 = $right_2;
	$right_4 = $right_2;
	$bottom_1 = 0;
	$bottom_2 = $listmb;
	$bottom_3 = !$isie6 ? 'bottom:'.$listmb.';' : '';
	$bottom_4 = $bottom_3;
}
$list_pos = array('right:'.$right_1.';position:absolute;',
				'position:absolute;right:'.$right_2.'px;bottom:'.$bottom_2.';',
				'position:fixed;_position:absolute;_right:'.$rignt_3.'px;'.$bottom_3,
				'position:fixed;_position:absolute;right:0;_right:'.$right_4.'px;'.$bottom_4);

$top_m = ($listpos == 1 ? intval($listwidth) : intval($listheight)) + intval($listmb) + intval($topmb);
if ($listway) {
	if ($listpos == 3 || $listpos == 4) $top_bottom = $topmb;
	
	$right_1 = intval($topmb);
	$right_2 = -1*intval($topwidth);
	$rignt_3 = $right_2;
	$right_4 = $right_2;
	$bottom_1 = 0;
	$bottom_2 = intval($topmb);
	$bottom_3 = !$isie6 ? 'bottom:'.$topmb.';' : '';
	$bottom_4 = $bottom_3;
} else {
	$top_bottom = 0;
	if ($toppos == 3) {
		$top_bottom = $listpos == 3 && $pid ? $top_m : intval($topmb);
	} else if ($toppos == 4) {
		$top_bottom = $listpos == 4 && $pid ? $top_m : intval($topmb);
	}
	
	$right_1 = $listpos == 1 && $pid ? $top_m : intval($topmb);
	$right_2 = -1*intval($topwidth);
	$rignt_3 = $right_2;
	$right_4 = $right_2;
	$bottom_1 = 0;
	$bottom_2 = $listpos == 2 && $pid ? $top_m : intval($topmb);
	$bottom_3 = !$isie6 ? 'bottom:'.($listpos == 3 && $pid ? $top_m : intval($topmb)).'px;' : '';
	$bottom_4 = !$isie6 ? 'bottom:'.($listpos == 4 && $pid ? $top_m : intval($topmb)).'px;' : '';
}
$top_pos = array('right:'.$right_1.'px;position:absolute;',
				'position:absolute;right:'.$right_2.'px;bottom:'.$bottom_2.'px;',
				'position:fixed;_position:absolute;_right:'.$rignt_3.'px;'.$bottom_3,
				'position:fixed;_position:absolute;right:0;_right:'.$right_4.'px;'.$bottom_4);

$insert_style = '#backto{width:100%;height:'.$height.';margin-top:'.(-1*intval($height)).'px;position:relative;}';
if ($showlist && $pid) $insert_style .= '#backto-list{'.$list_pos[$listpos-1].'width:'.$listwidth.';height:'.$listheight.';background-image:url('.$base.$listbgimg.');background-position:'.(-1*$listbgimg_pos['x']).'px '.(-1*$listbgimg_pos['y']).'px;}'.($ishover ? '#backto-list:hover{background-position:'.(-1*$listbgimg_pos['x']).'px '.(-1*$listbgimg_pos['y']-intval($listheight)).'px}' : '');
if ($showtop) $insert_style .= '#backto-top{'.$top_pos[$toppos-1].'width:'.$topwidth.';height:'.$topheight.';background-image:url('.$base.$topbgimg.');background-position:'.(-1*$topbgimg_pos['x']).'px '.(-1*$topbgimg_pos['y']).'px;}'.($ishover ? '#backto-top:hover{background-position:'.(-1*$topbgimg_pos['x']).'px '.(-1*$topbgimg_pos['y']-intval($topheight)).'px}' : '');
if ($insert_style) $document->addStyleDeclaration($insert_style);

if ($listpos == 1 && $toppos == 1) {
	$height = max(intval($listheight), intval($topheight)).'px';
} else {
	$height = $listpos == 1 ? $listheight : $topheight;
}

if ($listpos == 1 || $toppos == 1) echo '<div id="backto">';

if ($showlist && $start != null && $pid) {
?>
<a href="<?php echo $listLink; ?>" id="backto-list" class="backto-list"><span></span><?php echo $params->get('listshowtext') ? $params->get('listtext') : '' ?></a>
<?php
}
if ($showtop) {
?>
<a href="javascript:;" <?php echo $topishide ? 'style="display:none;"' : '' ?> id="backto-top" class="backto-top"><span></span><?php echo $params->get('topshowtext') ? $params->get('toptext') : '' ?></a>
<script type="text/javascript">
$(document).ready(function(){
	$("#backto-top").click(function(){
		if (document.documentElement.scrollTop) {
			document.documentElement.scrollTop = 10;
			$(document.documentElement).animate({scrollTop:0},300);
		} else {
			document.body.scrollTop = 10;
			$(document.body).animate({scrollTop:0},300);
		}
	});
	<?php
	if ($topishide) {
	?>
	$(window).scroll(function(){
		var top = $(this).scrollTop();
		if (top==0) {
			$('#backto-top').fadeOut();
		} else {
			$('#backto-top').fadeIn();
		}
	});
	<?php
	}
	?>
});
</script>
<?php
}
if ($listpos == 1 || $toppos == 1) echo '<div class="clr"></div></div>';

if (($listpos == 3 || $toppos == 3 || $listpos == 4 || $toppos == 4) && $isie6) {
?>
<script type="text/javascript">
$(document).ready(function(){
	var list_bottom = parseInt("<?php echo $list_bottom; ?>");
	var list_height = parseInt($('#backto-list').css('height'));
	
	var top_bottom = parseInt("<?php echo $top_bottom; ?>");
	var top_height = parseInt($('#backto-top').css('height'));
	
	var window_height = parseInt($(window).height());
	var window_width = parseInt($(window).width());
	var window_scroll = parseInt(document.documentElement.scrollTop);
	window.onresize = function(){
		window_height = parseInt($(window).height());
		window_width = parseInt($(window).width());
		IE6Scroll();
		IE6FixedRight();
	}
	var top = 0;
	var offset_top = parseInt($('.component-container')[0].offsetTop);
	var component_width = parseInt($('.component-container').width());
	var backto_list = $('#backto-list');
	var backto_top = $('#backto-top');
	$(window).scroll(function(){
		IE6Scroll();
	});
	
	function IE6FixedRight() {
		var fixed_right = (-1*(window_width-component_width)/2);
		<?php
			if ($listpos == 4) {
		?>
		backto_list.css({right:fixed_right+'px'});
		<?php
			}
			if ($toppos == 4) {
		?>
		backto_top.css({right:fixed_right+'px'});
		<?php
			}
		?>
	}
	
	function IE6Scroll() {
		window_scroll = parseInt(document.documentElement.scrollTop);
		
		<?php
			if ($listpos == 3 || $listpos == 4) {
		?>
		if (backto_list[0] != undefined) {
			top = window_scroll+window_height-offset_top-list_height-list_bottom;
			backto_list.css({top:top+'px'});
		}
		<?php
			}
			if ($toppos == 3 || $toppos == 4) {
		?>
		if (backto_top[0] != undefined) {
			top = window_scroll+window_height-offset_top-top_height-top_bottom;
			backto_top.css({top:top+'px'});
		}
		<?php
			}
		?>
	}
	IE6Scroll();
	IE6FixedRight();
	<?php
	
	?>
});
</script>
<?php
} else if ($listpos == 3 || $toppos == 3) {
?>
<script type="text/javascript">
$(document).ready(function(){
	component_width = parseInt($('.component-container').width());
<?php
	if ($listpos == 3) echo 'backto_list = $("#backto-list");if (backto_list[0] != undefined) backto_list.css({"margin-left":component_width+"px"});';
	if ($toppos == 3) echo 'backto_top = $("#backto-top");if (backto_top[0] != undefined) backto_top.css({"margin-left":component_width+"px"});';
?>
});
</script>
<?php
}
?>
