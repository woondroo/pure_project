<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Sitelinkx
 * @copyright Copyright (C) www.extro-media.de
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class SitelinkxViewSlabout extends JView
{
	function display($tpl = null) {
		$app =& JFactory::getApplication();
		JHTML::stylesheet( 'sitelinkx.css', 'administrator/components/com_sitelinkx/' );
		JToolBarHelper::title(   JText::_( 'SL_ABOUT' ), 'sitelinkx' );
		
		parent::display($tpl);
	}
}
?>
