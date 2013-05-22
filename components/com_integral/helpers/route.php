<?php
/**
 * @version		$Id: router.php 2012-05-19 13:25:06
 * @subpackage	com_integral
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Integral Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_integral
 * @since 1.5
 */
abstract class IntegralHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the integral
	 */
	public static function getIntegralRoute($id, $catid)
	{
		$needles = array(
			'integral'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_integral&view=integral&pid='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('Integral');
			$integrals = $categories->get($catid);

			if($integrals) {
				$needles['integrals'] = array_reverse($integrals->getPath());
				$needles['categories'] = $needles['integrals'];
				$link .= '&id='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param	int		$id		The id of the integral.
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_integral&task=integral.edit&w_id='. $id;
		}
		else {
			$link = 'index.php?option=com_integral&task=integral.add&w_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	public static function getIntegralsRoute($catid)
	{
		if ($catid instanceof JIntegralsNode) {
			$id = $catid->id;
			$integrals = $catid;
		}
		else {
			$id = (int) $catid;
			$integrals = JCategories::getInstance('Integral')->get($id);
		}

		if ($id < 1) {
			$link = '';
		}
		else {
			$needles = array(
				'integrals' => array($id)
			);

			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			}
			else {
				//Create the link
				$link = 'index.php?option=com_integral&view=integrals&id='.$id;

				if ($integrals) {
					$catids = array_reverse($integrals->getPath());
					$needles = array(
						'integrals' => $catids,
						'categories' => $catids
					);

					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					}
					else if ($item = self::_findItem()) {
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_integral');
			$items		= $menus->getItems('component_id', $component->id);
			
			if ($items) {
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];
	
						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}
	
						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();
			if ($active) 
			{
				return $active->id;
			}
		}

		return null;
	}
}
