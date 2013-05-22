<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_searchall
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
jimport('mulan.mldb');
jimport('mulan.mlstring');
jimport('joomla.html.pagination');

$search_key_words = JRequest::getVar('search_key_words');
$limit = JRequest::getVar('limit',12);
$start = JRequest::getVar('start',0);
$limit_sql = 'limit '.$start.','.$limit;

$searchmods = $params->get('searchmods');
$searchfield = $params->get('searchfield');
$searchshow = $params->get('searchshow');

$searchfield_array = explode(',',$searchfield);
$search_wheres = array();
if (count($searchfield_array) && $search_key_words) {
	foreach ($searchfield_array as $f) {
		$search_wheres[] = $f.' like \'%'.$search_key_words.'%\'';
	}
}

$searchfields = array('id','catid');
if (is_array($searchfield_array)) {
	$searchfields = array_merge($searchfields,$searchfield_array);
}
$searchshow_array = explode(',',$searchshow);
if (count($searchshow_array)) {
	foreach ($searchshow_array as $s) {
		$ele = explode(':',$s);
		$searchfields[] = $ele[0];
	}
}
$searchfields = array_unique($searchfields);

$sql = 'select '.implode(',',$searchfields).' from #__'.$searchmods.(count($search_wheres) ? ' where '.implode(' OR ',$search_wheres) : '').' order by id desc '.$limit_sql;
$os = MulanDBUtil::getObjectlistBySql($sql);

$sql_all = 'select count(*) as count from #__'.$searchmods.(count($search_wheres) ? ' where '.implode(' OR ',$search_wheres) : '');
$total = MulanDBUtil::getObjectBySql($sql_all)->count;
$page = new JPagination($total, $start, $limit, 'search_key_words='.$search_key_words.'&');

$itemid = MulanDBUtil::getObjectBySql('select id from #__menu where link like \'%option=com_'.$searchmods.'&view='.$searchmods.'%\' and menutype=\'mainmenu\' limit 1')->id;
$itemLink = 'index.php?option=com_'.$searchmods.'&view='.$searchmods.'&Itemid='.$itemid.'&limitstart=0';
?>
<div class="search-content">
	<?php
	if (count($os)) {
	?>
	<table>
		<tr>
		<?php
		if (count($searchshow_array)) {
			foreach ($searchshow_array as $s) {
				$ele = explode(':',$s);
				echo '<th width="'.(1/count($searchshow_array)*100).'%">'.$ele[1].'</th>';
			}
		}
		?>
		</tr>
		<?php
			foreach ($os as $o) {
		?>
		<tr>
			<?php
			if (count($searchshow_array)) {
				foreach ($searchshow_array as $key=>$s) {
					$ele = explode(':',$s);
					if ($key == 0) {
			?>
			<td><a target="_blank" href="<?php echo $itemLink.($o->catid ? '&id='.$o->catid : '').'&pid='.$o->id; ?>"><?php echo MulanStringUtil::substr_zh(strip_tags($o->$ele[0]),200,'...'); ?></a></td>
			<?php
					} else {
			?>
			<td><?php echo MulanStringUtil::substr_zh(strip_tags($o->$ele[0]),200,'...'); ?></td>
			<?php
					}
				}
			}
			?>
		</tr>
		<?php
			}
		?>
	</table>
	<?php
	} else {
		echo '<div class="no-list">Current no data!</div>';
	}
	?>
</div>
<div class="content-bottom-line"></div>
<div class='pagination'>
	<?php echo $page->getPagesLinks(); ?>
	<div class='clr'></div>
</div>

