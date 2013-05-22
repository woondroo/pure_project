<?php
/**
 * @version		$Id: category.php 2012-05-19 13:25:06
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
 * Integral Component Integrals Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_integral
 * @since 1.6
 */
class IntegralCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__integral';
		$options['extension'] = 'com_integral';
		parent::__construct($options);
	}
}
