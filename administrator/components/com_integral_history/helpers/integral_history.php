<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Integral_history component helper.
 */
abstract class Integral_historyHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_INTEGRAL_HISTORY_SUBMENU_MESSAGES'), 'index.php?option=com_integral_history', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_INTEGRAL_HISTORY_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_integral_history', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-integral_history {background-image: url(../media/com_integral_history/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_INTEGRAL_HISTORY_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_integral_history';
		}
		else {
			$assetName = 'com_integral_history.message.'.(int) $messageId;
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

