<?php

/**
 * Joomla! 1.5 component Qlue 404
 *
 * @version $Id: router.php 2010-11-30 12:52:08 svn $
 * @author Aaron Harding - Qlue
 * @package Joomla
 * @subpackage Qlue 404
 * @license GNU/GPL
 *
 * Qlue 404 will detect all the major errors usually found on a website (404, 500) errors. This extension will allow you to custom these custom error pages with ease while still maintaining the proper error codes for seo purposes. 
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
 */
function Qlue404BuildRoute(&$query) {
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}
	return $segments;
}
/*
 * Function to convert a SEF URL back to a system URL
 */
function Qlue404ParseRoute($segments) {
	$vars = array();
	return $vars;
}
?>