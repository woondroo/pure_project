<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_statistics
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
$code = $params->get('code');
$file = $params->get('file');
if ($code) echo '<script type="text/javascript">'.htmlspecialchars_decode($code).'</script>';
if ($file) echo '<script type="text/javascript" src="'.$file.'"></script>';
?>