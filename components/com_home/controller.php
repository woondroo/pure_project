<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * Home Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_home
 * @since 1.5
 */
class HomeController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{

		// Initialise variables.
		$cachable	= false;	// Huh? Why not just put that in the constructor?
		$safeurlparams = array();
		return parent::display($cachable,$safeurlparams);
	}
}
