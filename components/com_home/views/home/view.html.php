<?php
/**
 * @version		$Id: view.html.php 2011-11-21 08:38:16
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Homes component
 *
 * @package		Joomla.Site
 * @subpackage	com_home
 * @since		1.5
 */
class HomeViewHome extends JView
{
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$active	= $app->getMenu()->getActive();
		if (isset($active->query['layout'])) 
		{	
			$this->setLayout($active->query['layout']);
		}
		
		$title = $params->get('page_title', '');
		$title .= '-'.$app->getCfg('sitename');
		$this->document->setTitle($title);
		
		if ($params->get('menu-meta_description')) {
			$this->document->setDescription($params->get('menu-meta_description'));
		}
		
		if ($params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}
		
		parent::display($tpl);
	}
}
