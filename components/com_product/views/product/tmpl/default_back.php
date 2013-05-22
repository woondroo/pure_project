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
$itemid = JRequest::getVar("Itemid");
$cateid = JRequest::getVar('id');
$start = $this->start;

jimport('mulan.mldb');
jimport('mulan.mlimage');
jimport('mulan.mlhtml');
$pro_imgs = MulanImageUtil::images($this->item->proimgs);
$about_pros = MulanDBUtil::getObjectlistBySql('select * from #__product where id in('.$this->item->about.')');

$base = JURI::base();
$itemLink = 'index.php?option=com_product&view=product&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid.'&pid=';
?>
<div class='product-item-view'>
	<div class="product-title"><span><?php echo $this->item->title; ?></span></div>
	<div class="product-mess-area">
		<div class="product-img-view"></div>
		<div class="product-mess right">
			<div class="line-mess"><span>Model No. :</span><?php echo $this->item->modelno; ?></div>
			<div class="line-mess"><span>Brand Name :</span><?php echo $this->item->brandname; ?></div>
			<div class="line-mess"><span>Country of Orgin :</span><?php echo $this->item->country; ?></div>
			<div class="line-mess"><span>Unit Price :</span><?php echo $this->item->price; ?></div>
			<div class="line-mess"><span>Minimum Order :</span><?php echo $this->item->minorder; ?></div>
			<?php
			echo MulanDBUtil::loadmod('mod_switcher','switcher-page',array('isdetail'=>true,'from'=>$this->item->proimgs,'pimgsdesc'=>false));
			?>
		</div>
		<div class="clr"></div>
	</div>
	<div class="product-desc-tip">产品描述</div>
	<div class="product-desc">
		<?php echo $this->item->description; ?>
	</div>
	<div class="product-about-tip">相关产品</div>
	<div class="product-about">
		<?php
		if (count($about_pros)) {
			foreach ($about_pros as $key=>$pro) {
	?>
		<div<?php echo $key%3 == 0 ? ' style="margin-left:0;margin-top:0;"' : ' style="margin-top:0;"' ?> class='product-item'>
			<a href="<?php echo $itemLink.$pro->id; ?>" class="product-img-area">
				<div class="product-img" style="background:url(<?php echo $base.$pro->image; ?>);"></div>
			</a>
			<a href="<?php echo $itemLink.$pro->id; ?>" class='title' title='<?php echo $pro->title?>'><?php echo MulanStringUtil::substr_zh($pro->title,27,'...');?></a>
		</div>
	<?php
			}
		} else {
			echo '<div class="no-list">无相关产品！</div>';
		}
		?>
		<div class="clr"></div>
	</div>
	<?php
	echo MulanDBUtil::loadmod('mod_share','siteshare',array('simg'=>$base.$this->item->image,'stitle'=>$this->item->title,'surl'=>JRoute::_($itemLink.$this->item->id)));
	?>
	<div class="content-bottom-line"></div>
</div>