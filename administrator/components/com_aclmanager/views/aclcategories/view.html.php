<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * Aclmanagers View
 */
class AclmanagerViewAclcategories extends JView
{
	/**
	 * Aclmanagers view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$items = $this->get('Items');
		$this->state = $this->get('State');
		$rebuiltItems = array();
		if($items){
			foreach ($items as &$item) {
				if ($item->catid == 0) {
					$rebuiltItems[$item->id] = $item;
					if (!isset($rebuiltItems[$item->id]->submenu))
					{
						$rebuiltItems[$item->id]->submenu = array();
					}
				} 
			}
			foreach ($items as &$item) {
				if($item->catid != 0) {
					// Sub level.
					if (isset($rebuiltItems[$item->catid])) {
						if (isset($rebuiltItems[$item->catid]->submenu)) {
							$rebuiltItems[$item->catid]->submenu[] = &$item;
						}
					}
				}
			}
		}
		$items = $rebuiltItems ;
		$pagination = $this->get('Pagination');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
 
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
		$canDo = AclmanagerHelper::getActions();
		JToolBarHelper::title('权限类别管理', 'aclmanager');
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('aclcategory.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('aclcategory.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'aclcategories.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) 
		{
			
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_aclmanager');
			JToolBarHelper::divider();
			JToolBarHelper::publish('aclcategory.published', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::unpublish('aclcategory.unpublished', 'JTOOLBAR_UNPUBLISH', true);
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
		$document->setTitle(JText::_('权限类别管理组件'));
	}
}
