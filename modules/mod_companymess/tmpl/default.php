<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_companymess
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
?>
<div id="footer-company-mess">
	<div class="footer-company-contact">
		<?php
		echo str_replace('[','<', str_replace(']','>',$params->get('contact')));
		?>
	</div>
	<div class="footer-company-address">
		<?php
		echo str_replace('[','<', str_replace(']','>',$params->get('address')));
		?>
	</div>
</div>