<?php
/**
 * @version		$Id: default_form.php 21504 2011-06-10 06:21:35Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>

<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post">

	<fieldset class="word">
		<label for="search-searchword">
			<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>
		</label>
		<input type="text" name="searchword" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
		<button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_('COM_SEARCH_SEARCH');?></button>
		<input type="hidden" name="task" value="search" />
	</fieldset>

	<div class="phrases-box">
		<?php echo $this->lists['searchphrase']; ?>
	</div>
	
	<?php if ($this->params->get('search_areas', 1)) : ?>
	<div class="querycoms-box">
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = (is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active'])) || $this->searchareas['active'] == NULL ? 'checked="checked"' : '';
		?>
		<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> />
		<label for="area-<?php echo $val;?>"><span class="querycoms-tip">&nbsp;</span><span class="querycoms-text"><?php echo JText::_($txt); ?></span></label>
		<?php endforeach; ?>
		<?php echo $this->lists['ordering'];?>
	</div>
	<?php endif; ?>
	
	<?php if ($this->total > 0) : ?>
	<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php endif; ?>
	
	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)):?>
		<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total);?></p>
		<?php endif;?>
	</div>
</form>
<script type="text/javascript">
	$('.querycoms-box label').click(function(){
		var this_ele = this;
		setTimeout(changeCOMS,10);
	});
	function changeCOMS(){
		$('.querycoms-box .querycoms-tip').removeClass('active');
		$('.querycoms-box input').each(function(){
			if ($(this)[0].checked) {
				$('[for='+$(this).attr('id')+']').find('.querycoms-tip').addClass('active');
			}
		});
	}
	changeCOMS();
	
	$('.phrases-box .radiobtn').click(function(){
		setTimeout(changeBG,10);
	});
	function changeBG(){
		$('.phrases-box .radiobtn').removeClass('active');
		$('.phrases-box input').each(function(){
			if ($(this)[0].checked) $('[for='+$(this).attr('id')+']').addClass('active');
		});
	}
	changeBG();
</script>