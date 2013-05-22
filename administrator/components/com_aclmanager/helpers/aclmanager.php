<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Aclmanager component helper.
 */
abstract class AclmanagerHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('权限'), 'index.php?option=com_aclmanager', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('权限分类'), 'index.php?option=com_aclmanager&view=aclcategories', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-aclmanager {background-image: url(../media/com_aclmanager/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('权限分类管理'));
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
			$assetName = 'com_aclmanager';
		}
		else {
			$assetName = 'com_aclmanager.message.'.(int) $messageId;
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
