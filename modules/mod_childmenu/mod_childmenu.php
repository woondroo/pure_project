<?php
/**
 * @version		$Id: mod_childmenu.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_childmenu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('mulan.mltools');

$base				= JURI::base();
$showAll			= $params->get('showAllChildren');
$mainmenualign		= $params->get('mainmenualign');
$showchildmenupre	= $params->get('showchildmenupre');
$childmenupre		= $params->get('childmenupre');
$showchildtoptitle	= $params->get('showchildtoptitle');
$showchild2menupre	= $params->get('showchild2menupre');
$child2menupre		= $params->get('child2menupre');
$childtitle			= $params->get('childtitle');
$toptitlebg			= $params->get('toptitlebg');
$toptitlebg_pos		= MulanToolsUtil::getMaterialPos($toptitlebg);
$toptitlebg			= $toptitlebg_pos['img'];
$toptitleprebg		= $params->get('toptitleprebg');
$toptitleprebg_pos	= MulanToolsUtil::getMaterialPos($toptitleprebg);
$toptitleprebg		= $toptitleprebg_pos['img'];

require JModuleHelper::getLayoutPath('mod_childmenu', $params->get('layout', 'default'));