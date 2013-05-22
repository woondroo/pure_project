<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Comcreater component helper.
 */
abstract class ComcreaterHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		//JSubMenuHelper::addEntry(JText::_(''), 'index.php?option=com_comcreater', $submenu == 'messages');
		//JSubMenuHelper::addEntry(JText::_('权限分类'), 'index.php?option=com_categories&view=categories&extension=com_comcreater', $submenu == 'categories');
		// set some global property
		//$document = JFactory::getDocument();
	///$document->addStyleDeclaration('.icon-48-comcreater {background-image: url(../media/com_comcreater/images/tux-48x48.png);}');
		//if ($submenu == 'categories') 
		//{
		//	$document->setTitle(JText::_('权限分类管理'));
		//}
	}
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($messageId)) {
			$assetName = 'com_comcreater';
		}
		else {
			$assetName = 'com_comcreater.message.'.(int) $messageId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result;
	}
}
