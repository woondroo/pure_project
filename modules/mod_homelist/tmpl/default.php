<?php
/**
 * @version		$Id: default.php 20974 2011-03-16 14:14:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	mod_homelist
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$base = JURI::base();
$listway = $params->get('listway');
$listway_vals = explode('-',$listway);

$count = $params->get('count');
$module_mix = $params->get('module_mix');
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

$more_link = $params->get('more_link');
$more_pos = $params->get('more_pos');
$more_pos_vals = array('float:left;','float:right;');

$content_height = $params->get('content_height');
$content_margin = $params->get('content_margin');
$content_margin_vals = explode(' ',$content_margin);

$image_pos = $params->get('image_pos');
$image_width = $params->get('image_width');
$image_height = $params->get('image_height');

$date_pos = $params->get('date_pos');
$date_width = $params->get('date_width');
$date_height = $params->get('date_height');
$date_end = $params->get('date_end');
$date_bg = $params->get('date_bg');
$date_bg_pos = MulanToolsUtil::getMaterialPos($date_bg);
$date_bg = $date_bg_pos['img'];

$title_length = $params->get('title_length');
$title_width = $params->get('title_width');
$title_height = $params->get('title_height');
$create_title_pre = $params->get('create_title_pre');

$desc_length = $params->get('desc_length');
$desc_width = $params->get('desc_width');
$desc_height = $params->get('desc_height');

$item_width = $params->get('item_width');
$item_height = $params->get('item_height');

$other_item_width = $params->get('other_item_width');
$other_item_height = $params->get('other_item_height');
$other_image_width = $params->get('other_image_width');
$other_image_height = $params->get('other_image_height');
$other_date_width = $params->get('other_date_width');
$other_date_height = $params->get('other_date_height');
$other_date_end = $params->get('other_date_end');
$other_title_length = $params->get('other_title_length');
$other_title_width = $params->get('other_title_width');
$other_title_height = $params->get('other_title_height');
$other_desc_length = $params->get('other_desc_length');
$other_desc_width = $params->get('other_desc_width');
$other_desc_height = $params->get('other_desc_height');
$other_date_bg = $params->get('other_date_bg');
$other_date_pos = MulanToolsUtil::getMaterialPos($other_date_bg);
$other_date_bg = $other_date_pos['img'];

$image_list_scroll_num = $params->get('image_list_scroll_num');
$image_list_bt_width = $params->get('image_list_bt_width');
$image_list_bt_height = $params->get('image_list_bt_height');
$image_list_bt_margin = $params->get('image_list_bt_margin');
$image_list_bt_bg = $params->get('image_list_bt_bg');
$image_list_bt_pos = MulanToolsUtil::getMaterialPos($image_list_bt_bg);
$image_list_bt_bg = $image_list_bt_pos['img'];
$image_list_anim_time = $params->get('image_list_anim_time');

$float_vals = array('left','right');

$is_more_link = $module_link && $more_link && $more_show;

$insert_style = '';
$html_str = '';

$module_height = ($topbt_show ? intval($topbt_height) : 0)+intval($content_height);
$insert_style .= '.home-list-'.$module_alias.'{width:'.$module_width.';height:'.$module_height.'px;}';

$top_width = intval($module_width)-($topbt_margin_vals[1]+$topbt_margin_vals[3]);
$top_height = intval($topbt_height)-($topbt_margin_vals[0]+$topbt_margin_vals[2]);
foreach ($topbt_margin_vals as &$v) $v .= 'px';
$insert_style .= '.home-list-'.$module_alias.' .home-list-top{width:'.$top_width.'px;height:'.$top_height.'px;padding:'.implode(' ',$topbt_margin_vals).';'.($module_top_bg ? 'background:url('.$base.$module_top_bg.') '.(-1*$module_top_pos['x']).'px '.(-1*$module_top_pos['y']).'px;' : '').'}';

$insert_style .= '.home-list-'.$module_alias.' .home-list-more{'.$more_pos_vals[$more_pos-1].'width:'.$more_width.';height:'.$more_height.';line-height:'.$more_height.';'.($more_bg ? 'background-image:url('.$base.$more_bg.');background-position:'.(-1*$more_bg_pos['x']).'px '.(-1*$more_bg_pos['y']).'px;' : '').'}';
if ($more_bg) $insert_style .= '.home-list-'.$module_alias.' .home-list-more:hover{background-position:'.(-1*$more_bg_pos['x']).'px '.(-1*$more_bg_pos['y']-intval($more_height)).'px;;}';

$content_width = intval($module_width)-($content_margin_vals[1]+$content_margin_vals[3]);
$content_height = intval($content_height)-($content_margin_vals[0]+$content_margin_vals[2]);
foreach ($content_margin_vals as &$v) $v .= 'px';
$insert_style .= '.home-list-'.$module_alias.' .home-list-content{width:'.$content_width.'px;height:'.$content_height.'px;padding:'.implode(' ',$content_margin_vals).';'.($module_content_bg ? 'background:url('.$base.$module_content_bg.') '.(-1*$module_content_pos['x']).'px '.(-1*$module_content_pos['y']).'px;' : '').'}';

$img_float_where = $image_pos-1;
$date_float_where = $date_pos-1;

$img_style 			= '.home-list-'.$module_alias.' .content-img{width:'.$image_width.';height:'.$image_height.';float:'.$float_vals[$img_float_where].';}';
$other_img_style 	= '.home-list-'.$module_alias.' .content-other-img{width:'.$other_image_width.';height:'.$other_image_height.';float:'.$float_vals[$img_float_where].';}';
$title_style 		= '.home-list-'.$module_alias.' .content-title{width:'.$title_width.';height:'.$title_height.';line-height:'.$title_height.';}';
$other_title_style 	= '.home-list-'.$module_alias.' .content-other-title{width:'.$other_title_width.';height:'.$other_title_height.';line-height:'.$other_title_height.';}';
$desc_style 		= '.home-list-'.$module_alias.' .content-desc{width:'.$desc_width.';height:'.$desc_height.';'.($listway_vals[1] == 'id' ? 'float:left;' : '').'}';
$other_desc_style 	= '.home-list-'.$module_alias.' .content-other-desc{width:'.$other_desc_width.';height:'.$other_desc_height.';}';
$date_style 		= '.home-list-'.$module_alias.' .content-date{'.($date_bg ? 'background-image:url('.$base.$date_bg.');background-position:'.(-1*$date_bg_pos['x']).'px '.(-1*$date_bg_pos['y']).'px;' : '').'width:'.$date_width.';height:'.$date_height.';line-height:'.$date_height.';float:'.$float_vals[$date_pos].';}'
					.($date_bg ? '.home-list-'.$module_alias.' .home-list-one:hover .content-date{background-position:'.(-1*$date_bg_pos['x']).'px '.(-1*$date_bg_pos['y']-intval($date_height)).'px;}' : '');
$date_img_style 	= '.home-list-'.$module_alias.' .content-img-date{'.($date_bg ? 'background-image:url('.$base.$date_bg.');background-position:'.(-1*$date_bg_pos['x']).'px '.(-1*$date_bg_pos['y']).'px;' : '').'width:'.$date_width.';height:'.$date_height.';float:'.$float_vals[$date_pos].';}'
					.($date_bg ? '.home-list-'.$module_alias.' .home-list-one:hover .content-date{background-position:'.(-1*$date_bg_pos['x']).'px '.(-1*$date_bg_pos['y']-intval($date_height)).'px;}' : '');
$other_date_style 	= '.home-list-'.$module_alias.' .content-other-date{'.($other_date_bg ? 'background-image:url('.$base.$other_date_bg.');background-position:'.(-1*$other_date_pos['x']).'px '.(-1*$other_date_pos['y']).'px;' : '').'width:'.$other_date_width.';height:'.$other_date_height.';line-height:'.$other_date_height.';}'
					.($other_date_bg ? '.home-list-'.$module_alias.' .home-list-one:hover .content-date{background-position:'.(-1*$other_date_pos['x']).'px '.(-1*$other_date_pos['y']-intval($other_date_height)).'px;}' : '');
?>
<div id="home-list-<?php echo $module_alias; ?>" class="home-list home-list-<?php echo $module_alias; ?>">
	<?php
	if ($topbt_show) {
		$html_str .= '<div class="home-list-top">'.($topbt_text ? '<span>'.$topbt_text.'</span>' : '');
		if ($is_more_link) {
			$html_str .= '<a class="home-list-more" href="'.$more_link.'"'.($module_link_open ? ' target="_blank"' : '').'>'.($more_showtext ? $more_text : '').'</a>';
		}
		$html_str .= '</div>';
	}
	
	$html_str .= '<div class="home-list-content">';
	if ($listway == 'i-ii-il' || $listway == 'i-id-il') $html_str .= '<a href="javascript:void(0);" class="list-img-bt content-list-img-lb"></a><div class="list-img-container"><div class="list-img-scroll">';
	
	foreach ($items as $key=>$item) {
		if ($module_mix && $key > 0) {
			$img_float_where == 0 ? $img_float_where = 1 : $img_float_where = 0;
			if ($listway == 'i-tt-dd' && $key > 1) $date_float_where == 0 ? $date_float_where = 1 : $date_float_where = 0;
			if ($listway != 'i-tt-dd') $date_float_where == 0 ? $date_float_where = 1 : $date_float_where = 0;
		}
		$html_str .= ($module_link ? '<a href="'.$item->link.'"'.($module_link_open ? ' target="_blank"' : '') : '<div').' id="home-list-'.$module_alias.'-'.$item->id.'" class="home-list-one">';
		
		if ($listway_vals[0] == 'i') {
			$img_html = '<div id="img_'.$item->id.'" class="'.($listway == 'i-tt-dd' && $key == 0 ? 'content-other-img' : 'content-img').'" style="background:url('.$base.$item->image.') center center no-repeat;"></div>';
		} else if ($listway_vals[0] == 'bd') {
			$date = strtotime($item->addtime);
			$day = date('d',$date);
			$min_date = date('Y m l',$date);
			$min_date_vals = explode(' ',$min_date);
			$min_date_vals[2] = substr($min_date_vals[2],0,3);
			$date_html = '<div id="date_'.$item->id.'" class="content-img-date">' .
							'<span class="content-date-b">'.$day.$date_end.'</span>' .
							'<span class="content-date-m">'.implode(' ',$min_date_vals).'</span>' .
						'</div>';
		}
		
		if ($listway_vals[1] == 't') {
			$title_html = '<div id="title_'.$item->id.'" class="content-title">'.MulanStringUtil::substr_zh($item->title,$title_length,'...').'</div>';
		} else if ($listway_vals[1] == 'tt') {
			$date = strtotime($item->addtime);
			$min_date = date('Y-m-d',$date);
			$date_html = '<div id="date_'.$item->id.'" class="content-date">'.$min_date.'</div>';
			
			if ($key == 0) {
				$title_html = '<div id="title_'.$item->id.'" class="content-other-title">'.MulanStringUtil::substr_zh($item->title,$other_title_length,'...').'</div>';
			} else{
				$title_html = '<div id="title_'.$item->id.'" class="content-title">'.MulanStringUtil::substr_zh($item->title,$title_length,'...').'</div>';
			}
		} else if ($listway_vals[1] == 'ii') {
			$img_list_html = '<div id="listimg_'.$item->id.'" class="content-list-img">'.$img_html.'</div>';
		} else if ($listway_vals[1] == 'id') {
			$img_list_html = '<div id="listimg_'.$item->id.'" class="content-list-img">' .
								$img_html .
								'<div id="desc_'.$item->id.'" class="content-desc">'.MulanStringUtil::substr_zh(strip_tags($item->description),$desc_length,'...').'</div>' .
							'</div>';
		}
		
		if ($listway_vals[2] == 'd') {
			$desc_html = '<div id="desc_'.$item->id.'" class="content-desc">'.MulanStringUtil::substr_zh(strip_tags($item->description),$desc_length,'...').'</div>';
		} else if ($listway_vals[2] == 'dd') {
			$desc_html = '<div id="desc_'.$item->id.'" class="content-other-desc">'.MulanStringUtil::substr_zh(strip_tags($item->description),$other_desc_length,'...').'</div>';
		} else if ($listway_vals[2] == 'dt') {
			$date = strtotime($item->addtime);
			$min_date = date('Y-m-d',$date);
			$date_html = '<div id="date_'.$item->id.'" class="content-date">'.$date_end.$min_date.'</div>';
			
			$create_title = '<div id="auto_title_'.$item->id.'" class="auto-content-title">'.$create_title_pre.'00'.($key+1).'</div>';
		}
		
		if ($listway == 'i-i-i') {
			if ($key == 0) $insert_style .= $img_style;
			$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$item_width.';height:'.$item_height.';}';
			
			$html_str .= $img_html;
		} else if ($listway == 'i-t-d') {
			if ($key == 0) {
				$insert_style .= $img_style;
				$insert_style .= $title_style;
				$insert_style .= $desc_style;
				
				$td_height = intval($title_height)+intval($desc_height);
				$td_margin = (intval($image_height)-$td_height)/2;
				if ($td_margin > 0) $insert_style .= '.home-list-'.$module_alias.' .content-td{height:'.$td_height.'px;margin:'.$td_margin.'px 0;}';
				if ($td_margin < 0) $insert_style .= '.home-list-'.$module_alias.' .content-img{margin:'.abs($td_margin).'px 0;}';
			}
			$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$item_width.';height:'.$item_height.';}';
			$insert_style .= '#img_'.$item->id.'{float:'.$float_vals[$img_float_where].';}'.
							'#td_'.$item->id.'{float:'.$float_vals[1-$img_float_where].';}';
			
			$html_str .= $img_html;
			$html_str .= '<div id="td_'.$item->id.'" class="content-td">';
			$html_str .= $title_html;
			$html_str .= $desc_html;
			$html_str .= '</div>';
		} else if ($listway == 'bd-t-d') {
			if ($key == 0) {
				$insert_style .= $date_img_style;
				$insert_style .= $title_style;
				$insert_style .= $desc_style;
				
				$td_height = intval($title_height)+intval($desc_height);
				$td_margin = (intval($date_height)-$td_height)/2;
				if ($td_margin > 0) $insert_style .= '.home-list-'.$module_alias.' .content-td{height:'.$td_height.'px;margin:'.$td_margin.'px 0;}';
				if ($td_margin < 0) $insert_style .= '.home-list-'.$module_alias.' .content-img-date{margin:'.abs($td_margin).'px 0;}';
			}
			$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$item_width.';height:'.$item_height.';}';
			$insert_style .= '#date_'.$item->id.'{float:'.$float_vals[$date_float_where].';}'.
							'#td_'.$item->id.'{float:'.$float_vals[1-$date_float_where].';}';
			
			$html_str .= $date_html;
			$html_str .= '<div id="td_'.$item->id.'" class="content-td">';
			$html_str .= $title_html;
			$html_str .= $desc_html;
			$html_str .= '</div>';
		} else if ($listway == 'i-tt-dd') {
			if ($key == 0) {
				$insert_style .= $date_style;
				$insert_style .= $title_style;
				$insert_style .= $other_img_style;
				$insert_style .= $other_title_style;
				$insert_style .= $other_desc_style;
				
				$insert_style .= '#img_'.$item->id.'{float:'.$float_vals[$img_float_where].';}'.
											'#td_'.$item->id.'{float:'.$float_vals[1-$img_float_where].';}';
				
				$td_height = intval($other_title_height)+intval($other_desc_height);
				$td_margin = (intval($other_image_height)-$td_height)/2;
				
				$td_down_margin = intval($content_height)-intval($other_item_height)-($count-1)*intval($item_height);
				$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{margin-bottom:'.$td_down_margin.'px}';
				if ($td_margin > 0) $insert_style .= '#td_'.$item->id.'{height:'.$td_height.'px;margin:'.$td_margin.'px 0;}';
				if ($td_margin < 0) $insert_style .= '#img_'.$item->id.'{margin:'.abs($td_margin).'px 0;}';
			} else if ($key == 1) {
				$td_height = max(intval($date_height),intval($title_height));
				$insert_style .= '.home-list-'.$module_alias.' .content-td{height:'.$td_height.'px;}';
			}
			
			if ($key == 0) {
				$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$other_item_width.';height:'.$other_item_height.';}';
			} else if ($key > 0) {
				$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$item_width.';height:'.$item_height.';}';
				$insert_style .= '#date_'.$item->id.'{float:'.$float_vals[$date_float_where].';}'.
								'#td_'.$item->id.'{float:'.$float_vals[1-$date_float_where].';}';
			}
			
			if ($key == 0) $html_str .= $img_html;
			if ($key > 0) $html_str .= $date_html;
			$html_str .= '<div id="td_'.$item->id.'" class="content-td">';
			$html_str .= $title_html;
			if ($key == 0) $html_str .= $desc_html;
			$html_str .= '</div>';
		} else if ($listway == 'i-ii-il' || $listway == 'i-id-il') {
			if ($key == 0) {
				$insert_style .= $img_style;
				if ($listway_vals[1] == 'id') $insert_style .= $desc_style;
				$insert_style .= '.home-list-'.$module_alias.' .home-list-one{width:'.$item_width.';height:'.$item_height.';}';
				
				$img_m_x = max((intval($item_width)-intval($image_width))/2, 0);
				$img_m_y = max((intval($item_height)-intval($image_height)-($listway_vals[1] == 'id' ? intval($desc_height) : 0))/2, 0);
				$img_h = intval($image_height)+($listway_vals[1] == 'id' ? intval($desc_height) : 0);
				$insert_style .= '.home-list-'.$module_alias.' .content-list-img{margin:'.$img_m_y.'px '.$img_m_x.'px;width:'.$image_width.';height:'.$img_h.'px;}';
				
				$container_width = intval($item_width)*$image_list_scroll_num;
				$scroll_width = intval($item_width)*count($items);
				$img_list_m_x = max(($content_width-$container_width)/2, 0);
				$insert_style .= '.home-list-'.$module_alias.' .list-img-container{margin:0 '.$img_list_m_x.'px;width:'.$container_width.'px;height:'.$item_height.';}';
				$insert_style .= '.home-list-'.$module_alias.' .list-img-scroll{width:'.$scroll_width.'px;height:'.$item_height.';}';
				
				$insert_style .= '.home-list-'.$module_alias.' .list-img-bt{background-image:url('.$base.$image_list_bt_bg.');width:'.$image_list_bt_width.';height:'.$image_list_bt_height.';}';
				$img_b_pos = $img_list_m_x-intval($image_list_bt_margin)-intval($image_list_bt_width);
				$img_b_mt = -1*(intval($image_list_bt_width)+($listway_vals[1] == 'id' ? intval($desc_height) : 0))/2;
				$insert_style .= '.home-list-'.$module_alias.' .content-list-img-lb{background-position:'.(-1*$image_list_bt_pos['x']).'px '.(-1*$image_list_bt_pos['y']).'px;left:'.$img_b_pos.'px;margin-top:'.$img_b_mt.'px;}.home-list-'.$module_alias.' .content-list-img-lb:hover{background-position:'.(-1*$image_list_bt_pos['x']).'px '.(-1*$image_list_bt_pos['y']-intval($image_list_bt_height)).'px;}';
				$insert_style .= '.home-list-'.$module_alias.' .content-list-img-rb{background-position:'.(-1*$image_list_bt_pos['x']-intval($image_list_bt_width)).'px '.(-1*$image_list_bt_pos['y']).'px;right:'.$img_b_pos.'px;margin-top:'.$img_b_mt.'px;}.home-list-'.$module_alias.' .content-list-img-rb:hover{background-position:'.(-1*$image_list_bt_pos['x']-intval($image_list_bt_width)).'px '.(-1*$image_list_bt_pos['y']-intval($image_list_bt_height)).'px;}';
				
				$fun_alias = str_replace('-','_',$module_alias);
				$insert_script = '$(document).ready(function(){' .
					'$("#home-list-'.$module_alias.' .content-list-img-lb").click(function(){moveHomeListPS_'.$fun_alias.'(-1);});' .
					'$("#home-list-'.$module_alias.' .content-list-img-rb").click(function(){moveHomeListPS_'.$fun_alias.'(1);});' .
					'var isrun=false;' .
					'var item_width_'.$fun_alias.'='.intval($item_width).';' .
					'var count_'.$fun_alias.'='.count($items).';' .
					'var showCount_'.$fun_alias.'='.$image_list_scroll_num.';' .
					'var moveleft_'.$fun_alias.'=0;'.
					'function moveHomeListPS_'.$fun_alias.'(where){' .
						'moveleft_'.$fun_alias.'=parseInt($("#home-list-'.$module_alias.' .list-img-scroll").css("left"));'.
						'if(count_'.$fun_alias.'<=showCount_'.$fun_alias.'||isrun==true)return;'.
						'isrun=true;'.
						'if(where==1){' .
							'moveleft_'.$fun_alias.'-=item_width_'.$fun_alias.'*showCount_'.$fun_alias.';'.
							'var rightBorder=-1*item_width_'.$fun_alias.'*(count_'.$fun_alias.'-3);rightBorder=rightBorder>0?0:rightBorder;'.
							'if(moveleft_'.$fun_alias.'<=rightBorder)moveleft_'.$fun_alias.'=rightBorder;'.
						'}else if(where==-1){' .
							'moveleft_'.$fun_alias.'+=item_width_'.$fun_alias.'*showCount_'.$fun_alias.';'.
							'if(moveleft_'.$fun_alias.'>0)moveleft_'.$fun_alias.'=0;'.
						'}' .
						'$("#home-list-'.$module_alias.' .list-img-scroll").animate({left:moveleft_'.$fun_alias.'+"px"},'.$image_list_anim_time.',function(){isrun=false;});'.
					'}'.
				'});';
			}
			
			$html_str .= $img_list_html;
		} else if ($listway == 'i-t-dt') {
			if ($key == 0) {
				$insert_style .= $img_style;
				$insert_style .= $auto_title_style;
				$insert_style .= $date_style;
				$insert_style .= $title_style;
				
				$td_height = intval($other_title_height)+intval($title_height)+intval($date_height);
				$td_margin = (intval($item_height)-intval($image_height)-$td_height)/2;
				if ($td_margin > 0) $insert_style .= '.home-list-'.$module_alias.' .content-td{height:'.$td_height.'px;margin:'.$td_margin.'px 0;}';
			}
			
			$insert_style .= '#home-list-'.$module_alias.'-'.$item->id.'{width:'.$item_width.';height:'.$item_height.';'.($key % 3 == 0 ? 'margin-left:0px;' : '').'}';
			
			$html_str .= $img_html;
			$html_str .= '<div id="td_'.$item->id.'" class="content-td">';
			$html_str .= $create_title;
			$html_str .= $date_html;
			$html_str .= $title_html;
			$html_str .= '</div>';
		}
		
		$html_str .= $module_link ? '</a>' : '</div>';
	}
	
	if ($listway == 'i-ii-il' || $listway == 'i-id-il') $html_str .= '</div></div><a href="javascript:void(0);" class="list-img-bt content-list-img-rb"></a>';
	$html_str .= '</div>';
	
	echo $html_str;
	?>
</div>
<?php
if ($insert_style) $document->addStyleDeclaration($insert_style);
if ($insert_script) $document->addScriptDeclaration($insert_script);
?>
