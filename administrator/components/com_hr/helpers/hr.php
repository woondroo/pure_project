<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Hr component helper.
 */
abstract class HrHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_HR_SUBMENU_MESSAGES'), 'index.php?option=com_hr', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_HR_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_hr', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-hr {background-image: url(../media/com_hr/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_HR_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_hr';
		}
		else {
			$assetName = 'com_hr.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.expexcel'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authoriseInCustom($action, $assetName));
		}
//var_dump($result);
		return $result;
	}
}

