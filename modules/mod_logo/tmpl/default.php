<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_logo
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

$left = $params->get('left', '0px');
$right = $params->get('right', 0);
$top = $params->get('top', '0px');
$bottom = $params->get('bottom', 0);

$width = $params->get('width', '240px');
$height = $params->get('height', '50px');

$text = $params->get('showtext') ? $params->get('text') : '';

$logo_area_style = array();
array_push($logo_area_style, 'width:'.$width);
array_push($logo_area_style, 'height:'.$height);
array_push($logo_area_style, $right ? 'right:'.$right : 'left:'.$left);
array_push($logo_area_style, $bottom ? 'bottom:'.$bottom : 'top:'.$top);

$logo_style = array();
if ($params->get('logoimg')) array_push($logo_style, 'background-image:url('.JURI::base().$params->get('logoimg').')');
array_push($logo_style, 'line-height:'.$height);

$document = JFactory::getDocument();
$insert_style = '#header #logo{'.implode(';',$logo_area_style).'}#header #logo a{'.implode(';',$logo_style).'}';
$document->addStyleDeclaration($insert_style);

echo '<h1 id="logo">';
echo '<a href="'.JURI::base(true).'">'.$text.'</a>';
echo '</h1>';
?>