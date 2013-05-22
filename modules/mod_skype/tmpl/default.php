<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_skype
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
?>
<div id="left-online">
	<?php
	$document = JFactory::getDocument();
	$base = JURI::base();
	if ($params->get('skypelink')) {
		if ($params->get('skypescript')) {
			$document->addScript(strpos($params->get('skypescript'),'ttp://') ? $params->get('skypescript') : JURI::base().$params->get('skypescript'));
			//echo '<script src="'.(strpos($params->get('skypescript'),'ttp://') ? $params->get('skypescript') : JURI::base().$params->get('skypescript')).'" type="text/javascript"></script>';
		}
		echo '<a target="_blank" class="skype-bt" href="'.$params->get('skypelink').'"></a>';
	}
	if ($params->get('msnlink')) {
		echo '<a class="msn-bt" href="'.$params->get('msnlink').'"></a>';
		if ($params->get('msnscript1')) {
			$document->addScript(strpos($params->get('msnscript1'),'ttp://') ? $params->get('msnscript1') : JURI::base().$params->get('msnscript1'));
			//echo '<script src="'.(strpos($params->get('msnscript1'),'ttp://') ? $params->get('msnscript1') : JURI::base().$params->get('msnscript1')).'" type="text/javascript"></script>';
		}
		if ($params->get('msnscript2')) {
			$document->addScript(strpos($params->get('msnscript2'),'ttp://') ? $params->get('msnscript2') : JURI::base().$params->get('msnscript2'));
			//echo '<script src="'.(strpos($params->get('msnscript2'),'ttp://') ? $params->get('msnscript2') : JURI::base().$params->get('msnscript2')).'" type="text/javascript"></script>';
		}
	}
	?>
</div>