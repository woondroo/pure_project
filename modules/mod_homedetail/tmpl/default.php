<?php
/**
 * @version		$Id: default.php 20974 2011-03-16 14:14:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	mod_homedetail
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('mulan.mltools');

$document = JFactory::getDocument();
$base = JURI::base();
$title = $item->title;
$img = $item->image;
$link = $item->link;
$desc = strip_tags($item->description);

$module_alias = $params->get('module_alias');
$module_link = $params->get('module_link');
$module_link_open = $params->get('module_link_open');
$module_width = $params->get('module_width');
$module_top_bg = $params->get('module_top_bg');
$module_top_pos = MulanToolsUtil::getMaterialPos($module_top_bg);
$module_top_bg = $module_top_pos['img'];

$module_content_bg = $params->get('module_content_bg');
$module_content_pos = MulanToolsUtil::getMaterialPos($module_content_bg);
$module_content_bg = $module_content_pos['img'];

//$modulebg_x = $params->get('modulebg_x');
//$modulebg_y = $params->get('modulebg_y');
//$modulebg_pos_x = array('left','center','right');
//$modulebg_pos_y = array('top','center','bottom');

//之前用法：background:url('.$base.$module_bg.') '.$modulebg_pos_x[$modulebg_x-1].' '.$modulebg_pos_y[$modulebg_y-1].' no-repeat;

$topbt_show = $params->get('topbt_show');
$topbt_height = $params->get('topbt_height');
$topbt_margin = $params->get('topbt_margin');
$topbt_text = $params->get('topbt_text');
$topbt_margin_vals = explode(' ',$topbt_margin);

$more_show = $params->get('more_show');
$more_text = $params->get('more_text');
$more_showtext = $params->get('more_showtext');
$more_width = $params->get('more_width');
$more_height = $params->get('more_height');
$more_bg = $params->get('more_bg');
$more_bg_pos = MulanToolsUtil::getMaterialPos($more_bg);
$more_bg = $more_bg_pos['img'];

$more_link = $params->get('more_link', $link);
$more_pos = $params->get('more_pos');
$more_pos_vals = array('float:left;','float:right;');

$content_height = $params->get('content_height');
$content_margin = $params->get('content_margin');
$content_margin_vals = explode(' ',$content_margin);

$image_pos = $params->get('image_pos');
$image_width = $params->get('image_width');
$image_height = $params->get('image_height');
$image_link = $params->get('image_link', $link);
$image_pos_vals = array('left','left','right','left');

$desc_show = $params->get('desc_show');
$desc_length = $params->get('desc_length');
$desc_width = $params->get('desc_width');
$desc_height = $params->get('desc_height');
$desc_pos_vals = array('right','left','left','left');

$is_more_link = $module_link && $more_link && $more_show;
$is_image_link = $module_link && ($link || $image_link);

$insert_style = '';
$html_str = '';

$desc_image_margin_top = intval($content_height) - intval($image_height) - intval($desc_height);

$module_height = ($topbt_show ? intval($topbt_height) : 0)+intval($content_height);
$insert_style .= '.home-detail-'.$module_alias.'{width:'.$module_width.';height:'.$module_height.'px;}';

$top_width = intval($module_width)-($topbt_margin_vals[1]+$topbt_margin_vals[3]);
$top_height = intval($topbt_height)-($topbt_margin_vals[0]+$topbt_margin_vals[2]);
foreach ($topbt_margin_vals as &$v) $v .= 'px';
$insert_style .= '.home-detail-'.$module_alias.' .home-detail-top{width:'.$top_width.'px;height:'.$top_height.'px;padding:'.implode(' ',$topbt_margin_vals).';'.($module_top_bg ? 'background:url('.$base.$module_top_bg.') '.(-1*$module_top_pos['x']).'px '.(-1*$module_top_pos['y']).'px;' : '').'}';

$insert_style .= '.home-detail-'.$module_alias.' .home-detail-more{'.$more_pos_vals[$more_pos-1].'width:'.$more_width.';height:'.$more_height.';line-height:'.$more_height.';'.($more_bg ? 'background-image:url('.$base.$more_bg.');background-position:'.(-1*$more_bg_pos['x']).'px '.(-1*$more_bg_pos['y']).'px;' : '').'}';
if ($more_bg) $insert_style .= '.home-detail-'.$module_alias.' .home-detail-more:hover{background-position:'.(-1*$more_bg_pos['x']).'px '.(-1*$more_bg_pos['y']-intval($more_height)).'px;}';

$content_width = intval($module_width)-($content_margin_vals[1]+$content_margin_vals[3]);
$content_height = ($desc_show ? intval($desc_height) : 0)+intval($image_height)-($content_margin_vals[0]+$content_margin_vals[2]);
foreach ($content_margin_vals as &$v) $v .= 'px';
$insert_style .= '.home-detail-'.$module_alias.' .home-detail-content{width:'.$content_width.'px;height:'.$content_height.'px;padding:'.implode(' ',$content_margin_vals).';'.($module_content_bg ? 'background:url('.$base.$module_content_bg.') '.(-1*$module_content_pos['x']).'px '.(-1*$module_content_pos['y']).'px;' : '').'}';

$insert_style .= '.home-detail-'.$module_alias.' .content-img{'.($desc_image_margin_top > 0 && $image_pos == 4 ? 'margin-top:'.$desc_image_margin_top.'px;' : '').'width:'.$image_width.';height:'.$image_height.';float:'.$image_pos_vals[$image_pos-1].';'.($img ? 'background:url('.$base.$img.') center center no-repeat;' : '').'}';
if ($desc_show) $insert_style .= '.home-detail-'.$module_alias.' .content-desc{'.($desc_image_margin_top > 0 && $image_pos == 2 ? 'margin-top:'.$desc_image_margin_top.'px;' : '').'width:'.$desc_width.';height:'.$desc_height.';float:'.$desc_pos_vals[$image_pos-1].';}';

if ($insert_style) $document->addStyleDeclaration($insert_style);
?>
<div id="home-detail-<?php echo $module_alias; ?>" class="home-detail home-detail-<?php echo $module_alias; ?>">
	<?php
	if ($topbt_show) {
		$html_str .= '<div class="home-detail-top">'.($topbt_text ? '<span>'.$topbt_text.'</span>' : '');
		if ($is_more_link) {
			$html_str .= '<a class="home-detail-more" href="'.$more_link.'"'.($module_link_open ? ' target="_blank"' : '').'>'.($more_showtext ? $more_text : '').'</a>';
		}
		$html_str .= '</div>';
	}
	$html_str .= '<div class="home-detail-content">';
	
	$img_html = ($is_image_link ? '<a href="'.($image_link ? $image_link : $link).'"'.($module_link_open ? ' target="_blank"' : '') : '<div').' class="content-img">';
	$img_html .= ($is_image_link ? '</a>' : '</div>');
	
	$desc_html = '<div class="content-desc">';
	$desc_html .= MulanStringUtil::substr_zh($desc,$desc_length,'...');
	$desc_html .= '</div>';
	
	if ($image_pos == 4) {
		if ($desc_show) $html_str .= $desc_html;
		$html_str .= $img_html;
	} else {
		$html_str .= $img_html;
		if ($desc_show) $html_str .= $desc_html;
	}
	
	$html_str .= '</div>';
	
	echo $html_str;
	?>
</div>
