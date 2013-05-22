<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
if($showItem){
	$repeat = $showItem->repeat;
	switch($repeat)
	{
		case 0:$repeat = 'no-repeat';break;
		case 1:$repeat = 'repeat-x';break;
		case 2:$repeat = 'repeat-y';break;
		case 3:$repeat = '';break;
	}

?>
<div id="top-add-show" 
	 style="background:<?php echo $showItem->backgroundcolor?$showItem->backgroundcolor:'#fff'?> <?php echo $showItem->backgroundimg?'url('.$showItem->backgroundimg.')':''?> <?php echo repeat; ?>">
	<div  class="frame960 center relative">
		<a title="<?php echo $showItem->title?>" href="<?php echo $showItem->link? $showItem->link:'#' ?>">
			<img id="add-img" alt="<?php echo $showItem->imgalt?$showItem->imgalt:'Woondroo'?>" src="<?php echo $showItem->img;?>"/>
		</a>
		<a id="top-add-close" class="absolute"></a>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var frameHeight = $("#add-img").height();
		var showTime = <?php echo $showItem->showtime?$showItem->showtime:2000;?>;
		var openspeed = <?php echo $showItem->openspeed?$showItem->openspeed:3000;?>;
		var closespeed = <?php echo $showItem->closespeed?$showItem->closespeed:1000;?>;
		var autoclose =  <?php echo $showItem->autoclose?'true':'false' ;?>;
		$("#add-img").show();
		$("#top-add-close").hide();
		$("#top-add-show").animate({
			height:frameHeight
		},openspeed,function(){
			if(autoclose)
			{
				setTimeout(function(){
					closeThis();
				},showTime);
			}
			else
			{
				$("#top-add-close").fadeIn();
				$("#top-add-close").click(function(){
					closeThis();
				})
			}
		});
		function closeThis()
		{
			$("#top-add-show").animate({
				height:0
			},closespeed,function(){
				$("#add-img").hide();
			});	
		}
	});
</script>
<?php }?>