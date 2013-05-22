<?php
/**
 * Sitelinkx Controller for Sitelinkx Component
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

/**
 * Sitelinkx Model
 *
 * @package    Joomla.Components
 * @subpackage 	Sitelinkx
 */
class SitelinkxControllerSitelinkx extends SitelinkxController{


	/**
	 * Parameters in config.xml.
	 *
	 * @var	object
	 * @access	protected
	 */
	private $_params = null;

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct(){
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		
		// Set reference to parameters
		$this->_params = &JComponentHelper::getParams( 'com_sitelinkx' );
		//$dummy = $this->_params->get('parm_text');

	}

	/**
	 * display the edit form
	 * @return void
	 */
	public function edit(){
		JRequest::setVar( 'view', 'sitelinkx' );
		JRequest::setVar( 'layout', 'default'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save(){
		$model = $this->getModel('sitelinkx'); 

		if ($model->store()) {
			$msg = JText::_( 'SL_SAVED' );
		} else {
			$msg = JText::_( 'SL_ERROR_SAVE' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_sitelinkx&controller=sitelinkxlist';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	public function remove(){
		$model = $this->getModel('sitelinkx'); //
		if(!$model->delete()) {
			$msg = JText::_( 'SL_ERROR_DEL' );
		} else {
			$msg = JText::_( 'SL_DEL' );
		}

		$this->setRedirect( 'index.php?option=com_sitelinkx&controller=sitelinkxlist', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel(){
		$msg = JText::_( 'SL_CANCEL' );
		$this->setRedirect( 'index.php?option=com_sitelinkx&controller=sitelinkxlist', $msg );
	}
	
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db 	=& JFactory::getDBO();
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'SELECT_SL_PUBLISH' ) );
		}

		$cids = implode( ',', $cid );
    $blibla = "1";

		$query = 'UPDATE #__sitelinkx SET published = '. $blibla .' WHERE id IN ( '. $cids .' )';
		$db->setQuery( $query );
    $result = $db->loadResult();		
		$msg = JText::_( 'SL_PUBLISHED' );

		$this->setRedirect( 'index.php?option=com_sitelinkx&controller=sitelinkxlist', $msg );
	}


	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db 	=& JFactory::getDBO();
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'SELECT_SL_UNPUBLISH' ) );
		}

		$cids = implode( ',', $cid );
    $blibla = "0";

		$query = 'UPDATE #__sitelinkx SET published = '. $blibla .' WHERE id IN ( '. $cids .' )';
		$db->setQuery( $query );
    $result = $db->loadResult();		
		$msg = JText::_( 'SL_UNPUBLISHED' );

		$this->setRedirect( 'index.php?option=com_sitelinkx&controller=sitelinkxlist', $msg );
	}	
}