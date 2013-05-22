<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_musicplayer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

$music1 = $params->get('music1');
$music2 = $params->get('music2');
$music3 = $params->get('music3');
$musics = array();
$base = JURI::base();
if ($music1) array_push($musics, $base.$music1);
if ($music2) array_push($musics, $base.$music2);
if ($music3) array_push($musics, $base.$music3);
?>
<div id="music-player<?php echo $params->get('moduleclass_sfx'); ?>">
	<object type="application/x-shockwave-flash" data="templates/system/flash/dewplayer-mini.swf?mp3=<?php echo implode('|',$musics); ?>&autoplay=1&autoreplay=1" width="160" height="20" id="dewplayer-mini">
		<param name="wmode" value="transparent" />
		<param name="movie" value="templates/system/flash/dewplayer-mini.swf?mp3=<?php echo implode('|',$musics); ?>&autoplay=1&autoreplay=1" />
	</object>
</div>