<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Liveshow component helper.
 */
abstract class LiveshowHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_LIVESHOW_SUBMENU_MESSAGES'), 'index.php?option=com_liveshow', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_LIVESHOW_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_liveshow', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-liveshow {background-image: url(../media/com_liveshow/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_LIVESHOW_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_liveshow';
		}
		else {
			$assetName = 'com_liveshow.message.'.(int) $messageId;
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

