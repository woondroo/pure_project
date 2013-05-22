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
class TableSitelinkx extends JTable{
	/** jcb code */
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	/**
	 *
	 * @var string
	 */
	var $wort = null;
	var $ersatz = null;
	var $schlagwort = null;
	var $fenster = null;
	var $published = null;
	var $begpub = null;
	var $endpub = null;
	var $anzahl = null;
	var $suchm = null;
	/** jcb code */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableSitelinkx(& $db){
		parent::__construct('#__sitelinkx', 'id', $db);
	}
	
	function check(){
		// write here data validation code
		return parent::check();
	}
}