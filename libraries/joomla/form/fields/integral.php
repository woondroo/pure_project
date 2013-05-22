<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * 2012-03-02 wengebin Add
 * 
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldIntegral extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Integral';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribue to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true).'/media/system/js/modal.js');
		$document->addStyleSheet(JURI::root(true).'/media/system/css/modal.css');
		
		$class = $this->element['class'] ? $this->element['class'] : '';
		
		$this->id = $this->id ? $this->id : 'integral_input_'.str_replace(array('jform','[',']'),'',$this->name);
		
		return '<a class="to_add_integral" href="#to-'.str_replace(array('jform','[',']'),'',$this->name).'" rel="boxed">'.JText::_('COM_USERS_USER_FIELD_ADDINTEGRAL_LABEL').'</a>
				<div class="to_add_integral_area">
					<div id="to-'.str_replace(array('jform','[',']'),'',$this->name).'">
						'.JText::_('COM_USERS_USER_FIELD_ADDINTEGRAL_LABEL').'：<input type="text" id="'.$this->id.'" class="'.$this->id.'" name="'.$this->name.'" class="inputbox'.($class ? ' '.$class.'-input' : '').'" size="'.$this->element['size'].'" value="'.$this->value.'" />
						<a href="#" onclick="javascript:addintegral_submit();" class="custome-button-red">'.JText::_('JTOOLBAR_APPLY').'</a><br/>
						'.JText::_('COM_USERS_USER_FIELD_INTEGRALREASON_LABEL').'：<input type="text" id="integral-reason-input" class="integral-reason-input" name="jform[integral_reason]" class="inputbox" size="'.$this->element['size'].'" value="管理员手动增加积分" />
					</div>
				</div>
				<script type="text/javascript">
					SqueezeBox.initialize({});
					SqueezeBox.assign($$(\'a[rel=boxed][href^=#]\'), {
						size:{x:310,y:60}
					});
					function addintegral_submit(){
						$("#'.$this->id.'").val($($(".'.$this->id.'")[1]).val());
						$("#integral-reason-input").val($($(".integral-reason-input")[1]).val());
						Joomla.submitbutton(\'user.apply\');
					}
				</script>';
	}
}
