<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo MulanHTMLUtil::getUrlByAlias('search'); ?>" method="post">
	<div class="main-search <?php echo $moduleclass_sfx ?>">
		<?php
			$output = '<div id="search_key_words_container"><input name="searchword" id="search_key_words" maxlength="'.$maxlength.'" class="inputbox'.$moduleclass_sfx.'" type="text" value="'.$text.'" onblur="if (this.value==\'\') this.value=\''.$text.'\';" onfocus="if (this.value==\''.$text.'\') this.value=\'\';" /></div>';
			$output .= '<input id="search-submit" type="submit" value="'.$button_text.'" class="'.(!$button ? 'opa0 ' : '').'button'.$moduleclass_sfx.'" onclick="this.form.searchword.focus();"/>';
			echo $output;
		?>
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</div>
</form>
