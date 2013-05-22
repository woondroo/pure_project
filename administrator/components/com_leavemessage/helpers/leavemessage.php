<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Leavemessage component helper.
 */
abstract class LeavemessageHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_LEAVEMESSAGE_SUBMENU_MESSAGES'), 'index.php?option=com_leavemessage', $submenu == 'messages');
		//JSubMenuHelper::addEntry(JText::_('COM_LEAVEMESSAGE_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_leavemessage', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-leavemessage {background-image: url(../media/com_leavemessage/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_LEAVEMESSAGE_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_leavemessage';
		}
		else {
			$assetName = 'com_leavemessage.message.'.(int) $messageId;
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

