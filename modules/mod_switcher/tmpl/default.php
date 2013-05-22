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

$base = JURI::base();
jimport('mulan.mlhtml');
jimport('mulan.mlimage');
jimport('mulan.mlstring');
jimport('mulan.mltools');
if (!count($items)) return;

$count_item = count($items);
$thisTotalPage = ceil($count_item/$params->get('show'));

$document = JFactory::getDocument();
$switcherfill = explode('-',$params->get('switcherfill'));
$float_switchers = array('sl','sr');
$float_view = array('sl'=>' right','sr'=>' left');
$float_switcher = array('sl'=>' left','sr'=>' right');

$alias = $params->get('alias');
$decoration = $params->get('decoration');
$show_pagination_bts = $params->get('show_pagination_bts');
$show = $params->get('show');
$switcherbg = $params->get('switcherbg');
$switcherbg_pos = MulanToolsUtil::getMaterialPos($switcherbg);
$switcherbg = $switcherbg_pos['img'];
$use100 = $params->get('use100');
$use100min = $params->get('use100min');

$itembg = $params->get('itembg');
$itembg_pos = MulanToolsUtil::getMaterialPos($itembg);
$itembg = $itembg_pos['img'];
$iteminnerwidth = $params->get('iteminnerwidth', '100px');
$iteminnerheight = $params->get('iteminnerheight', '60px');
$itemouterwidth = $params->get('itemouterwidth', '110px');
$itemouterheight = $params->get('itemouterheight', '70px');
$itemshowtitle = $params->get('itemshowtitle', 0);
$itemtitleheight = $params->get('itemtitleheight', '20px');

$thumb_item_width = $use100 ? 1920 : intval($iteminnerwidth);

$isshowbt = $params->get('isshowbt');
$btbg = $params->get('btbg');
$btbg_pos = MulanToolsUtil::getMaterialPos($btbg);
$btbg = $btbg_pos['img'];
$btwidth = $params->get('btwidth', '35px');
$btheight = $params->get('btheight', '60px');
$btm1 = $params->get('btm1', '0px');
$btm2 = $params->get('btm2', '0px');

$pagebtbg = $params->get('pagebtbg');
$pagebtbg_pos = MulanToolsUtil::getMaterialPos($pagebtbg);
$pagebtbg = $pagebtbg_pos['img'];
$pagebtuseimg = $params->get('pagebtuseimg');
$pagebtshownum = $params->get('pagebtshownum');
$pagebtwidth = $params->get('pagebtwidth', '60px');
$pagebtheight = $params->get('pagebtheight', '10px');
$pagebtm = $params->get('pagebtm', '5px');
$pagebtposx = $params->get('pagebtposx', 2);
$pagebtposy = $params->get('pagebtposy', 3);
$pagebtmx = $params->get('pagebtmx', '0px');
$pagebtmy = $params->get('pagebtmy', '0px');

$pagination_width = (intval($pagebtwidth)+intval($pagebtm))*$thisTotalPage;
if ($decoration == 1) $pagination_width = intval($pagebtwidth);
$posx = array('left:','left:','right:');
$posx_val = array('0','50%','0');
$mx = array('margin-left:','margin-left:','margin-right:');
$mx_val = array($pagebtmx,'-'.($pagination_width/2).'px',$pagebtmx);
$posy = array('top:','top:','bottom:');
$posy_val = array('0','50%','0');
$my = array('margin-top:','margin-top:','margin-bottom:');
$my_val = array($pagebtmy,'-'.(intval($pagebtheight)/2).'px',$pagebtmy);
if ($decoration == 1) $my_val = array($pagebtmy,'-'.((intval($pagebtheight)+intval($pagebtm))*$thisTotalPage/2).'px',$pagebtmy);
$pagination_css = $posx[$pagebtposx-1].$posx_val[$pagebtposx-1].';'.
				$mx[$pagebtposx-1].$mx_val[$pagebtposx-1].';'.
				$posy[$pagebtposy-1].$posy_val[$pagebtposy-1].';'.
				$my[$pagebtposy-1].$my_val[$pagebtposy-1].';';

