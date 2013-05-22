<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_banner_index
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mldb.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'mulan'.DS.'mlstring.php' );
jimport('mulan.mlhtml');
?>
<div class="banner-index">
	<div class="frame960 relative">
		<div class="banner-img absolute">
			<div class="relative">
			<?php foreach( $items as $index=>$o){?>
			<div class="leftimg left<?php echo $index+1?>" 
					moveInDerection="<?php echo $o->left_in_derc?>"
					moveInSpeed="<?php echo $o->left_in_movespeed?>"
					moveInType="<?php echo $o->left_in_movetype?>"
					fadeInSpeed="<?php echo $o->left_in_fadespeed?>"
					fadeInType="<?php echo $o->left_in_fadetype?>"
					
					moveOutDerection="<?php echo $o->left_out_derc?>"
					moveOutSpeed="<?php echo $o->left_out_movespeed?>"
					moveOutType="<?php echo $o->left_out_movetype?>"
					fadeOutSpeed="<?php echo $o->left_out_fadespeed?>"
					fadeOutType="<?php echo $o->left_out_fadetype?>"
			>
				<?php echo $o->link?'<a href="'.$o->link.'" title="'.$o->title.'">':''?>
					<img src="<?php echo $o->leftimg?>" alt="<?php echo $o->title?>"/>
				<?php echo $o->link?'</a>':'';?>
			</div>
			<div class="rightimg right<?php echo $index+1?>"
					moveInDerection="<?php echo $o->right_in_derc?>"
					moveInSpeed="<?php echo $o->right_in_movespeed?>"
					moveInType="<?php echo $o->right_in_movetype?>"
					fadeInSpeed="<?php echo $o->right_in_fadespeed?>"
					fadeInType="<?php echo $o->right_in_fadetype?>"
					
					moveOutDerection="<?php echo $o->right_out_derc?>"
					moveOutSpeed="<?php echo $o->right_out_movespeed?>"
					moveOutType="<?php echo $o->right_out_movetype?>"
					fadeOutSpeed="<?php echo $o->right_out_fadespeed?>"
					fadeOutType="<?php echo $o->right_out_fadetype?>"
			>
				<?php echo $o->link?'<a target="_blank" href="'.$o->link.'" title="'.$o->title.'">':''?>
					<img src="<?php echo $o->rightimg?>" alt="<?php echo $o->title?>"/>
				<?php echo $o->link?'</a>':'';?>
			</div>
			<?php }?>
			</div>
		</div>
		<ul class="banner-bt absolute">
			<?php foreach( $items as $index=>$o){?>
			<li>
				<a class="pointer" target="<?php echo $index+1?>">
					<span class="ico" style="background-image:url(<?php echo $o->logo?>)"></span>
				</a>
			</li>
			<?php }?>
		</ul>
	</div>
</div>
