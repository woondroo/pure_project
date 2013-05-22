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
class JFormFieldCheckcode extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Checkcode';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribue to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		$class = $this->element['class'] ? $this->element['class'] : '';
		
		return '<input name="'.$this->name.'" id="'.$this->id.'" type="text" class="inputbox'.($class ? ' '.$class.'-input' : '').'" maxlength="4"/><img title="点击刷新验证码" id="codeimg" class="check_code inputbox'.($class ? ' '.$class : '').'" onclick="changeCode(\''.JURI::base(true).'\',this)" src="index.php?option=com_users&task=displaycaptcha"/>';
	}
}
