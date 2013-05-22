<?php
/**
 * @version		$Id: route.php2012-01-11 06:49:50
 * @package		Joomla.Site
 * @subpackage	com_product
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Product Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_product
 * @since 1.5
 */
abstract class ProductHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the product
	 */
	public static function getProductRoute($id, $catid)
	{
		$needles = array(
			'product'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_product&view=product&pid='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('Product');
			$products = $categories->get($catid);

			if($products) {
				$needles['products'] = array_reverse($products->getPath());
				$needles['categories'] = $needles['products'];
				$link .= '&id='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
//			echo 'Itemid1:'.$item.';<br/>';
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
//			echo 'Itemid2:'.$item.';<br/>';
//var_dump($needles);echo '<br/><br/>';
		}

		return $link;
	}

	/**
	 * @param	int		$id		The id of the product.
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_product&task=product.edit&w_id='. $id;
		}
		else {
			$link = 'index.php?option=com_product&task=product.add&w_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	public static function getProductsRoute($catid)
	{
		if ($catid instanceof JProductsNode) {
			$id = $catid->id;
			$products = $catid;
		}
		else {
			$id = (int) $catid;
			$products = JCategories::getInstance('Product')->get($id);
		}

		if ($id < 1) {
			$link = '';
		}
		else {
			$needles = array(
				'products' => array($id)
			);

			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			}
			else {
				//Create the link
				$link = 'index.php?option=com_product&view=products&id='.$id;

				if ($products) {
					$catids = array_reverse($products->getPath());
					$needles = array(
						'products' => $catids,
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

			$component	= JComponentHelper::getComponent('com_product');
			$items		= $menus->getItems('component_id', $component->id);

			if ($items) {
				jimport('mulan.mldb');
				$parents = array();
				foreach ($items as $item)
				{
					/**
					 * 2012-07-04 wengebin edit
					 * 
					 * 搜索时无法正确的为“动态菜单”找到 Itemid ，
					 * 可以根据菜单的 parent_id 查询所有符合此 Itemid 的分类，然后添加。
					 */
					if ($item->parent_id && !array_key_exists($item->parent_id, $parents)) {
						$parent = MulanDBUtil::getObjectBySql('select lft,rgt from #__categories where published=1 and id='.$item->parent_id);
						if ($parent->rgt) $parents[$item->parent_id] = MulanDBUtil::getObjectlistBySql('select id from #__categories where extension=\''.$item->component.'\' and published=1 and lft>'.$parent->lft.' and rgt<'.$parent->rgt);
//						echo $item->parent_id.':';var_dump($parents[$item->parent_id]);echo ';<br/>';
					}
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];
						
						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}
						
						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
						
						if (count($parents[$item->parent_id])) {
							foreach ($parents[$item->parent_id] as $it) {
								self::$lookup[$view][$it->id] = $item->id;
							}
						}
					}
				}
			}
//			echo 'lookup:';var_dump(self::$lookup);echo'<br/><br/>';
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
