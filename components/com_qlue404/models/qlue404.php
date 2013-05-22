<?php

//no direct access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');
jimport('joomla.filesystem.file');
jimport('joomla.base.object');

class Qlue404ModelQlue404 extends JModelItem {
			
	protected $_context = 'com_qlue404.qlue404';
	
	public function populateState() {
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		
		if(defined('QLUE_ERROR_CODE')) {
			$this->setState('qlue404.error.code', QLUE_ERROR_CODE);
		}
		
		if(defined('QLUE_ERROR_MESSAGE')) {
			$this->setState('qlue404.error.message', QLUE_ERROR_MESSAGE);
		}
		
		// Load the object state.
		$id	= JRequest::getInt('id', false);

		// Load the parameters.
		$this->setState('params', $params);
		$this->setState('filter.published', 1);
	}
	
	public function &getItem($id = null) {
			
		if ($this->_item === null) {
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('qlue404.id');
			}
			
			// Get a level row instance.
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_qlue404'.DS.'tables');
			$table = JTable::getInstance('Custom', 'Qlue404Table');
			
			// If and id has been set and is for my component load it
			if($id && (JRequest::getCmd('option') == 'com_qlue404')) {
				$item = array('id' => $id);
			} else {
				$item = array('error' => $this->getState('qlue404.error.code'), 'operator' => 'like');
			}
			
			// Attempt to load the row.
			if ($table->load($item)) {
				
				// Check published state.
				if ($published = $this->getState('filter.published')) {
					if ($table->published != $published) {
						$this->_item = $this->_createError();
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$table->text = $table->description;
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
				
			} else if(JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_qlue404'.DS.'error'.DS.'error.php')) {
					
				$this->_item = $this->_createError();	
				
			} else if ($error = $table->getError()) {
				$this->setError($error);
			}
			
		}

		return $this->_item;
	}
	
	protected function _createError() {
			
		static $item;
		
		// If we already created our item return it
		if(isset($item)) {
			return $item;
		}
		
		// create error object
		$error = new JObject();
		
		// set the error values for our error file
		$error->set('code', $this->getState('qlue404.error.code'));
		$error->set('message', $this->getState('qlue404.error.message'));
		
		// Get a default table row
		$table = JTable::getInstance('Custom', 'Qlue404Table');
		
		// Create a new item from our error.php file
		$table->title = ($this->getState('qlue404.error.code')) ? $this->getState('qlue404.error.code').' '.$this->getState('qlue404.error.message') : JText::_('PAGE_NOT_FOUND');
		
		// Get output of our error file
		ob_start();
		// Lets load our contents of our error page
		require_once JPATH_ROOT.DS.'components'.DS.'com_qlue404'.DS.'error'.DS.'error.php';
		// Set the contents of our error file
		$table->text = ob_get_contents();
		// Close our output
		ob_end_clean();
		
		// Create our item
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');
		
		return $item;		
	}
}

?>