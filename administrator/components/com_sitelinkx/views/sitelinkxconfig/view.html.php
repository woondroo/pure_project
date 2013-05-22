<?php
/**
 * Sitelinkx View for Sitelinkx Component
 * 
 * @package    Sitelinkx
 * @subpackage com_sitelinkx
 * @license  GNU/GPL v2
 *
 * Created with Marco's Component Creator for Joomla! 1.6
 * http://www.mmleoni.net/joomla-component-builder
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Sitelinkx view
 *
 * @package    Joomla.Components
 * @subpackage 	Sitelinkx
 */
class SitelinkxViewSitelinkxconfig extends JView
{
	/**
	 * display method of Sitelinkx view
	 * @return void
	 **/
	function display($tpl = null){
		$user  = JFactory::getUser();
		
		//get the data
		$konfig =& $this->get('Data');
		JHTML::stylesheet( 'sitelinkx.css', 'administrator/components/com_sitelinkx/' );
		JToolBarHelper::title(   JText::_( 'SL_CONFIG' ),'sitelinkx' );
		if($user->authorise('core.edit', 'com_sitelinkx')) JToolBarHelper::save();
		JToolBarHelper::cancel();
		$this->assignRef('sitelinkxconfig', $konfig);
		parent::display($tpl);
	}
}