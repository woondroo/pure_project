<?php
/**
 * version $Id: view.html.php 2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


class NewViewNews extends JView
{

	protected $state;
	protected $items;
	protected $pagination;
	protected $currentCate;
	
	function display($tpl = null)
	{
		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$this->currentCate = $this->get('CurrentCate');
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
		$this->assignRef('pagination',	$pagination);

		$this->_prepareDocument();
		
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$this->params	= $app->getParams();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and news id will match
		$active	= $app->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		
		$title = $this->currentCate->ctitle ? $this->currentCate->ctitle : JText::_('COM_NEW_DEFAULT_PAGE_TITLE');
		$parentTitle = $this->params->get('page_title', $menu->title);
		$this->document->setTitle($title.'-'.$parentTitle .'-'. $app->getCfg('sitename'));
		
		if ($this->currentCate->metadesc)
		{
			$this->document->setDescription($this->currentCate->metadesc);
		}
		elseif (!$this->currentCate->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->currentCate->metakey)
		{
			$this->document->setMetadata('keywords', $this->currentCate->metakey);
		}
		elseif (!$this->currentCate->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->currentCate->metadata->author) {
			$this->document->setMetadata('author', $this->currentCate->metadata->author);
		}
		
		if ($this->currentCate->metadata->robots) {
			$this->document->setMetadata('robots', $this->currentCate->metadata->robots);
		}
	}
}
