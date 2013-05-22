<?php
/**
 * @version		$Id: default.php 20338 2011-01-18 08:44:38Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_breadcrumbs
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('mulan.mltools');
?>

<div class="breadcrumbs<?php echo $moduleclass_sfx; ?>">
<?php
echo '<div class="bread-text-container">';
for ($i = 0; $i < $count; $i ++) :
	// If not the last item in the breadcrumbs add the separator
	if ($i < $count -1) {
		if (!empty($list[$i]->link)) {
			echo '<a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a>';
		} else {
			echo '<span>';
			echo $list[$i]->name;
			echo '</span>';
		}
		if($i < $count -2){
			echo ' '.$separator.' ';
		}
	}  else if ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
		if($i > 0){
			echo ' '.$separator.' ';
		}
		 echo '<span>';
		echo MulanStringUtil::substr_zh($list[$i]->name,15,'...');
		  echo '</span>';
	}
endfor;
echo '</div>';

/**
 * 2012-04-07 wengebin Edit!
 */
$iconwidth = $params->get('iconwidth');
$iconheight = $params->get('iconheight');
$iconm = $params->get('iconm');
$iconm_params = explode(' ',$iconm);
$iconm = implode('px ',$iconm_params).'px';
$textm = $params->get('textm');
$iconimg = $params->get('iconimg');
$iconimg_pos = MulanToolsUtil::getMaterialPos($iconimg);
$iconimg = $iconimg_pos['img'];

$document = JFactory::getDocument();
$insert_style = '.breadcrumbs .showHere{background:url('.JURI::base().$iconimg.') '.(-1*$iconimg_pos['x']).'px '.(-1*$iconimg_pos['y']).'px no-repeat;width:'.$iconwidth.';height:'.$iconheight.';margin:'.$iconm.';}.breadcrumbs .showHereText{margin-right:'.$textm.';}';
$document->addStyleDeclaration($insert_style);

if ($params->get('showIcon', 1)) echo '<span class="showHere"></span>';
if ($params->get('showHere', 1)) echo '<span class="showHereText">' .JText::_('MOD_BREADCRUMBS_HERE').'</span>';
?>
</div>
