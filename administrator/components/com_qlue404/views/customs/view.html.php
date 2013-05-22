<?php

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import joomla view library
jimport('joomla.application.component.view');

class Qlue404ViewCustoms extends JView {
		
	function display($tpl = null) {
		
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		$state = $this->get('State');
		
		if(count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->items = $items;
		$this->pagination = $pagination;
		$this->state = $state;
		
		// Create our toolbar
		$this->addToolbar();
		
		// Display layout file
		parent::display($tpl);
	}
	
	function addToolbar() {
				
		// Set the title
		JToolBarHelper::title(JText::_('COM_QLUE404_TITLE'), 'generic.png');
		
		// Get ACL actions
		$canDo = Qlue404Helper::getActions();
		
		if($canDo->get('core.create')) {
			JToolBarHelper::addNew('custom.add');
		}
		
		if($canDo->get('core.edit')) {
			JToolBarHelper::editList('custom.edit');
		}
        
        if($canDo->get('core.delete')) {
        	JToolBarHelper::deleteList('', 'customs.delete');
        }
        
        if($canDo->get('core.edit.state')) {
        	JToolBarHelper::custom('customs.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('customs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true); 
		} 		
	}
}

?>