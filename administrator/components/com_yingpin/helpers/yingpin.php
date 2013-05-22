<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Yingpin component helper.
 */
abstract class YingpinHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_YINGPIN_SUBMENU_MESSAGES'), 'index.php?option=com_yingpin', $submenu == 'messages');
		//JSubMenuHelper::addEntry(JText::_('COM_YINGPIN_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_yingpin', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-yingpin {background-image: url(../media/com_yingpin/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_YINGPIN_ADMINISTRATION_CATEGORIES'));
		}
	}
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($messageId)) {
			$assetName = 'com_yingpin';
		}
		else {
			$assetName = 'com_yingpin.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.expexcel'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authoriseInCustom($action, $assetName));
		}
 
		return $result;
	}
}

