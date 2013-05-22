<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Selectpro component helper.
 */
abstract class SelectproHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_SELECTPRO_SUBMENU_MESSAGES'), 'index.php?option=com_selectpro', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_SELECTPRO_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_selectpro', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-selectpro {background-image: url(../media/com_selectpro/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_SELECTPRO_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_selectpro';
		}
		else {
			$assetName = 'com_selectpro.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.expexcel'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result;
	}
}

