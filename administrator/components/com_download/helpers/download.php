<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Download component helper.
 */
abstract class DownloadHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_DOWNLOAD_SUBMENU_MESSAGES'), 'index.php?option=com_download', $submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_DOWNLOAD_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_download', $submenu == 'categories');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-download {background-image: url(../media/com_download/images/tux-48x48.png);}');
		if ($submenu == 'categories') 
		{
			$document->setTitle(JText::_('COM_DOWNLOAD_ADMINISTRATION_CATEGORIES'));
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
			$assetName = 'com_download';
		}
		else {
			$assetName = 'com_download.message.'.(int) $messageId;
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

