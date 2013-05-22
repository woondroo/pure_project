<?php

// no direct access
defined('_JEXEC') or die('Restricted Access');

// import joomla model list library
jimport('joomla.application.component.modeladmin');

class Qlue404ModelCustom extends JModelAdmin {
		
	protected $text_prefix = 'COM_QLUE404';
			
	public function getTable($type = 'Custom', $prefix = 'Qlue404Table', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {
    	// Get the form.
        $form = $this->loadForm('com_qlue404.custom', 'custom', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
        	return false;
        }
        return $form;
    }
    
    protected function loadFormData() {
    	// Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_qlue404.edit.custom.data', array());
        if (empty($data)) {
        	$data = $this->getItem();
        }
        return $data;
    }
	
}

?>