<?php
/**
 * @package    Sitelinkx
 * @subpackage com_sitelinkx
 * @license  !license!
 *
 * Created with Marco's Component Creator for Joomla! 1.6
 * http://www.mmleoni.net/joomla-component-builder
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sitelinkx')){
	return JError::raiseWarning(404, JText::_( 'JERROR_ALERTNOAUTHOR' ));
}

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

$controllers = explode(',', 'sitelinkxlist,sitelinkxconfig,slabout');
if(!JRequest::getWord('controller')){
	JRequest::setVar( 'controller', $controllers[0] );
}
//foreach($controllers as $controller){
	$link = JRoute::_("index.php?option=com_sitelinkx&controller={$controllers[0]}");
	$selected = ($controllers[0] == JRequest::getWord('controller'));
	JSubMenuHelper::addEntry(JText::_(  'Sitelinkx Manager' ), "index.php?option=com_sitelinkx&controller={$controllers[0]}", ($controllers[0] == JRequest::getWord('controller')));

	$link = JRoute::_("index.php?option=com_sitelinkx&controller={$controllers[1]}");
	$selected = ($controllers[1] == JRequest::getWord('controller'));
	JSubMenuHelper::addEntry(JText::_(  'Sitelinkx Config' ), "index.php?option=com_sitelinkx&controller={$controllers[1]}", ($controllers[1] == JRequest::getWord('controller')));

//	$link = JRoute::_("index.php?option=com_sitelinkx&controller={$controllers[2]}");
//	$selected = ($controllers[2] == JRequest::getWord('controller'));
//	JSubMenuHelper::addEntry(JText::_(  'About Sitelinkx' ), "index.php?option=com_sitelinkx&controller={$controllers[2]}", ($controllers[2] == JRequest::getWord('controller')));
//}
JRequest::setVar( 'view', JRequest::getWord('controller') );


// Require specific controller if requested; allways, in standard execution
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'SitelinkxController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getCmd( 'task' ) );

// Redirect if set by the controller
$controller->redirect();