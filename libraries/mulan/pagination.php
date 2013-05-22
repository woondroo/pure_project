<?php
/**
 * @version		$Id: pagination.php 10124 2008-03-10 12:40:29Z willebil $
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.html.pagination');

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Joomla! Framework
 *
 * @author		Louis Landry <louis.landry@joomla.org>
 * @package 	Joomla.ext
 * @subpackage	mulan
 * @since		1.5
 */
class SPagination extends JPagination
{

	/**
	 * Set the pagination iteration loop values
	 */
	var $displayedPages = 0;
	
	/**
	 * Set the pagination ellipsis
	 */
	var $ellipsis = null;

	/**
	 * Constructor
	 *
	 * @param	int		The total number of items
	 * @param	int		The offset of the item to start at
	 * @param	int		The number of items to display per page
	 */
	function __construct($total, $limitstart, $limit, $displayedPages=5, $ellipsis="...")
	{
		// Value/Type checking
		$this->total		= (int) $total;
		$this->limitstart	= (int) max($limitstart, 0);
		$this->limit		= (int) max($limit, 0);
		$this->displayedPages = $displayedPages;
		$this->ellipsis = $ellipsis;

		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}

		if (!$this->limit)
		{
			$this->limit = $total;
			$this->limitstart = 0;
		}

		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}

		// Set the total pages and current page values
		if($this->limit > 0)
		{
			$this->set('pages.first', 1);
			$this->set('pages.total', ceil($this->total / $this->limit));
			$this->set('pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values
		if ($this->get('pages.total') < ($this->get('pages.first') + $this->displayedPages)) {

			$this->set('pages.start', ($this->get('pages.first') + 1));
			$this->set('pages.stop',  ($this->get('pages.total') - 1));					
		} else {
		
			if ($this->get('pages.current') < ($this->get('pages.first') + $this->displayedPages)) {
			
				$this->set('pages.start', ($this->get('pages.first') + 1));
				$this->set('pages.stop',  ($this->get('pages.first') + $this->displayedPages));
			} else if ($this->get('pages.current') > ($this->get('pages.total') - $this->displayedPages)) {
			
				$this->set('pages.start', ($this->get('pages.total') - $this->displayedPages));
				$this->set('pages.stop',  ($this->get('pages.total') - 1));
			} else {
			
				$this->set('pages.start', ($this->get('pages.current') - 2));
				$this->set('pages.stop',  ($this->get('pages.current') + 2));
			}
		}
		
		// If we are viewing all records set the view all flag to true
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
	}
	
	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @access	public
	 * @return	string	Pagination page list string
	 * @since	1.0
	 */
	function getPagesLinks()
	{
		global $mainframe;

		$lang =& JFactory::getLanguage();

		// Build the page navigation list
		$data = $this->_buildDataObject();

		$list = array();

		$itemOverride = false;
		$listOverride = false;

		/*$chromePath = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'pagination.php';
		if (file_exists($chromePath))
		{
			require_once ($chromePath);
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
				$itemOverride = true;
			}
			if (function_exists('pagination_list_render')) {
				$listOverride = true;
			}
		}*/

		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = ($itemOverride) ? pagination_item_active($data->all) : $this->_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = ($itemOverride) ? pagination_item_active($data->start) : $this->_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
		}
		
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_active($data->previous) : $this->_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_active($page) : $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page) : $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = ($itemOverride) ? pagination_item_active($data->next) : $this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
		}
		
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = ($itemOverride) ? pagination_item_active($data->end) : $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
		}

		if($this->total > $this->limit){
			return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
		}
		else{
			return '';
		}
	}
	
	function _list_render($list)
	{
		// Initialize variables
		$html = "<span class=\"pagination\">";
		//$html .= @$list['start']['data'];
		$html .= @$list['previous']['data'];
	
		foreach( $list['pages'] as $page )
		{
			if($page['data']['active']) {
				$html .= '<strong>';
			}
	
			$html .= $page['data'];
	
			if($page['data']['active']) {
				$html .= '</strong>';
			}
		}
	
		$html .= @$list['next']['data'];
		//$html .= @$list['end']['data'];
	
		$html .= "</span>";
		return $html;
	}
	
	function _item_active(&$item) {
		return "<span class=\"".$item->class."\"><a href=\"".$item->link."\" title=\"".$item->text."\">".$item->text."</a></span>";
	}
	
	function _item_inactive(&$item) {
		return "<span class=\"".$item->class."\">".$item->text."</span>";
	}
	
	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new SPaginationObject(JText::_('View All'), 'page-all');
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= JRoute::_("&limitstart=");
		}

		// Set the start and previous data objects
		$data->start	= new SPaginationObject(JText::_('Start'), 'page-start');
		$data->previous	= new SPaginationObject(JText::_('Prev'), 'page-prev');

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') -2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= JRoute::_("&limitstart=0");
			
			$data->previous->base	= $page;
			$data->previous->link	= JRoute::_("&limitstart=".$page);
		}
		$data->start->class = 'page-start';
		$data->previous->cass = 'page-prev';

		// Set the next and end data objects
		$data->next	= new SPaginationObject(JText::_('Next'), 'page-next');
		$data->end	= new SPaginationObject(JText::_('End'), 'page-end');

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= JRoute::_("&limitstart=".$next);
			
			$data->end->base	= $end;
			$data->end->link	= JRoute::_("&limitstart=".$end);
		}

		$data->pages = array();

		// Modify Date : 2008/10/17 Mender :  Mulan Su
		$start = $this->get('pages.start');
		$stop  = $this->get('pages.stop');
		$first   = $this->get('pages.first');
		$total  = $this->get('pages.total');

		$data->pages[$first] = new SPaginationObject($first, 'page-inactive');
		if ($first != $this->get('pages.current') || $this->_viewall) {
			$data->pages[$first]->base = '0';
			$data->pages[$first]->link = JRoute::_("&limitstart="); //set the empty for removal from route
			$data->pages[$first]->class = 'page-active';
		}

		if (($start - $first) > 1) {
			$data->pages[$first+1] = new SPaginationObject($this->ellipsis, 'page-inactive');
		}
		
		for ($i = $start; $i <= $stop; $i ++)
		{

			$data->pages[$i] = new SPaginationObject($i, 'page-inactive');
			if ($i != $this->get('pages.current') || $this->_viewall)
			{
				$offset = ($i -1) * $this->limit;
			
				$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route
			
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= JRoute::_("&limitstart=".$offset);
				$data->pages[$i]->class = 'page-active';
			}
		}
		
		if (($total - $stop) > 1) {
			$data->pages[$total-1] = new SPaginationObject($this->ellipsis, 'page-inactive');
		}
		
		$data->pages[$total] = new SPaginationObject($total, 'page-inactive');
		if ($total != $this->get('pages.current') || $this->_viewall) {
			$page = ($total - 1) * $this->limit;
			
			$page = $page == 0 ? '' : $page; //set the empty for removal from route
		
			$data->pages[$total]->base = $page;
			$data->pages[$total]->link = JRoute::_("&limitstart=". $page);
			$data->pages[$total]->class = 'page-active';
		}
		
		return $data;
	}
}

/**
 * Pagination object representing a particular item in the pagination lists
 *
 * @author		Louis Landry <louis.landry@joomla.org>
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class SPaginationObject extends JPaginationObject
{
	var $class = null;

	function __construct($text, $class, $base=null, $link=null)
	{
		parent::__construct($text, $base, $link);
		
		$this->class = $class;
	}
}