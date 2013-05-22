<?php
/**
 * @version		$Id: default.php 2012-01-11 09:44:48
 * @package		Joomla.Site
 * @subpackage	com_new
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$base = JURI::base();
$itemid = JRequest::getVar('Itemid');
$cateid = JRequest::getVar('id');
$start = JRequest::getVar('limitstart');
$itemLink = 'index.php?option=com_new&view=new&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid;

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

$item_width = $this->params->get('item_width');
$item_height = $this->params->get('item_height');
$item_pos = explode(' ',$this->params->get('item_pos'));

$item_bt_width = $this->params->get('item_bt_width');
$item_bt_height = $this->params->get('item_bt_height');
$item_bt_pos = explode(' ',$this->params->get('item_bt_pos'));

$item_img_width = $this->params->get('item_img_width');
$item_img_height = $this->params->get('item_img_height');
$item_img_circle = $this->params->get('item_img_circle');
$item_img_pos = explode(' ',$this->params->get('item_img_pos'));

$item_title_size = $this->params->get('item_title_size');
$item_title_width = $this->params->get('item_title_width');
$item_title_height = $this->params->get('item_title_height');
$item_title_pos = explode(' ',$this->params->get('item_title_pos'));

$item_auto_title_width = $this->params->get('item_auto_title_width');
$item_auto_title_height = $this->params->get('item_auto_title_height');
$item_auto_title_pos = explode(' ',$this->params->get('item_auto_title_pos'));

$item_desc_size = $this->params->get('item_desc_size');
$item_desc_width = $this->params->get('item_desc_width');
$item_desc_height = $this->params->get('item_desc_height');
$item_desc_pos = explode(' ',$this->params->get('item_desc_pos'));

$item_date_width = $this->params->get('item_date_width');
$item_date_height = $this->params->get('item_date_height');
$item_date_pos = explode(' ',$this->params->get('item_date_pos'));

$listparams = explode('-',$listway);
$num_aline = intval($listparams[0]);
$item_elements = $listparams[2];

$imglink = $imglink && $item_elements != 'tdt';
$titlelink = $titlelink && $item_elements != 'tdt';

$insert_style = '';
if ($imgcirclebg) {
	$insert_style = '.list-img-area{background-image:url('.$base.$imgcirclebg.')}';
}

$new_item_width = max(intval($item_img_width)+intval($item_img_circle)*2,
						intval($item_title_width == '100%' ? 0 : $item_title_width),
						intval($item_bt_width == '100%' ? 0 : $item_bt_width));
if ($item_width != 'auto') $new_item_width = intval($item_width);
$news_items_width = $new_item_width*$num_aline + intval($item_pos[3])*($num_aline-1);
$insert_style .= '.new-list{width:'.$news_items_width.'px}.list-item{width:'.$new_item_width.'px;height:'.$item_height.';margin:'.implode(' ',$item_pos).';}';
?>
<div class="new-list list-<?php echo $listway; ?>">
	<?php
	if (count($this->items)) {
		foreach($this->items as $key=>$o) {
			$title = $o->title;
			$sub_title = MulanStringUtil::substr_zh(strip_tags($o->title), $item_title_size, '...');
			$date = date("( Y-m-d )",strtotime($o->addtime));
			
			echo ($item_elements == 'tdt'
				 ? '<a'.($openway ? '' : ' target="_blank"').' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"'
				 : '<div')
				 .($key % $num_aline == 0 && $num_aline > 1 ? ' style="margin-left:0;"' : '')
				 .' class="list-item list-item-n'.$num_aline.'">';
			
			$item_str = '';
			
			/**
			 * 图片部分
			 */
			$item_str .= $imglink ? '<a' : '<div';
			$item_str .= $openway && $imglink ? '' : ' target="_blank"';
			$item_str .= $imglink ? ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '';
			$item_str .= ' title="'.$title.'" class="list-img-area list-img-n'.$num_aline.'">';
			$item_str .= '<div class="list-img" style="background-image:url('.$base.$o->image.');"></div>';
			$item_str .= $imglink ? '</a>' : '</div>';
			
			if ($num_aline == 1 && $item_elements != 'ittd') {
				$item_str = '';
			} else if ($item_elements != 'ittd') {
				if ($key == 0) $insert_style .= '.list-img-area{width:'.$item_img_width.';height:'.$item_img_height.';padding:'.$item_img_circle.';margin:'.implode(' ',$item_img_pos).';}';
			}
			
			/**
			 * 自动标题部分
			 */
			if ($item_elements == 'tdt') {
				$item_str .= '<div class="list-text-container"><div class="list-auto-title">'.$create_title_pre.'00'.($key+1).'</div>';
				
				if ($key == 0) $insert_style .= '.list-auto-title{width:'.$item_auto_title_width.';height:'.$item_auto_title_height.';line-height:'.$item_auto_title_height.';padding:'.implode(' ',$item_auto_title_pos).';}';
			}
			
			/**
			 * 日期部分
			 */
			if ($item_elements == 'tdt') {
				$date = strtotime($o->addtime);
				$min_date = date('Y-m-d',$date);
				$item_str .= '<div class="list-date">'.$date_end.$min_date.'</div>';
				
				if ($key == 0) $insert_style .= '.list-date{width:'.$item_date_width.';height:'.$item_date_height.';line-height:'.$item_date_height.';padding:'.implode(' ',$item_date_pos).';}';
			}
			
			/**
			 * 标题部分
			 */
			if ($item_elements == 'title' || $item_elements == 'tb' || $item_elements == 'tdt') {
				$item_str .= $titlelink ? '<a' : '<div';
				$item_str .= $openway && $titlelink ? '' : ' target="_blank"';
				$item_str .= $titlelink ? ' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'"' : '';
				$item_str .= ' class="list-title"';
				$item_str .= ' title="'.$o->title.'">';
				$item_str .= $sub_title;
				$item_str .= $titlelink ? '</a>' : '</div>';
				
				if ($key == 0) $insert_style .= '.list-title{width:'.$item_title_width.';height:'.$item_title_height.';line-height:'.$item_title_height.';padding:'.implode(' ',$item_title_pos).';}';
			}
			
			if ($item_elements == 'tdt') $item_str .= '</div>';
			
			/**
			 * 描述
			 */
			if ($item_elements == 'ittd' || $item_elements == 'ttd') {
				$sub_title .= '&nbsp;&nbsp;<span class="list-c-date-inline">'.$date.'</span>';
				$desc = MulanStringUtil::substr_zh(strip_tags($o->description), $item_desc_size, '...');
				if ($item_elements == 'ttd') $desc .= '&nbsp;<a'.($openway ? '' : ' target="_blank"').' href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'" class="list-c-in-inline" title="'.$moretext.'">'.$moretext.'</a>';
				if ($key == 0) {
					$list_content_height = max(intval($item_title_height),intval($item_date_height))+intval($item_desc_height)+intval($item_desc_pos[0])+intval($item_desc_pos[2]);
					if ($item_elements == 'ittd') $list_content_height += intval($item_bt_height) + intval($item_bt_pos[0]) + intval($item_bt_pos[2]);
					$list_content_margin = (intval($item_height)-$list_content_height)/2;
					
					$insert_style .= '.list-c-text{width:'.$item_desc_width.';height:'.$item_desc_height.';padding:'.implode(' ',$item_desc_pos).';}' .
									'.list-c-date-inline{height:'.$item_date_height.';line-height:'.$item_date_height.';}';
					if ($item_elements == 'ittd') {
						$insert_style .= '.list-c-title,.list-c-text,.list-c-in{float:left}.list-content{float:right}' .
										'.list-c-in{width:'.$item_bt_width.';height:'.$item_bt_height.';line-height:'.$item_bt_height.';padding:'.implode(' ',$item_bt_pos).';}' .
										'.list-img-area{width:'.$item_img_width.';height:'.$item_img_height.';padding:'.$item_img_circle.';left:'.$item_img_pos[3].';top:'.$item_img_pos[0].';}';
					} else {
						$insert_style .= '.list-c-in-inline{width:'.$item_bt_width.';height:'.$item_bt_height.';line-height:'.$item_bt_height.';padding:'.implode(' ',$item_bt_pos).';}';
					}
				}
			} else if ($item_elements == 'tt') {
				if ($key == 0) {
					$list_content_height = max(intval($item_title_height),intval($item_date_height));
					$list_content_margin = (intval($item_height)-$list_content_height)/2;
					$insert_style .= '.list-c-date{width:'.$item_date_width.';height:'.$item_date_height.';line-height:'.$item_date_height.';}';
				}
			}
			
			/**
			 * 按钮部分
			 */
			if ($item_elements == 'tb') {
				$item_str .= '<a';
				$item_str .= $openway ? '' : ' target="_blank"';
				$item_str .= ' class="list-bt" href="'.$itemLink.'&pid='.$o->id.'&alias='.$o->alias.'">'.($showbttext ? $bttext : '').'</a>';
				
				if ($key == 0) $insert_style .= '.list-bt{width:'.$item_bt_width.';height:'.$item_bt_height.';line-height:'.$item_bt_height.';margin:'.implode(' ',$item_bt_pos).';}';
			}
			
			/**
			 * 标题-简述 类型排列的 html 元素拼凑
			 */
			if ($num_aline == 1) {
				if ($key == 0) {
					$list_content_width = 'width:100%;';
					if ($item_elements == 'ittd') $list_content_width = max(intval($item_title_width)+intval($item_title_pos[0])+intval($item_title_pos[2]),
																			intval($item_desc_width)+intval($item_desc_pos[0])+intval($item_desc_pos[2]),
																			intval($item_date_width)+intval($item_date_pos[0])+intval($item_date_pos[2])).'px';
					$insert_style .= '.list-content{width:'.$list_content_width.';height:'.$list_content_height.'px;margin:'.$list_content_margin.'px 0;}' .
									'.list-content .list-c-title{width:'.$item_title_width.';height:'.$item_title_height.';line-height:'.$item_title_height.';padding:'.implode(' ',$item_title_pos).';}';
				}
				
				$item_str .= '<div class="list-content">';
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
			
			$item_str .= $item_elements == 'tdt' ? '</a>' : '</div>';
			echo $item_str;
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
<?php
$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);
?>