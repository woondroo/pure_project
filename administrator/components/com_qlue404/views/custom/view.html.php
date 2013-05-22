<?php

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import joomla view library
jimport('joomla.application.component.view');

class Qlue404ViewCustom extends JView {
		
	function display($tpl = null) {
			
		// Get our data
		$form = $this->get('Form');
		$item = $this->get('Item');
		
		// Check for errors
		if(count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Assign data to layout
		$this->form = $form;
		$this->item = $item;
		
		// Add our toolbar
		$this->addToolbar();
		
		// Display layout file
		parent::display($tpl);
		
		// Set the document
        $this->setDocument();
	}
	
	function addToolbar() {
					
		// Hide the main menu
		JRequest::setVar('hidemainmenu', true);	
		
		// Determine which actions could be taken
		$canDo = Qlue404Helper::getActions();	
		
		// Check if our item is new
		$isNew = ($this->item->id == 0);
			
		// Set the title
		JToolBarHelper::title($isNew ? JText::_('COM_QLUE404_CREATING') : JText::_('COM_QLUE404_EDITING'), 'generic.png');
		
		if($isNew) {
				
			// If user can create then show buttons
			if($canDo->get('core.create')) {
				JToolBarHelper::save('custom.save');
				JToolBarHelper::apply('custom.apply');
			}			
		} else {
		
			// If user can edit then show buttons
			if($canDo->get('core.edit')) {
				JToolBarHelper::save('custom.save');
				JToolBarHelper::apply('custom.apply');
			}
		}
		
		JToolBarHelper::cancel('custom.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
	
	function setDocument() {
		$isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_QLUE404_CREATING') : JText::_('COM_QLUE404_EDITING'));
        $document->addScript(JURI::root() ."/administrator/components/com_qlue404/views/custom/submitbutton.js");
        JText::script('COM_QLUE404_ERROR_UNACCEPTABLE');     
	}
}

?>