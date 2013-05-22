<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// Import the com_menus helper.
require_once realpath(JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php');

/**
 * Supports an HTML select list of menus
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldMenu extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Menu';

	/**
	 * Method to get the list of menus for the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), JHtml::_('menu.menus'));
		$script = '$(document).ready(function(){
			$("#jform_params_menutype").change(function(){
				change_menutype();
			});
			function change_menutype() {
				if ($("#jform_params_menutype")[0] != undefined) {
					if ($("#jform_params_menutype").val() != "mainmenu") {
						var lis = $("#basic-options").parent().find(".pane-slider ul.adminformlist li");
						if (lis.length <= 0) return;
						for (var i = 0; i < lis.length; i++) {
							if (i > 12) {
								$(lis[i]).hide();
							}
						}
					} else {
						$(".pane-slider ul.adminformlist li").show();
					}
				}
			}
			change_menutype();
		});';
		echo '<script type="text/javascript">'.$script.'</script>';
		return $options;
	}
}