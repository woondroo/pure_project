<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_sitelanguage
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('mulan.mltools');
$base = JURI::base();

$app = JFactory::getApplication();
$pagelanguage = $app->getCfg('pagelanguage');

$showbg = $params->get('showbg');
$usebg = $params->get('usebg');
$languagebg = $params->get('languagebg');
$languagebg_pos = MulanToolsUtil::getMaterialPos($languagebg);
$languagebg = $base.$languagebg_pos['img'];

$isselect = $params->get('isselect');
$showtext = $params->get('showtext');

$left = $params->get('left', '0px');
$right = $params->get('right', 0);
$top = $params->get('top', '0px');
$bottom = $params->get('bottom', 0);

$width = $params->get('width', '45px');
$height = $params->get('height', '20px');

$languagetext = $params->get('languagetext');
if ($languagetext) $languagetext = explode('|',$languagetext);
$languagesrc = $params->get('languagesrc');
if ($languagesrc) $languagesrc = explode('|',$languagesrc);

$insert_style = '';
$document = JFactory::getDocument();
if ($isselect && count($languagetext)) {
	$main_language_style = array();
	array_push($main_language_style, 'width:'.$width);
	array_push($main_language_style, 'height:'.$height);
	array_push($main_language_style, $right ? 'right:'.$right : 'left:'.$left);
	array_push($main_language_style, $bottom ? 'bottom:'.$bottom : 'top:'.$top);
	
	$cur_lang_style = array();
	array_push($cur_lang_style, 'width:'.$width);
	array_push($cur_lang_style, 'height:'.$height);
	array_push($cur_lang_style, 'line-height:'.$height);
	array_push($cur_lang_style, 'background:url('.$languagebg.') '.(-1*$languagebg_pos['x']).'px '.(-1*$languagebg_pos['y']).'px');
	
	$languages_container_style = array();
	array_push($languages_container_style, 'width:'.$width);
	array_push($languages_container_style, 'height:'.(intval($height)*count($languagetext)).'px');
	
	$item_style = array();
	array_push($item_style, 'width:'.$width);
	array_push($item_style, 'height:'.$height);
	array_push($item_style, 'line-height:'.$height);
	
	$cur_language_style = $item_style;
	if ($languagebg && $showbg) array_push($cur_language_style, 'background:url('.$languagebg.') '.(-1*$languagebg_pos['x']).'px '.(-1*$languagebg_pos['y']).'px');
	
	$lang_html = '';
	$cur_lang_html = '';
	for ($i = 0; $i < count($languagetext); $i++) {
		$languagesrc_splits = explode('-',$languagesrc[$i]);
		if ($pagelanguage == $languagesrc_splits[1]) {
			$insert_style .= '.main-languages #0_lang{'.implode(';',$cur_language_style).'}';
			$cur_lang_html = '<a href="javascript:;" id="0_lang" class="cur-language">'.($showtext ? $languagetext[$i] : '').'</a>';
		}
		
		if (!$usebg) {
			$bg_pos = 1;
			if ($i == 0) {
				$bg_pos = 1;
			} else if ($i < count($languagetext)-1) {
				$bg_pos = 2;
			} else {
				$bg_pos = 3;
			}
		} else {
			$bg_pos = $i;
		}
		if ($languagebg && $showbg) {
			if ($i > 0) array_pop($item_style);
			
			$pos_x = (-1*intval($width)*$bg_pos-$languagebg_pos['x']).'px';;
			$pos_y = $pagelanguage == $languagesrc_splits[1] ? (-1*intval($height)-$languagebg_pos['y']).'px' : (-1*$languagebg_pos['y']).'px';
			array_push($item_style, 'background:url('.$languagebg.') '.$pos_x.' '.$pos_y);
		}
		$insert_style .= '.main-languages .'.$languagesrc_splits[0].'{'.implode(';',$item_style).'}';
		$lang_html .= $pagelanguage == $languagesrc_splits[1] 
			? '<span id="'.$i.'_lang" bgpos="'.$bg_pos.'" class="'.$languagesrc_splits[0].' '.$languagesrc_splits[0].'-active">'.($showtext ? $languagetext[$i] : '').'</span>' 
			: '<a id="'.$i.'_lang" bgpos="'.$bg_pos.'" href="'.$root.$languagesrc_splits[0].'" class="'.$languagesrc_splits[0].'">'.($showtext ? $languagetext[$i] : '').'</a>';
	}
	$insert_style .= '.main-languages{'.implode(';',$main_language_style).'}.main-languages .cur-language{'.implode(';',$cur_lang_style).'}';
	$insert_style .= '#languages-container{'.implode(';',$languages_container_style).'}';
?>
<div class="main-languages">
	<?php echo $cur_lang_html; ?>
	<div id="languages-container">
		<?php echo $lang_html; ?>
		<div class="clr"></div>
	</div>
</div>
<script type="text/javascript">
	$('.cur-language').hover(function(){
		$('#languages-container').show();
	});
	$('#languages-container').hover(function(){
	},function(){
		$('#languages-container').hide();
	});
<?php
	if ($languagebg && $showbg) {
?>
	$('#languages-container a').hover(function(){
		$(this).css({'background-position':(-1*(parseInt($(this).attr('bgpos')))*<?php echo intval($width); ?>-<?php echo $languagebg_pos['x']; ?>)+'px '+(-1*parseInt($(this).height())-<?php echo $languagebg_pos['y']; ?>)+'px'});
	},function(){
		$(this).css({'background-position':(-1*(parseInt($(this).attr('bgpos')))*<?php echo intval($width); ?>-<?php echo $languagebg_pos['x']; ?>)+'px <?php echo -1*$languagebg_pos['y']; ?>px'});
	});
<?php
	}
?>
</script>
<?php
} else if (count($languagetext)) {
	$main_language_style = array();
	array_push($main_language_style, 'width:'.(intval($width)*count($languagetext)).'px');
	array_push($main_language_style, 'height:'.$height);
	array_push($main_language_style, $right ? 'right:'.$right : 'left:'.$left);
	array_push($main_language_style, $bottom ? 'bottom:'.$bottom : 'top:'.$top);
	
	$item_style= array();
	array_push($item_style, 'width:'.$width);
	array_push($item_style, 'height:'.$height);
	array_push($item_style, 'line-height:'.$height);
	
	$insert_style .= '.main-languages{'.implode(';',$main_language_style).'}';
?>
<div class="main-languages">
	<?php
	for ($i = 0; $i < count($languagetext); $i++) {
		$languagesrc_splits = explode('-',$languagesrc[$i]);
		
		if (!$usebg) {
			$bg_pos = 0;
			if ($i == 0) {
				$bg_pos = 0;
			} else if ($i < count($languagetext)-1) {
				$bg_pos = 1;
			} else {
				$bg_pos = 2;
			}
		} else {
			$bg_pos = $i;
		}
		if ($languagebg && $showbg) {
			if ($i > 0) array_pop($item_style);
			
			$pos_x = (-1*intval($width)*$bg_pos-$languagebg_pos['x']).'px';
			$pos_y = $pagelanguage == $languagesrc_splits[1] ? (-1*intval($height)-$languagebg_pos['y']).'px' : (-1*$languagebg_pos['y']).'px';
			array_push($item_style, 'background:url('.$languagebg.') '.$pos_x.' '.$pos_y);
		}
		$insert_style .= '.main-languages .'.$languagesrc_splits[0].'{'.implode(';',$item_style).'}';
		echo $pagelanguage == $languagesrc_splits[1] 
			? '<span id="'.$i.'_lang" bgpos="'.$bg_pos.'" class="'.$languagesrc_splits[0].' '.$languagesrc_splits[0].'-active">'.($showtext ? $languagetext[$i] : '').'</span>' 
			: '<a id="'.$i.'_lang" bgpos="'.$bg_pos.'" href="'.$root.$languagesrc_splits[0].'" class="'.$languagesrc_splits[0].'">'.($showtext ? $languagetext[$i] : '').'</a>';
	}
	?>
</div>
<?php
	if ($languagebg && $showbg) {
?>
<script type="text/javascript">
	$('#languages-container a').hover(function(){
		$(this).css({'background-position':(-1*(parseInt($(this).attr('bgpos')))*<?php echo intval($width); ?>-<?php echo $languagebg_pos['x']; ?>)+'px '+(-1*parseInt($(this).height())-<?php echo $languagebg_pos['y']; ?>)+'px'});
	},function(){
		$(this).css({'background-position':(-1*(parseInt($(this).attr('bgpos')))*<?php echo intval($width); ?>-<?php echo $languagebg_pos['x']; ?>)+'px <?php echo -1*$languagebg_pos['y']; ?>px'});
	});
</script>
<?php
	}
}
$document->addStyleDeclaration($insert_style);
?>
