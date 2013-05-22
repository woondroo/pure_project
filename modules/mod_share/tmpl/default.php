<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_share
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

$simg = $params->get('simg');
$stitle = str_replace(array('\'','"'),'&quot;',strip_tags($params->get('stitle')));
$surl = $params->get('surl');
?>
<div class="share-buttons">
<?php
if (!$params->get('shareway')) {
?>
	<div class="add-plus">
		<font>&nbsp;-&nbsp;分享到&nbsp;</font>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'sina','<?php echo $surl; ?>');" class="add-plus-one sina">新浪微博</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'tqq','<?php echo $surl; ?>');" class="add-plus-one tqq">腾讯微博</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'qzone','<?php echo $surl; ?>');" class="add-plus-one qzone">QQ空间</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'tieba','<?php echo $surl; ?>');" class="add-plus-one tieba">百度贴吧</a>
		<?php
		/*
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'tsohu','<?php echo $surl; ?>');" class="add-plus-one tsohu">搜狐微博</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'douban','<?php echo $surl; ?>');" class="add-plus-one douban">豆瓣</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'renren','<?php echo $surl; ?>');" class="add-plus-one renren">人人网</a>
		<a href="javascript:;" simg="<?php echo $simg; ?>" stitle="<?php echo $stitle; ?>" onclick="return doShare(this,'kaixin001','<?php echo $surl; ?>');" class="add-plus-one kaixin001">开心网</a>
		*/
		?>
		<div class="clr"></div>
	</div>
	<?php
	if ($params->get('hasscript')) {
	?>
	<script type="text/javascript">
	$(document).ready(function(){
		if ($('.add-plus')[0] != undefined) {
			$('.share-buttons').hover(function(){
				$(this).find('.add-plus').css({'width':'400px'});
			},function(){
				$(this).find('.add-plus').css({'width':'145px'});
			});
		}
	});
	</script>
<?php
	}
} else {
?>
	<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4fbafbba68381ad5"></script>
<?php
}
?>
</div>