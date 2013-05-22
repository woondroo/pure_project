<?php
/**
 * Sitelinkx Model for Sitelinkx Component
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
 * Sitelinkx Table
 *
 * @package    Joomla.Components
 * @subpackage 	Sitelinkx
 */
class TableSitelinkxconfig extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	/**
	 *
	 * @var float
	 */
	var $version = null;
	var $anzahl = null;
	var $suchm = null;
	var $erreichb = null;
	var $fenster = null;
	var $hinweis = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableSitelinkxconfig(& $db){
		parent::__construct('#__sitelinkx_config', 'id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}