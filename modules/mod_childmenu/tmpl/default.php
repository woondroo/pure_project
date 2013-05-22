<?php
/**
 * @version		\$Id: default.php 2012-02-03 18:48:12
 * @subpackage	com_childmenu
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access.
defined('_JEXEC') or die;
jimport('mulan.mldb');

$cateid = JRequest::getVar('id');
$com = JRequest::getVar('option');
$view = JRequest::getVar('view');
$itemid = JRequest::getVar('Itemid');

if ($itemid) {
	$extend_menu = MulanDBUtil::getObjectBySql('select id,title,params from #__menu where id='.$itemid);
	if ($extend_menu->params) {
		$json_obj = json_decode($extend_menu->params);
	}
}

$prefix = '';
if ($showchildmenupre) {
	$prefix = $childmenupre;
}

if ($showchildtoptitle) {
	$insert_style .= '.menu-top-title{background:url('.$base.$toptitlebg.') '.(-1*$toptitlebg_pos['x']).'px '.(-1*$toptitlebg_pos['y']).'px}.menu-top-title p a{background:url('.$base.$toptitleprebg.') '.(-1*$toptitleprebg_pos['x']).'px '.(-1*$toptitleprebg_pos['y']).'px}';
?>
<div class="menu-top-title"><p><a></a><?php echo $extend_menu->title.'</p>'.($childtitle ? '<span>'.$childtitle.'</span>' : ''); ?><div class="clr"></div></div>
<?php
}

$cat_link = 'index.php?option='.$com.'&view='.$view.'&Itemid='.$itemid.'&id=';
if ($json_obj->parentcid) {
	$parent_cat = MulanDBUtil::getObjectBySql('select * from #__categories where published=1 and id='.$json_obj->parentcid);
	if ($parent_cat->id) {
?>
<ul class="menu">
	<li class="item-first"></li>
<?php
		$cats = MulanDBUtil::getObjectlistBySql('select * from #__categories where published=1 and extension='.MulanDBUtil::dbQuote($com).' and lft > '.$parent_cat->lft.' and rgt < '.$parent_cat->rgt.' order by lft,id');
		if (count($cats)) {
			foreach ($cats as $cat) {
				$parent_level = $parent_cat->level;
				if ($cat->level == $parent_level+1) {
					$find_child = 0;
					$find_child_active = 0;
					$child_str = '';
					if ($showAll) {
						foreach($cats as $cat_child) {
							$child_prefix = '';
							if ($showchild2menupre) {
								$child_prefix = $child2menupre;
							}
							if ($cat_child->level == $parent_level+2 && $cat_child->parent_id == $cat->id && $json_obj->isopen) {
								if ($cat_child->id == $cateid) {
									$find_child_active = $cateid;
								}
								$child_str .= $find_child == 0 ? '<ul>' : '';
								$child_str .= '<li class="item-'.$cat_child->id.($find_child_active == $cat_child->id ? ' current active' : '').'">'.
											'<a href="'.JRoute::_($cat_link.$cat_child->id).'">'.$child_prefix.$cat_child->title.'</a>'.
											'</li>';
								$find_child ++;
							}
						}
						if ($find_child > 0) {
							$child_str .= '<div class="clr"></div></ul>';
						}
					}
?>
	<li class="item-<?php echo $cat->id.($cat->id == $cateid || $find_child_active ? ' active' : '').($child_str != '' ? ' deeper' : '').($find_child_active > 0 ? ' parent' : ''); ?>">
		<a href="<?php echo JRoute::_($cat_link.$cat->id); ?>"><?php echo $prefix.$cat->title; ?></a>
		<?php echo $child_str != '' ? $child_str : ''; ?>
	</li>
<?php
				}
			}
		}
?>
	<li class="item-end"></li>
</ul>
<?php
	}
}
$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);
?>
