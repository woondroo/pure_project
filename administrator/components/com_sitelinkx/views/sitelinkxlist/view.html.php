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
 * Sitelinkx View
 *
 * @package    Joomla.Components
 * @subpackage 	Sitelinkx
 */
class SitelinkxViewSitelinkxlist extends JView
{
	/**
	 * Sitelinkxlist view display method
	 * @return void
	 **/
	function display($tpl = null){
		$app =& JFactory::getApplication();
		$user  = JFactory::getUser();

		// Get data from the model
		$rows = & $this->get( 'Data');
		
		// draw menu
    JHTML::_('behavior.tooltip');	
		JHTML::stylesheet( 'sitelinkx.css', 'administrator/components/com_sitelinkx/' );
		JToolBarHelper::title(   JText::_( 'SL_MAN' ), 'sitelinkx' );
		if($user->authorise('core.delete', 'com_sitelinkx')) JToolBarHelper::deleteList();
		if($user->authorise('core.edit', 'com_sitelinkx')) JToolBarHelper::editListX();
		if($user->authorise('core.create', 'com_sitelinkx')) JToolBarHelper::addNewX();

		
		// configuration editor for config.xml
		if($user->authorise('core.admin', 'com_sitelinkx')){
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_sitelinkx');
		}
		

		$this->assignRef('rows', $rows );
		$pagination =& $this->get('Pagination');
		$this->assignRef('pagination', $pagination);

		// SORTING get the user state of order and direction
		$default_order_field = 'id';
		$lists['order_Dir'] = $app->getUserStateFromRequest('com_sitelinkxfilter_order_Dir', 'filter_order_Dir', 'ASC');
		$lists['order'] = $app->getUserStateFromRequest('com_sitelinkxfilter_order', 'filter_order', $default_order_field);
		$lists['search'] = $app->getUserStateFromRequest('com_sitelinkxsearch', 'search', '');
		$this->assignRef('lists', $lists);


		parent::display($tpl);
	}
}