<?php
/**
 * @version		$Id: mod_homelist.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_homelist
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the homelist functions only once
require_once dirname(__FILE__).'/helper.php';

$items = modHomelistHelper::getItems($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_homelist',$params->get('layout', 'default'));
