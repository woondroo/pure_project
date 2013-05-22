<?php
/**
 * @version		$Id: featured.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Frontpage Component Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class ContentModelHome extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			
		}

		parent::__construct($config);
	}
	
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);
		$this->setState('layout', JRequest::getCmd('layout'));
	}
}
