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
class SitelinkxViewSitelinkx extends JView
{
	/**
	 * display method of Sitelinkx view
	 * @return void
	 **/
	function display($tpl = null){
		$user  = JFactory::getUser();
		
		//get the data
		JHTML::stylesheet( 'sitelinkx.css', 'administrator/components/com_sitelinkx/' );
		$linkwords =& $this->get('Data');
		$isNew = ($linkwords->id == null);

		$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
		JToolBarHelper::title(   JText::_( 'SL_NAME' ).': <small><small>[ ' . $text.' ]</small></small>' );
		
		if ($isNew)  {
			if($user->authorise('core.create', 'com_sitelinkx')) JToolBarHelper::save();
			JToolBarHelper::cancel();
		} else {
			if($user->authorise('core.edit', 'com_sitelinkx')) JToolBarHelper::save();
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('sitelinkx', $linkwords);
		
		parent::display($tpl);
	}
}