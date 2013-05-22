
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Products View
 */
class ProductViewProducts extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Products view display method
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
			JError::raiseError(500, implode('<br />', $errors));
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
		$canDo = ProductHelper::getActions();
		JToolBarHelper::title(JText::_('COM_PRODUCT_MANAGER_PRODUCTS'), 'product');
		
		if ($canDo->get('core.expexcel')) {
			JToolBarHelper::exportList('', 'products.expexcel', 'JTOOLBAR_EXPORTEXCEL');
		}
		
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('product.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('products.topthis','topthis','','JTOOLBAR_TOPTHIS');
			JToolBarHelper::divider();
			JToolBarHelper::editList('product.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
			JToolBarHelper::publish('product.published', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('product.unpublished', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
		}
		
		if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_product');
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
		$document->setTitle(JText::_('COM_PRODUCT_ADMINISTRATION'));
	}
}
