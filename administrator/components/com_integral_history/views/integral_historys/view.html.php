
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Integral_historys View
 */
class Integral_historyViewIntegral_historys extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Integral_historys view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Check for errors.
		if (count($errors =$this->get('Errors'))) 
		{
			JError::raiseError(500, implode('\n', $errors));
			return false;
		}
		
		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}
 
		// Set the toolbar
		$this->addToolBar();
		// Display the template
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = Integral_historyHelper::getActions();
		JToolBarHelper::title(JText::_('COM_INTEGRAL_HISTORY_MANAGER_INTEGRAL_HISTORYS'), 'integral_history');
		if (('core.expexcel')) {
			JToolBarHelper::exportList('', 'integral_historys.expexcel', 'JTOOLBAR_EXPORTEXCEL');
		}

		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'integral_historys.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin') && false) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_integral_history');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_INTEGRAL_HISTORY_ADMINISTRATION'));
	}
}
