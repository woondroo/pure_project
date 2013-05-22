<?php
/**
 * @version		$Id: default.php 2012-01-11 06:49:50
 * @package		Joomla.Site
 * @subpackage	com_product
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$base = JURI::base();
$itemid = JRequest::getVar('Itemid');
$id = JRequest::getVar('id');
$start = JRequest::getVar('limitstart');
$itemLink = 'index.php?option=com_product&view=product&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$id;

$listway = $this->params->get('listway');
$openway = $this->params->get('openway');
$imglink = $this->params->get('imglink');
$imgcirclebg = $this->params->get('imgcirclebg');
$titlelink = $this->params->get('titlelink');
$showbttext = $this->params->get('showbttext');
$bttext = $this->params->get('bttext');
$titlepre = $this->params->get('titlepre');
$moretext = $this->params->get('moretext');

$date_end = $this->params->get('date_end');
$create_title_pre = $this->params->get('create_title_pre');

$listparams = explode('-',$listway);
$num_aline = intval($listparams[0]);
$item_isframe = $listparams[1];
$item_elements = $listparams[2];

$imglink = $imglink && $item_elements != 'tdt';
$titlelink = $titlelink && $item_elements != 'tdt';

$title_size = 19;
if ($item_elements == 'ttd' || $item_elements == 'tt') {
	$title_size = 25;
} else if ($item_elements == 'tdt') {
	$title_size = 16;
} else if ($num_aline > 1) {
	$title_size = 27;
}

$desc_size = 70;
if ($item_elements == 'ttd') {
	$desc_size = 128;
}

if ($imgcirclebg) {
	$document = JFactory::getDocument();
	$insert_style = '.list-img-area{background-image:url('.$base.$imgcirclebg.');}';
	$document->addStyleDeclaration($insert_style);
}
?>
<div class='product-list list-area-n<?php echo $num_aline; ?> list-<?php echo $listway; ?>'>
	<?php
	if (count($this->items)) {
		foreach($this->items as $key=>$o) {
			$title = $o->title;
			$sub_title = MulanStringUtil::substr_zh(strip_tags($o->title), $title_size, '...');
			$date = date("( Y-m-d )",strtotime($o->addtime));
	?>
	<?php echo $item_elements == 'tdt' ? '<a'.($openway ? '' : ' target="_blank"').' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '<div'; ?><?php echo $key % $num_aline == 0 && $num_aline > 1 ? ' style="margin-left:0;"' : ''; ?> class='list-item list-item-n<?php echo $num_aline; ?> list-item-n<?php echo $num_aline; ?>-e<?php echo $item_elements; ?>'>
		<?php
		$item_str = '';
		/**
		 * 图片部分
		 */
		$item_str .= $imglink ? '<a' : '<div';
		$item_str .= $openway && $imglink ? '' : ' target="_blank"';
		$item_str .= $imglink ? ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '';
		$item_str .= ' title="'.$title.'" class="list-img-area list-img-n'.$num_aline.' list-img-'.$item_isframe.'">';
		$item_str .= '<div class="list-img" style="background-image:url('.$base.$o->image.');"></div>';
		$item_str .= $imglink ? '</a>' : '</div>';
		
		if ($num_aline == 1 && $item_elements != 'ittd') $item_str = '';
		
		/**
		 * 自动标题部分
		 */
		if ($item_elements == 'tdt') {
			$item_str .= '<div class="list-text-container"><div class="list-auto-title">'.$create_title_pre.'00'.($key+1).'</div>';
		}
		
		/**
		 * 日期部分
		 */
		if ($item_elements == 'tdt') {
			$date = strtotime($o->addtime);
			$min_date = date('Y-m-d',$date);
			$item_str .= '<div class="list-date">'.$date_end.$min_date.'</div>';
		}
		
		/**
		 * 标题部分
		 */
		if ($item_elements == 'title' || $item_elements == 'tb' || $item_elements == 'tdt') {
			$item_str .= $titlelink ? '<a' : '<div';
			$item_str .= $openway && $titlelink ? '' : ' target="_blank"';
			$item_str .= $titlelink ? ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '';
			$item_str .= ' class="list-title list-title-'.$item_isframe.'"';
			$item_str .= ' title="'.$o->title.'">';
			$item_str .= $sub_title;
			$item_str .= $titlelink ? '</a>' : '</div>';
		}
		
		if ($item_elements == 'tdt') $item_str .= '</div>';
		
		/**
		 * 描述
		 */
		if ($item_elements == 'ittd' || $item_elements == 'ttd') {
			$sub_title .= '&nbsp;&nbsp;<span class="list-c-date-inline">'.$date.'</span>';
			$desc = MulanStringUtil::substr_zh(strip_tags($o->description), $desc_size, '...');
			if ($item_elements == 'ttd') $desc .= '&nbsp;<a'.($openway ? '' : ' target="_blank"').' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'" class="list-c-in-inline" title="'.$moretext.'">'.$moretext.'</a>';
		}
		
		/**
		 * 按钮部分
		 */
		if ($item_elements == 'tb') {
			$item_str .= '<a';
			$item_str .= $openway ? '' : ' target="_blank"';
			$item_str .= ' class="list-bt" href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'">'.($showbttext ? $bttext : '').'</a>';
		}
		
		/**
		 * 标题-简述 类型排列的 html 元素拼凑
		 */
		if ($num_aline == 1) {
			$item_str .= '<div class="list-content list-'.$item_elements.'-content">';
			$item_str .= $titlelink ? '<a' : '<div';
			$item_str .= $openway && $titlelink ? '' : ' target="_blank"';
			$item_str .= $titlelink ? ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '';
			$item_str .= ' class="list-c-title"'.($item_elements == 'tt' ? ' style="background-image:url('.$base.$titlepre.');"' : '').' title="'.$title.'">'.$sub_title;
			$item_str .= $titlelink ? '</a>' : '</div>';
			if ($item_elements == 'tt') $item_str .= '<div class="list-c-date">'.$date.'</div>';
			if ($desc) $item_str .= '<div class="list-c-text">'.$desc.'</div>';
			if ($item_elements == 'ittd') {
				$item_str .= '<a';
				$item_str .= $openway ? '' : ' target="_blank"';
				$item_str .= ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"';
				$item_str .= ' class="list-c-in" title="'.$moretext.'">'.$moretext;
				$item_str .= '</a>';
			}
			$item_str .= '</div>';
		}
		
		echo $item_str;
		?>
	<?php echo $item_elements == 'tdt' ? '</a>' : '</div>'; ?>
	<?php
		}
	} else {
		echo '<div class="no-list">暂无数据！</div>';
	}
	?>
	<div class='clr'></div>
</div>
<div class="content-bottom-line"></div>
<div class='pagination'>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
