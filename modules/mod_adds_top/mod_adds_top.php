<?php
/**
 * @version		$Id: mod_footer.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__).'/helper.php';
$showItem  = modAddsTopHelper::hasShow();
require JModuleHelper::getLayoutPath('mod_adds_top', $params->get('layout', 'default'));
?>