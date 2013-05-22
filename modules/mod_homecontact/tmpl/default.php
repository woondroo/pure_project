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

$home_contact_alias = $params->get('home_contact_alias');
$home_contact_bg = $params->get('home_contact_bg');
$home_contact_tel = $params->get('home_contact_tel');
$home_contact_add = $params->get('home_contact_add');
$home_contact_qq = $params->get('home_contact_qq');
$home_contact_width = $params->get('home_contact_width');
$home_contact_height = $params->get('home_contact_height');

if ($home_contact_bg) $insert_style = '.home-contact-'.$home_contact_alias.'{width:'.$home_contact_width.';height:'.$home_contact_height.';background:url('.$base.$home_contact_bg.') center center no-repeat;}';

$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);
?>
<div class="home-contact-<?php echo $home_contact_alias; ?>">
	<div class="home-contact-tel"><?php echo $home_contact_tel; ?></div>
	<div class="home-contact-add"><?php echo $home_contact_add; ?></div>
	<a class="home-contact-qq" target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $home_contact_qq; ?>&site=qq&menu=yes"></a>
</div>