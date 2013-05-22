<?php
/**
 * @version		$Id: default_component.php 21322 2011-05-11 01:10:29Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="'.$item->anchor_css.'" ' : '';
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
	$item->params->get('menu_text', 1 ) ?
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
} else {
	/**
	 * 2012-02-04 wengebin edit!
	 * 如果想让主导航的文字不显示，只需在“全局设置”里面设置即可！
	 */
	if ((intval($item->level) == 1 && $item->menutype == 'mainmenu' && $showmenutitle) || (intval($item->level) > 1 && $item->menutype == 'mainmenu' && $showmenuchildtitle) || $item->menutype != 'mainmenu') {
		$prefix = '';
		if ($item->menutype == 'mainmenu' && $item->level == '1') {
			if ($showmenupre) $prefix = $menupre;
		} else if ($item->menutype == 'mainmenu' && $item->level == '2') {
			if ($showchildmenupre) $prefix = $childmenupre;
		} else if ($showchildmenupre && $item->level == '1') {
			$prefix = $childmenupre;
		}
		
		if ($item->menutype != 'mainmenu' && intval($item->level) > 1) {
			if ($showchild2menupre) {
				$prefix = $child2menupre;
			}
		}
		
		$linktype = $prefix.$item->title;
	} else {
		$linktype = '';
	}
}

switch ($item->browserNav) :
	default:
	case 0:
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
	// window.open
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a>
<?php
		break;
endswitch;
