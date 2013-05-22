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
class SitelinkxControllerSitelinkxconfig extends SitelinkxController{


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
		$this->registerTask( 'edit'  , 	'' );
		
		// Set reference to parameters
		$this->_params = &JComponentHelper::getParams( 'com_sitelinkx' );
		//$dummy = $this->_params->get('parm_text');

	}


	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save(){
		$model = $this->getModel('sitelinkxconfig'); 

		if ($model->store()) {
			$msg = JText::_( 'SL_CONFIG_SAVED' );
		} else {
			$msg = JText::_( 'SL_ERROR_CFG' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_sitelinkx';
		$this->setRedirect($link, $msg);
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel(){
		$msg = JText::_( 'SL_CANCEL' );
		$this->setRedirect( 'index.php?option=com_sitelinkx', $msg );
	}
}