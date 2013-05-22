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
 * HTML View class for the Blanks component
 *
 * @package		Joomla.Site
 * @subpackage	com_blank
 * @since		1.5
 */
class BlankViewBlank extends JView
{
	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$active		= $app->getMenu()->getActive();
		
		if (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		
		$title = $params->get('page_title', '').'-'.$app->getCfg('sitename');
		$this->document->setTitle($title);
		
		if ($params->get('menu-meta_description')) {
			$this->document->setDescription($params->get('menu-meta_description'));
		}
		
		if ($params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}
		
		// Get some data from the models
		parent::display($tpl);
	}
}