$viewwidth = $params->get('viewwidth', '600px');
$viewheight = $params->get('viewheight', '400px');

$viewbg = $params->get('viewbg');
$viewbg_pos = MulanToolsUtil::getMaterialPos($viewbg);
$viewbg = $viewbg_pos['img'];
$viewitemwidth = $params->get('viewitemwidth', '600px');
$viewitemheight = $params->get('viewitemheight', '400px');

$descwidth = $params->get('descwidth', '600px');
$descheight = $params->get('descheight', '60px');
$descm = $params->get('descm', '10px');
$descbg = $params->get('descbg', '#000');
$descopacity = $params->get('descopacity', '20');

$switcher_params = array();
array_push($switcher_params, 'id="'.$alias.'-switcher"');
array_push($switcher_params, 'class="switcher '.$alias.'-switcher'.(in_array($switcherfill[1],$float_switchers) ? $float_switcher[$switcherfill[1]] : '').'"');
array_push($switcher_params, 'show="'.$show.'"');
array_push($switcher_params, 'fadein="'.$params->get('fadein').'"');
array_push($switcher_params, 'pagebt="'.$params->get('pagebt').'"');
array_push($switcher_params, 'hidebt="'.$params->get('hidebt').'"');
array_push($switcher_params, 'showtime="'.$params->get('showtime').'"');
array_push($switcher_params, 'movetime="'.$params->get('movetime').'"');
array_push($switcher_params, 'decoration="'.$params->get('decoration').'"');
array_push($switcher_params, 'animway="'.$params->get('animway').'"');
array_push($switcher_params, 'auto="'.$params->get('auto').'"');
array_push($switcher_params, 'autotime="'.$params->get('autotime').'"');
array_push($switcher_params, 'autoactive="'.$params->get('autoactive').'"');
array_push($switcher_params, 'use100="'.$use100.'"');

$insert_style = '';
$item_height = $itemshowtitle ? (intval($itemouterheight)+intval($itemtitleheight)).'px' : $itemouterheight;
if ($switcherfill[2] == 'whp') $item_height = "100%";

// switcher css
$switcher_width = $use100 ? '100%' : (intval($itemouterwidth)*$show+(intval($btwidth)+intval($btm1)+intval($btm2))*2).'px';
if ($decoration == 1) $switcher_width = $itemouterwidth;
$switcher_other_css = '';
if ($switcherfill[2] == 'whp') $switcher_other_css = 'position:absolute;left:0;top:0;height:100%;z-index:-1;';
$insert_style .= '#'.$alias.'-switcher{'.($switcherbg ? 'background:url('.$base.$switcherbg.') '.(-1*$switcherbg_pos['x']).'px '.(-1*$switcherbg_pos['y']).'px no-repeat;' : '').'width:'.$switcher_width.';'.$switcher_other_css.';}';

// content-frame css
$switcher_content_frame_width = $use100 ? '100%' : (intval($itemouterwidth)*$show).'px';
if ($decoration == 1) $switcher_content_frame_width = $itemouterwidth;
$switcher_content_frame_height = $item_height;
if ($decoration == 1) $switcher_content_frame_height = (intval($item_height)*$show).'px';
$switcher_content_frame_margin = $use100 ? '0 0' : '0 '.(intval($btwidth)+intval($btm1)+intval($btm2)).'px';
if ($decoration == 1) $switcher_content_frame_margin = (intval($btwidth)+intval($btm1)+intval($btm2)).'px 0';
$insert_style .= '#'.$alias.'-switcher .content-frame{width:'.$switcher_content_frame_width.';height:'.$switcher_content_frame_height.';margin:'.$switcher_content_frame_margin.';}';

