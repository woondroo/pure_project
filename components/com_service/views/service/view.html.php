<?php
/**
 * @version		$Id: view.html.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the services component
 *
 * @package		Joomla.Site
 * @subpackage	com_service
 * @since		1.5
 */
class ServiceViewService extends JView
{
	protected $state;
	protected $item;
	protected $items;
	protected $productItemid;
	protected $recommends;
	protected $currentCate;
	protected $points;
	
	function display($tpl = null)
	{
		// Get some data from the models
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->item			= $this->get('Item');
		$this->productItemid = $this->get('ProductItemid');
		$this->recommends	= $this->get('RecommendPros');
		$this->currentCate	= $this->get('CurrentCate');
		$this->points		= $this->get('Points');
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$this->params	= $app->getParams();
		
		$parentTitle = $this->params->get('page_title', '');
		$this->document->setTitle($this->item->title.'-'.$parentTitle .'-'. $app->getCfg('sitename'));
		
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->item->metadata->author) {
			$this->document->setMetadata('author', $this->item->metadata->author);
		}
		
		if ($this->item->metadata->robots) {
			$this->document->setMetadata('robots', $this->item->metadata->robots);
		}
	}
}
