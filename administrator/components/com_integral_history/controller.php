
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of Integral_history component
 */
class Integral_historyController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'Integral_historys'));
 
		// call parent behavior
		parent::display($cachable);
 
		// Set the submenu
		Integral_historyHelper::addSubmenu('messages');
	}
}