// left/right button css
$lr_mt = $itemshowtitle ? intval($btheight)+intval($itemtitleheight) : intval($btheight);
if ($decoration == 1) $lr_mt = $btwidth;
$l_pos_css = 'left:'.$btm1.';margin-top:-'.($lr_mt/2).'px;';
if ($decoration == 1) $l_pos_css = 'top:'.$btm1.';left:50%;margin-left:-'.($lr_mt/2).'px;';
$insert_style .= '#'.$alias.'-switcher .left-button{width:'.$btwidth.';height:'.$btheight.';'.$l_pos_css.($btbg ? 'background-image:url('.$base.$btbg.');background-position:'.(-1*$btbg_pos['x']).'px '.(-1*$btbg_pos['y']).'px;' : '').'}'.
				($btbg ? '#'.$alias.'-switcher .left-button:hover{background-position:'.(-1*$btbg_pos['x']).'px '.(-1*$btbg_pos['y']-intval($btheight)).'px;}' : '');
$r_pos_css = 'right:'.$btm1.';margin-top:-'.($lr_mt/2).'px;';
if ($decoration == 1) $r_pos_css = 'top:auto;bottom:'.$btm1.';left:50%;margin-left:-'.($lr_mt/2).'px;';
$insert_style .= '#'.$alias.'-switcher .right-button{width:'.$btwidth.';height:'.$btheight.';'.$r_pos_css.($btbg ? 'background-image:url('.$base.$btbg.');background-position:'.(-1*$btbg_pos['x']-intval($btwidth)).'px '.(-1*$btbg_pos['y']).'px;' : '').'}'.
				($btbg ? '#'.$alias.'-switcher .right-button:hover{background-position:'.(-1*$btbg_pos['x']-intval($btwidth)).'px '.(-1*$btbg_pos['y']-intval($btheight)).'px;}' : '');

// item css
$item_width = $use100 ? '100%' : $itemouterwidth;
$item_content_width = $use100 ? '100%' : $iteminnerwidth;
$item_content_margin = $use100 ? 0 : ((intval($itemouterheight)-intval($iteminnerheight))/2).'px '.((intval($itemouterwidth)-intval($iteminnerwidth))/2).'px';
$moveable_width = (intval($item_width)*$show).'px';
if ($decoration == 1) $moveable_width = $item_width;
$insert_style .= ($use100 ? '#'.$alias.'-switcher{min-width:'.$use100min.'}' : '').'#'.$alias.'-switcher .moveable{'.($use100 ? 'min-width:'.$use100min.';' : '').'width:'.$moveable_width.';}'.
				'.'.$alias.'-switcher .moveable .item{width:'.$item_width.';height:'.$item_height.';'.($itembg ? 'background-image:url('.$base.$itembg.');background-position:'.(-1*$itembg_pos['x']).'px '.(-1*$itembg_pos['y']).'px' : '').'}'.
				'#'.$alias.'-switcher .moveable .item .item-content{width:'.$item_content_width.';height:'.$iteminnerheight.';margin:'.$item_content_margin.'}';
if ($itembg) $insert_style .= '#'.$alias.'-switcher .moveable .active,#'.$alias.'-switcher .moveable .item:hover{background-position:'.(-1*$itembg_pos['x']).'px '.(-1*$itembg_pos['y']-intval($item_height)).'px;}';

// pagination css
$insert_style .= '#'.$alias.'-switcher .bigpagination{width:'.$pagination_width.'px;height:'.$pagebtheight.';'.$pagination_css.'}';
$pagination_margin_css = 'margin:0 '.(intval($pagebtm)/2).'px;_margin:0 '.(intval($pagebtm)/4).'px;';
if ($decoration == 1) $pagination_margin_css = 'margin:'.(intval($pagebtm)/2).'px 0;_margin:'.(intval($pagebtm)/4).'px 0;';
if (!$pagebtuseimg && $show_pagination_bts) {
	$insert_style .= '#'.$alias.'-switcher .bigpagination .pagination-b{width:'.$pagebtwidth.';height:'.$pagebtheight.';line-height:'.$pagebtheight.';'.$pagination_margin_css.($pagebtbg ? 'background-image:url('.$base.$pagebtbg.');background-position:'.(-1*$pagebtbg_pos['x']).'px '.(-1*$pagebtbg_pos['y']).'px;' : '').'}'.
					'#'.$alias.'-switcher .bigpagination .pagination-b:hover,#'.$alias.'-switcher .bigpagination .pagination-b.active{background-position:'.(-1*$pagebtbg_pos['x']).'px '.(-1*$pagebtbg_pos['y']-intval($pagebtheight)).'px}';
}

