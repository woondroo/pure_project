<?php 

// no direct access
defined('_JEXEC') or die('Restircted Access');

// Import joomla controller library
jimport('joomla.application.component.controller');

// Check if the error object is available
if( isset($error) && is_object($error)){
	define('QLUE_ERROR_CODE', $error->get('code'));
	define('QLUE_ERROR_MESSAGE', $error->get('message'));
}

/*
 * Note: JController::getInstance is a static function. It will only load one controller per page request.
 * This is a problem on error pages when a controller is already loaded, My component will not function correctly.
 * I will create my controller as in Joomla 1.5
 */
 
//$controller	= JController::getInstance('Qlue404', array('base_path' => JPATH_ROOT.DS.'components'.DS.'com_qlue404'));

// Lets load our controller file
require_once JPATH_ROOT.DS.'components'.DS.'com_qlue404'.DS.'controller.php';

// Create our new controller class
$controller = new Qlue404Controller(array('base_path' => JPATH_ROOT.DS.'components'.DS.'com_qlue404'));
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>