<?php
/**
 * @version		$Id: default.php 21726 2011-07-02 05:46:46Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
jimport('mulan.mldb');

$insert_style = '';

// 循环主导航菜单时，记录当前循环到的主导航菜单 Itemid，便于存储数据！
$cur_parent_id = 0;
// 缓存查询出来的父级分类对象
$catch_cats = array();
// 缓存对应导航下的子菜单个数
$catch_child_nums = array();
// 缓存对应导航下的子菜单对象
$catch_chilidren = array();
// 缓存对应导航下的子菜单 html 代码
$catch_childs_menu = array();
// 主导航菜单的 Itemid 集合
$menuids = array();
// 主导航的个数
$count_mainmenu = 0;
// 有子菜单的主导航 id 集合
$has_child_menuids = array();

$insert_style .= $mainmenu_child_top_style;
if ($mbg && !$musebg) $insert_style .= '#'.$mainmenu_css_type.' .menu li a{background-position:'.(-1*intval($mmargin)-$mbg_pos['x']).'px '.(-1*$mbg_pos['y']).'px;}'.
									'#'.$mainmenu_css_type.' .menu li a:hover,#'.$mainmenu_css_type.' .menu li.active a{background-position:'.(-1*intval($mmargin)-$mbg_pos['x']).'px '.(-1*intval($mheight)-$mbg_pos['y']).'px;}';
foreach ($list as $i => &$item) :
	$menuid = $item->params->get('aliasoptions');
	if (!$menuid && $item->params->get('isopen')) $menuid = $item->id;
	if ($menuid || $item->menutype == 'mainmenu') {
		if ($item->menutype == 'mainmenu' && $menutype == 'mainmenu' && $item->parent_id == 1) {
			if ($mbg && $musebg) $insert_style .= '#'.$mainmenu_css_type.' .item-'.$item->id.' a{background-position:'.(-1*intval($mwidth)*$count_mainmenu-intval($mmargin)-$mbg_pos['x']).'px '.(-1*$mbg_pos['y']).'px;}'.
												'#'.$mainmenu_css_type.' .item-'.$item->id.' a:hover,#'.$mainmenu_css_type.' li#item-'.$item->id.'.active a{background-position:'.(-1*intval($mwidth)*$count_mainmenu-intval($mmargin)-$mbg_pos['x']).'px '.(-1*intval($mheight)-$mbg_pos['y']).'px;}';
			$has_child_menuids[] = $item->id;
			$count_mainmenu ++;
		}
		if ($item->menutype == 'mainmenu' && $item->parent_id == 1 && !$item->deeper) {
			$menuid = $item->id;
		} else if ($item->menutype == 'mainmenu') {
			continue;
		}
		$menuids[] = $menuid;
	}
endforeach;

if ($mcbg && !$mcusebg && count($has_child_menuids)) {
	$pos_val_normal = array((-1*intval($mcmargin)-$mcbg_pos['x']).'px '.(-1*$mcbg_pos['y']).'px' ,(-1*$mcbg_pos['x']).'px '.(-1*intval($mcmargin)-$mcbg_pos['y']).'px');
	$pos_val_hover = array((-1*intval($mcmargin)-$mcbg_pos['x']).'px '.(-1*intval($mcheight)-$mcbg_pos['y']).'px' ,(-1*intval($mcwidth)-$mcbg_pos['x']).' '.(-1*intval($mcmargin)-$mcbg_pos['y']));
	$style_normal = '{background-position:'.$pos_val_normal[$mainmenualign-1].';}';
	$style_active = '{background-position:'.$pos_val_hover[$mainmenualign-1].';}';
	$normal_classes = '';
	$active_classes = '';
	foreach ($has_child_menuids as $mkey=>$mid) {
		$normal_classes .= ($mkey > 0 ? ',' : '').'#'.$mainmenu_css_type.' .menu li#item-'.$mid.' ul li a';
		$active_classes .= ($mkey > 0 ? ',' : '').'#'.$mainmenu_css_type.' .menu li#item-'.$mid.' ul li a:hover,#'.$mainmenu_css_type.' .menu li#item-'.$mid.' ul li.active a';
	}
	$insert_style .= $normal_classes.$style_normal.$active_classes.$style_active;
}

if ($menutype == 'mainmenu') {
	$mainmenu_width = intval($mmargin)*2 + intval($mwidth)*$count_mainmenu;
	$posx = array('left:','left:','right:');
	$posx_val = array('0','50%','0');
	$arrange = array('width:','height:');
	$insert_style .= '#'.$mainmenu_css_type.'{height:'.$mheight.';}#'.$mainmenu_css_type.' .menu{'.$posx[$mainmenupos-1].$posx_val[$mainmenupos-1].';'.($mainmenupos == 2 ? 'margin-left:'.(-1*$mainmenu_width/2).'px;' : '').'width:'.$mainmenu_width.'px;height:'.$mheight.';}#'.$mainmenu_css_type.' .menu li,#'.$mainmenu_css_type.' .menu li a{width:'.$mwidth.';height:'.$mheight.';line-height:'.$mheight.';}'.($mbg ? '#'.$mainmenu_css_type.' .menu li.item-first,#'.$mainmenu_css_type.' .menu li.item-end,#'.$mainmenu_css_type.' .menu li a{background-image:url('.$base.$mbg.');}' : '').
					'#'.$mainmenu_css_type.' .menu .item-first,#'.$mainmenu_css_type.' .menu .item-end{width:'.$mmargin.';}#'.$mainmenu_css_type.' .menu .item-first{background-position:'.(-1*$mbg_pos['x']).'px '.(-1*$mbg_pos['y']).'px;}#'.$mainmenu_css_type.' .menu .item-end{background-position:'.(-1*$mbg_pos['x']-($musebg ? $mainmenu_width-intval($mmargin) : intval($mmargin)+intval($mwidth))).'px '.(-1*$mbg_pos['y']).'px;}'.
					'#'.$mainmenu_css_type.' .menu li ul li{width:'.$mcwidth.';height:'.$mcheight.';line-height:'.$mcheight.';}#'.$mainmenu_css_type.' .menu li ul li a{width:'.$mcwidth.';height:'.$mcheight.';line-height:'.$mcheight.';'.($mcbg ? 'background-image:url('.$base.$mcbg.');' : '').'}'.
					'#'.$mainmenu_css_type.' .menu li ul .item-child-first,#'.$mainmenu_css_type.' .menu li ul .item-child-end{'.$arrange[$mainmenualign-1].$mcmargin.';'.($mcbg ? 'background-image:url('.$base.$mcbg.');' : '').'}#'.$mainmenu_css_type.' .menu li ul .item-child-first{background-position:'.(-1*$mcbg_pos['x']).'px '.(-1*$mcbg_pos['y']).'px;}'.($mcusebg ? '' : '#'.$mainmenu_css_type.' .menu li ul .item-child-end{background-position:'.($mainmenualign == 1 ? -1*$mcbg_pos['x']-intval($mcwidth)-intval($mcmargin) : -1*$mcbg_pos['x']).'px '.($mainmenualign == 1 ? -1*$mcbg_pos['y'] : -1*$mcbg_pos['y']-intval($mcheight)-intval($mcmargin)).'px;').($mcbreak ? ($mainmenualign == 1 ? 'margin-left:-'.$mcbreakwidth.';' : 'margin-top:-'.$mcbreakwidth.';') : '').'}';
}

array_flip($menuids);
if (count($menuids)) {
	$extend_menus = MulanDBUtil::getObjectlistBySql('select id,link,params from #__menu where id in('.implode(',',$menuids).')');
	$get_extend_menus = array();
	foreach ($extend_menus as $m) :
		$get_extend_menus[$m->id] = $m;
	endforeach;
}

// Note. It is important to remove spaces between elements.
?>
<?php
if ($menutype != 'mainmenu' && $showchildtoptitle) {
	$insert_style .= '.menu-top-title{background:url('.$base.$toptitlebg.') '.(-1*$toptitlebg_pos['x']).'px '.(-1*$toptitlebg_pos['y']).'px}.menu-top-title p a{background:url('.$base.$toptitleprebg.') '.(-1*$toptitleprebg_pos['x']).'px '.(-1*$toptitleprebg_pos['y']).'px}';
?>
<div class="menu-top-title"><p><a></a><?php echo MulanDBUtil::getObjectBySql('select title from #__menu_types where menutype=\''.$menutype.'\' limit 1')->title.'</p>'.($childtitle ? '<span>'.$childtitle.'</span>' : ''); ?><div class="clr"></div></div>
<?php
}
?>
<ul class="menu<?php echo $class_sfx;?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
	<li class="item-first"></li>
<?php
foreach ($list as $i => &$item) :
	/**
	 * 2012-02-03 wengebin Add!
	 * 能够将分类作为菜单显示，作为混合型菜单出现！
	 */
	if (!$showAll && $item->parent_id > 1) continue;
	$childs_menu = '';
	if ($get_extend_menus && count($get_extend_menus) && $showAll) {
		$menuid = $item->params->get('aliasoptions');
		$child_prefix = '';
		if ($showchild2menupre) $child_prefix = $child2menupre;
		if ($item->menutype == 'mainmenu') {
			$child_prefix = '';
			if ($showchildmenupre) $child_prefix = $childmenupre;
		}
		if (!$menuid && $item->params->get('isopen')) $menuid = $item->id;
		if ($item->menutype == 'mainmenu' && $item->parent_id == 1) $menuid = $item->id;
		$extend_menu = $get_extend_menus[$menuid];
		if ($extend_menu->params) {
			$json_obj = json_decode($extend_menu->params);
			$parentcid = $json_obj->parentcid;
			
			if ($json_obj->isopen) {
				$cateid = JRequest::getVar('id');
				$link_params = explode('&',$extend_menu->link);
				$com_params = explode('=',$link_params[0]);
				$view_params = explode('=',$link_params[1]);
				
				$com = $com_params[1];
				$view = $view_params[1];
				$itemid = $extend_menu->id;
				$cat_link = 'index.php?option='.$com.'&view='.$view.'&Itemid='.$itemid.'&id=';
				if ($catch_cats[$parentcid] != null) {
					$parent_cat = $catch_cats[$parentcid];
				} else {
					$catch_cats[$parentcid] = MulanDBUtil::getObjectBySql('select * from #__categories where published=1 and id='.$parentcid);
					$parent_cat = $catch_cats[$parentcid];
				}
				if ($parent_cat->id) {
					$cats = MulanDBUtil::getObjectlistBySql('select * from #__categories where published=1 and extension='.MulanDBUtil::dbQuote($com).' and lft > '.$parent_cat->lft.' and rgt < '.$parent_cat->rgt.' order by lft,id');
					if (count($cats)) {
						$cur_parent_id = $item->id;
						$find_child = 0;
						$find_child_active = 0;
						foreach ($cats as $cat) {
							$parent_level = $parent_cat->level;
							if ($cat->level == $parent_level+1 && $cat->level <= $endLevel && $cat->parent_id == $parentcid) {
								if ($cat->id == $cateid) {
									$find_child_active = $cateid;
								}
								$childs_menu .= $find_child == 0 ? '<ul>'.$mainmenu_child_top_bg_str.($menutype == 'mainmenu' ? '<li class="item-child-first"></li>' : '') : '';
								
								/*-------------- children's children start ---------------*/
								if ($menutype != 'mainmenu') {
									$find_child_2 = 0;
									$find_child_2_active = 0;
									$child_str = '';
									foreach($cats as $cat_child) {
										if ($cat_child->level == $parent_level+2 && $cat_child->level <= $endLevel && $cat_child->parent_id == $cat->id) {
											if ($cat_child->id == $cateid) {
												$find_child_2_active = $cateid;
											}
											$child_str .= $find_child_2 == 0 ? '<div class="child-items-2">' : '';
											$child_str .= '<a class="child-item-2 child-item-2-'.$cat_child->id.($find_child_2_active == $cat_child->id ? ' active-child-2' : '').'" href="'.JRoute::_($cat_link.$cat_child->id).'">'.($showmenuchildtitle ? $child_prefix.$cat_child->title : '').'</a>';
											$find_child_2 ++;
										}
									}
									if ($find_child_2 > 0) {
										$child_str .= '</ul>';
									}
								}
								/*-------------- children's children end ---------------*/
								
								$childs_menu .= '<li id="item-'.$cat->id.'" class="item-'.$cat->id.($find_child_active == $cat->id ? ' current active' : '').'">'.
												'<a href="'.JRoute::_($cat_link.$cat->id).'">'.($showmenuchildtitle ? $child_prefix.$cat->title : '').'</a>'.
												($child_str ? $child_str : '').
												'</li>';
								$find_child ++;
								$catch_chilidren[$cur_parent_id][] = $cat->id;
							}
						}
						if ($find_child > 0) {
							$childs_menu .= ($menutype == 'mainmenu' ? '<li class="item-child-end"></li>' : '').'<div class="clr"></div></ul>';
						}
						$catch_childs_menu[$item->id] = $childs_menu;
						$item->deeper = 200;
						$item->parent = 200;
						$catch_child_nums[$item->id] = $find_child;
						$cur_parent_id = 0;
					}
				}
			}
		}
	}
	
	$id = 'item-'.$item->id;
	$class = 'item-'.$item->id;
	if ($item->id == $active_id) {
		$class .= ' current';
	}

	if ($item->type == 'alias' &&
			in_array($item->params->get('aliasoptions'),$path)
			||	in_array($item->id, $path)) {
		$class .= ' active';
	}

	if ($item->deeper) {
		$class .= ' deeper';
	}

	if ($item->parent) {
		$class .= ' parent';
	}
	
	/**
	 * 2012-03-01 wengebin edit
	 *
	 * 注释原因：防止因为配置了相关文章或者其他内容而导致 parent 的添加，会造成 css 紊乱
	
	if (!$item->parent && $item->params->get('aliasoptions')) {
		$class .= ' parent';
	}
	
	 */

	if (!empty($id)) {
		$id = ' id="'.trim($id).'"';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li'.$id.$class.'>';

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_'.$item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;
	
	if ($catch_childs_menu[$item->id]) {
		echo $catch_childs_menu[$item->id];
	}
	
	// The next item is deeper.     

	if ($item->deeper === true && $showAll) {
		$cur_parent_id = $item->id;
		$catch_child_nums[$cur_parent_id] = 0;
		echo '<ul>'.$mainmenu_child_top_bg_str.(($menutype == 'mainmenu') ? '<li class="item-child-first"></li>' : '');
	}
	// The next item is shallower.
	else if ($item->shallower) {
		$catch_child_nums[$cur_parent_id] += 1;
		$catch_chilidren[$cur_parent_id][] = $item->id;
		$cur_parent_id = 0;
		echo '</li>';
		echo str_repeat(($menutype == 'mainmenu' ? '<li class="item-child-end"></li>' : '').'<div class="clr"></div></ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		$catch_child_nums[$cur_parent_id] += 1;
		$catch_chilidren[$cur_parent_id][] = $item->id;
		echo '</li>';
	}
endforeach;
?>
	<li class="item-end"></li>
</ul>
<?php
$child_left_json = '{';
$child_count = 0;
$child_top = intval($mheight)-($show_mct ? intval($mctopm) : 0);
if (count($catch_child_nums) && $menutype == 'mainmenu') {
	foreach ($catch_child_nums as $key=>$child_num) {
		if ($key != 0) {
			$child_width = $mainmenualign == 1 ? intval($mcwidth)*$child_num + intval($mcmargin)*2 : intval($mcwidth);
			$child_height = $mainmenualign == 1 ? intval($mcheight)+($show_mct ? intval($mctopheight) : 0) : intval($mcheight)*$child_num + intval($mcmargin)*2+($show_mct ? intval($mctopheight) : 0);
			$child_left = ($child_width-intval($mwidth)-($mcbreak && $mainmenualign == 1 ? intval($mcbreakwidth) : 0))/2;
			$insert_style .= '#'.$mainmenu_css_type.' .menu .item-'.$key.' ul #mc-top{width:'.$child_width.'px;}#'.$mainmenu_css_type.' .menu .item-'.$key.' ul{'.$arrange[0].$child_width.'px;'.$arrange[1].$child_height.'px;left:'.(-1*$child_left).'px;top:'.$child_top.'px;}';
			if ($mcusebg) $insert_style .= '#'.$mainmenu_css_type.' .menu li.item-'.$key.' ul .item-child-end{background-position:'.($mainmenualign == 1 ? -1*$mcbg_pos['x']-$child_width+intval($mcmargin) : -1*$mcbg_pos['x']).' '.($mainmenualign == 1 ? -1*$mcbg_pos['y'] : -1*$mcbg_pos['y']-$child_height+intval($mcmargin)+($show_mct ? intval($mctopheight) : 0)).';';
			$child_left_json .= $key.':'.(-1*$child_left).($child_count < count($catch_child_nums)-1 ? ',' : '');
		}
		$child_count ++;
	}
}
$child_left_json .= '}';
if (count($catch_chilidren) && $menutype == 'mainmenu' && $mcbg && $mcusebg) {
	foreach ($catch_chilidren as $key=>$child) {
		if ($key != 0) {
			foreach ($child as $key_c=>$c) {
				$pos_1 = $mainmenualign == 1 ? intval($mcwidth)*$key_c + intval($mcmargin) : intval($mcheight)*$key_c + intval($mcmargin);
				$pos_2 = $mainmenualign == 1 ? intval($mcheight) : intval($mcwidth);
				$pos_val_normal = array((-1*$pos_1-$mcbg_pos['x']).'px '.(-1*$mcbg_pos['y']).'px',(-1*$mcbg_pos['x']).'px '.(-1*$pos_1-$mcbg_pos['y']).'px');
				$pos_val_hover = array((-1*$pos_1-$mcbg_pos['x']).'px '.(-1*$pos_2-$mcbg_pos['y']).'px',(-1*$pos_2-$mcbg_pos['x']).'px '.(-1*$pos_1-$mcbg_pos['y']).'px');
				$insert_style .= '#'.$mainmenu_css_type.' .menu li#item-'.$key.' ul .item-'.$c.' a{background-position:'.$pos_val_normal[$mainmenualign-1].';}#'.$mainmenu_css_type.' .menu li#item-'.$key.' ul .item-'.$c.' a:hover,#'.$mainmenu_css_type.' .menu li#item-'.$key.' ul li#item-'.$c.'.active a{background-position:'.$pos_val_hover[$mainmenualign-1].';}';
			}
		}
	}
}

if ($mainmenuhide) $insert_style .= 'body #'.$mainmenu_css_type.' .menu li ul{display:none;opacity:0;filter:alpha(opacity=0);}';
if ($menutype == 'mainmenu' && $mainmenuopen) {
	$offset_pos = array('left','left','top','top');
	$script = '$(document).ready(function(){
		var anim_time = '.($mainmenufade ? $manimtime : '0').';
		var child_top = '.$child_top.';
		var child_left = eval("('.$child_left_json.')");';
	if ($mainmenuoffset > 0) {
		$script .= '
		$("#'.$mainmenu_css_type.' .menu li.deeper").each(function(){';
		if ($mainmenuoffset == 1 || $mainmenuoffset == 2) $script .= '
			var ele_class = $(this).attr("class");
			var item_pos = ele_class.indexOf("item-");
			var child_id = ele_class.substring(item_pos+5,ele_class.indexOf(" ",item_pos));';
		$script .= '
			$(this).find("ul").css({'.$offset_pos[$mainmenuoffset-1].':('.($mainmenuoffset == 1 || $mainmenuoffset == 2 ? 'child_left[child_id]' : 'child_top').($mainmenuoffset == 1 || $mainmenuoffset == 3 ? '-' : '+').'10)+"px"});
		});';
	}
	$script .= '
		var cur_child_id = 0;
		$("#'.$mainmenu_css_type.' .menu li.deeper").hover(function(){';
	if ($mainmenuoffset == 1 || $mainmenuoffset == 2) $script .= '
			var ele_class = $(this).attr("class");
			var item_pos = ele_class.indexOf("item-");
			cur_child_id = parseInt(ele_class.substring(item_pos+5,ele_class.indexOf(" ",item_pos)));';
	$script .= '
			$(this).find("ul").stop().css({opacity:0,display:"block"}).animate({opacity:1'.($mainmenuoffset > 0 ? ','.$offset_pos[$mainmenuoffset-1].':'.($mainmenuoffset == 1 || $mainmenuoffset == 2 ? 'child_left[cur_child_id]' : 'child_top').'+"px"' : '').'},anim_time);
		},function(){
			$(this).find("ul").stop().animate({opacity:0'.($mainmenuoffset > 0 ? ','.$offset_pos[$mainmenuoffset-1].':('.($mainmenuoffset == 1 || $mainmenuoffset == 2 ? 'child_left[cur_child_id]' : 'child_top').($mainmenuoffset == 1 || $mainmenuoffset == 3 ? '-' : '+').'10)+"px"' : '').'},anim_time,function(){$(this).css({display:"none"});});
		});
	});';
	echo '<script type="text/javascript">'.$script.'</script>';
}

$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);
?>