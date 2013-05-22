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
 * Form Field class for the Joomla Platform.
 * Supports a one line backup field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldBackupsite extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Backupsite';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 * 
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'.$class.$size.$disabled.$readonly.'/>' .
				'<a class="to_backup" href="javascript:;" onclick="executeBackup()">开始备份</a>' .
				'<script type="text/javascript">' .
					'var is_busy = false;' .
					'function executeBackup(){' .
						'if (is_busy == true) {alert("数据库操作正在进行，请耐心等待...");return;}' .
						'is_busy = true;' .
						'$(".to_backup").html("正在备份...").addClass("active");' .
						'$.ajax({' .
							'url:"index.php?option=com_backup&task=backup.backupDB",' .
							'success:function(data){' .
								'data = data.trim();' .
								'if (data!="false") {' .
									'$("#'.$this->id.'").val(data);' .
									'$(".to_backup").html("备份成功！");' .
								'} else {' .
									'is_busy = false;' .
									'$(".to_backup").html("备份失败，点击重新备份！").removeClass("active");' .
								'}' .
							'}' .
						'});' .
					'}' .
				'</script>';
	}
}