// Item title css
if ($itemshowtitle) $insert_style .= '#'.$alias.'-switcher .moveable .item h1{width:'.$item_width.';height:'.$itemtitleheight.';line-height:'.$itemtitleheight.';}';
$document->addStyleDeclaration($insert_style);

if ($params->get('showview')) {
	$insert_style = '';
	
	// switcher_view css
	$insert_style .= '#'.$alias.'-view{'.($viewbg ? 'background:url('.$base.$viewbg.') '.(-1*$viewbg_pos['x']).'px '.(-1*$viewbg_pos['y']).'px no-repeat;' : '').'width:'.$viewwidth.';height:'.$viewheight.';}';
	
	// scroll_area css
	$margin_item_w = (intval($viewheight)-intval($viewitemheight))/2;
	$margin_item_h = (intval($viewwidth)-intval($viewitemwidth))/2;
	$insert_style .= '#'.$alias.'-view .'.$alias.'-scroll-area{width:'.$viewitemwidth.';height:'.$viewitemheight.';margin:'.($margin_item_w > 0 ? $margin_item_w : 0).'px '.($margin_item_h > 0 ? $margin_item_h : 0).'px;}';
	
	// one_view css
	$insert_style .= '#'.$alias.'-view .one-view{width:'.$viewitemwidth.';height:'.$viewitemheight.';}';
	
	// desc css
	$desc_padding = (intval($viewitemwidth)-intval($descwidth))/2;
	$insert_style .= '#'.$alias.'-view .one-view-desc{width:'.$descwidth.';height:'.$descheight.';padding:'.$descm.' '.($desc_padding > 0 ? $desc_padding : 0).'px;}';
	
	// desc background css
	$insert_style .= '#'.$alias.'-view .one-view-bg{width:'.$viewitemwidth.';height:'.(intval($descheight)+intval($descm)*2).'px;background-color:'.$descbg.';opacity:'.($descopacity/100).';filter:alpha(opacity='.$descopacity.');}';
	
	$document->addStyleDeclaration($insert_style);
?>
<div id="<?php echo $alias; ?>-view" class="switcher-view <?php echo $alias; ?>-view<?php echo in_array($switcherfill[1],$float_switchers) ? $float_view[$switcherfill[1]] : ''; ?>">
	<div class="switcher-scroll-area <?php echo $alias; ?>-scroll-area">
		<?php
		if (count($items)) {
			foreach ($items as $n=>$item) {
		?>
		<div id="<?php echo $n.'_item'; ?>" style="background:url(<?php echo $base.MulanImageUtil::thumbimage($item->image,intval($viewitemwidth),intval($viewitemheight)); ?>) center center no-repeat;<?php echo $n > 0 ? 'display:none;' : '' ?>" class="one-view">
			<?php
			if ($params->get('showdesc')) {
			?>
			<div class="one-view-desc" style="z-index:<?php echo $n+1; ?>;">
				<div class="one-view-content">
					<h1><?php echo $item->title; ?></h1>
					<span><?php echo MulanStringUtil::substr_zh($item->description,90,'...'); ?></span>
				</div>
				<div class="one-view-bg"></div>
			</div>
			<?php
			}
			?>
		</div>
		<?php
			}
		}
		?>
	</div>
</div>
<?php
}
?>
<div <?php echo implode(' ',$switcher_params); ?>>
	<?php echo $isshowbt ? '<a href="javascript:void(0);" class="left-button"></a>' : '' ?>
	<div class="content-frame">
		<div class="moveable">
			<?php
			foreach ($items as $key=>$o) {
				echo $params->get('usea') ? '<a' : ($params->get('openlink') ? ($o->link ? '<a' : '<div') : '<div');
				echo ' id="'.$key.'" ';
				echo $params->get('usea') ? ($o->link && $params->get('openlink')
						? 'target="_blank" href="'.$o->link.'"' : 'href="javascript:void(0);"')
						: ($params->get('openlink') ? ($o->link ? 'target="_blank" href="'.$o->link.'"' : '') : '');
				echo ' class="item active">';
				if ($switcherfill[2] != 'whp') echo '<span class="item-content" style="background-image:url('.$base.MulanImageUtil::thumbimage($o->image,$thumb_item_width,intval($iteminnerheight)).')"></span>';
				if ($switcherfill[2] == 'whp') echo '<img src="'.$base.$o->image.'" style="width:100%">';
				if ($itemshowtitle) echo '<h1>'.$o->title.'</h1>';
				echo $params->get('usea') ? '</a>' : ($params->get('openlink') ? ($o->link ? '</a>' : '</div>') : '</div>');
			}
			?>
			<div class="clear"></div>
		</div>
	</div>
	<?php
	echo $isshowbt ? '<a href="javascript:void(0);" class="right-button"></a>' : '';
	if (!in_array($switcherfill[1],$float_switchers)) {
		echo '<div class="clr"></div>';
	}
	if ($thisTotalPage > 1) {
		$insert_style = '';
		$insert_style .= '#'.$alias.'-switcher .bigpagination .pagination-b{width:'.$pagebtwidth.';height:'.$pagebtheight.';line-height:'.$pagebtheight.';'.$pagination_margin_css.($pagebtbg ? 'background-image:url('.$base.$pagebtbg.');' : '').'}';
	?>
	<div<?php echo isset($show_pagination_bts) && $show_pagination_bts == 0 ? ' style="display:none;"' : ''; ?> class="bigpagination">
		<?php
			for ($i = 1; $i <= $thisTotalPage; $i++) {
				$insert_style .= '#'.$alias.'-switcher .bigpagination #'.$alias.'-pagination-'.$i.'{background-position:'.(-1*($i-1)*intval($pagebtwidth)-$pagebtbg_pos['x']).'px '.(-1*$pagebtbg_pos['y']).'px;}'.
								'#'.$alias.'-switcher .bigpagination #'.$alias.'-pagination-'.$i.':hover,#'.$alias.'-switcher .bigpagination #'.$alias.'-pagination-'.$i.'.active{background-position:'.(-1*($i-1)*intval($pagebtwidth)-$pagebtbg_pos['x']).'px '.(-1*intval($pagebtheight)-$pagebtbg_pos['y']).'px}';
		?>
			<a href="javascript:void(0);" id="<?php echo $alias; ?>-pagination-<?php echo $i; ?>" class="pagination-b <?php echo $i == 1 ? 'active' : ''; ?>" title="<?php echo $i;?>"><?php echo $pagebtshownum ? $i : ''; ?></a>
		<?php } ?>
		<div class="clr"></div>
	</div>
	<?php
		if ($pagebtuseimg) $document->addStyleDeclaration($insert_style);
	}
	
	if (in_array($switcherfill[1],$float_switchers)) {
		echo '<div class="clr"></div>';
	}
	?>
</div>
<?php
if (in_array($switcherfill[1],$float_switchers)) {
	echo '<div class="clr"></div>';
}
?>
<script>
	$(document).ready(function(){
		horizontalSwitcher('<?php echo $alias; ?>');
	});
	<?php
	if ($switcherfill[2] == 'whp'){
	?>
	$('html,body').css({'width':'100%','height':'100%'});
	var body_width = 0,body_height = 0;
	var scale_x_y = 1920/1080;
	function resizeWD(){
		body_width = parseInt(document.body.offsetWidth);
		body_height = parseInt(document.body.offsetHeight);
		if (body_width/body_height < scale_x_y) {
			$('#<?php echo $alias; ?>-switcher .item img').css({'height':body_height+'px','width':(scale_x_y*body_height)+'px','margin-left':(-1*(scale_x_y*body_height-body_width)/2)+'px'});
		} else {
			$('#<?php echo $alias; ?>-switcher .item img').css({'height':'auto','width':'100%','margin-left':'0px'});
		}
	}
	resizeWD();
	window.onresize = resizeWD;
	<?php
	}
	?>
</script>