<?php
/**
 * @version		$Id: new.php 2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2008 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';

$controller	= JController::getInstance('New');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
