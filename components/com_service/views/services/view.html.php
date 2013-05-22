<?php
/**
 * version $Id: view.html.php 21593 2011-06-21 02:45:51Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_service
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


class ServiceViewServices extends JView
{

	protected $state;
	protected $items;
	protected $currentCate;
	
	function display($tpl = null)
	{
		
		$app		= JFactory::getApplication();
		
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$currentCate= $this->get('CurrentCate');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		
		// Prepare the data.
		// Compute the  slug & link url.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		}
		
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('currentCate',	$currentCate);
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and services id will match
		$active	= $app->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_SERVICE_DEFAULT_PAGE_TITLE'));
		}
		$id = (int) @$menu->query['id'];
		$title = $this->currentCate[0]->ctitle ? $this->currentCate[0]->ctitle : '服务列表';
		$parentTitle = $this->params->get('page_title', '');
		$this->document->setTitle($title.'-'.$parentTitle .'-'. $app->getCfg('sitename'));
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
