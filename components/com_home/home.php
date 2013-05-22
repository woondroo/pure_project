<?php
/**
 * @version		$Id: home.php 2011-11-21 08:38:16
 * @package		Joomla.Site
 * @subpackage	com_home
 * @copyright	Copyright (C) 2008 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Home');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
