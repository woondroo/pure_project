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
 * HTML View class for the rssfeeds component
 *
 * @package		Joomla.Site
 * @subpackage	com_rssfeed
 * @since		1.5
 */
class RssfeedViewRssfeed extends JView
{
	protected $state;
	protected $item;
	protected $pre_item;
	protected $next_item;
	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		// Get some data from the models
		$this->feed	= $this->get('Feed');
		
		$parentTitle = $params->get('page_title', '');
		$this->document->setTitle($this->item->title.'-'.$parentTitle .'-'. $app->getCfg('sitename'));
		
		parent::display($tpl);
	}
}
