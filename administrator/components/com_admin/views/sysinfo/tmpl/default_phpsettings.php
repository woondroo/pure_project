<?php
/**
 * @version		$Id: default_phpsettings.php 22030 2011-09-02 12:41:22Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_ADMIN_REQUIRED_PHP_SETTINGS'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="250">
					<?php echo JText::_('COM_ADMIN_SETTING'); ?>
				</th>
				<th width="120">
					<?php echo JText::_('COM_ADMIN_RECOMMEND_VALUE'); ?>
				</th>
				<th width="120">
					<?php echo JText::_('COM_ADMIN_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">&#160;
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_PHP_VERSION_524'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['php_version'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['php_version']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_MYSQL_CONNECT'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['mysql_connect'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['mysql_connect']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_MBSTRING_LANGUAGE'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['mbstring.language'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['mbstring.language']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_MBSTRING_FUNCOVERLOAD'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['mbstring.func_overload'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['mbstring.func_overload']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_INI_PARSER'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['ini_parser'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['ini_parser']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_JSON_SUPPORT'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['json_support'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['json_support']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_CONFIGURATION'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['configuration'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['configuration']); ?>
				</td>
			</tr>
			
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_FILE_UPLOADS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['file_uploads'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['file_uploads']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_XML_ENABLED'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['xml'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['xml']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_ZLIB_ENABLED'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['zlib'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['zlib']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_ZIP_ENABLED'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['zip'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['zip']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_MBSTRING_ENABLED'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['mbstring'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['mbstring']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_ICONV_AVAILABLE'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.set', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['iconv'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.set', $this->php_settings['iconv']); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_ADMIN_RELEVANT_PHP_SETTINGS'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="250">
					<?php echo JText::_('COM_ADMIN_SETTING'); ?>
				</th>
				<th width="120">
					<?php echo JText::_('COM_ADMIN_RECOMMEND_VALUE'); ?>
				</th>
				<th width="120">
					<?php echo JText::_('COM_ADMIN_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">&#160;
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_SAFE_MODE'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['safe_mode'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['safe_mode']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_OPEN_BASEDIR'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.string', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['open_basedir'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.string', $this->php_settings['open_basedir']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_DISPLAY_ERRORS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['display_errors'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['display_errors']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_SHORT_OPEN_TAGS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 1); ?>
				</td>
				<td style="color:<?php echo $this->php_settings['short_open_tag'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['short_open_tag']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_MAGIC_QUOTES'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['magic_quotes_gpc'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['magic_quotes_gpc']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_REGISTER_GLOBALS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['register_globals'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['register_globals']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_DISABLED_FUNCTIONS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.string', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['disable_functions'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.string', $this->php_settings['disable_functions']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_OUTPUT_BUFFERING'); ?>
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.boolean', 0); ?>
				</td>
				<td style="color:<?php echo !$this->php_settings['output_buffering'] ? 'green' : 'red'; ?>">
					<?php echo JHtml::_('phpsetting.boolean', $this->php_settings['output_buffering']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_SESSION_SAVE_PATH'); ?>
				</td>
				<td>
					
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.string', $this->php_settings['session.save_path']); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMIN_SESSION_AUTO_START'); ?>
				</td>
				<td>
					
				</td>
				<td>
					<?php echo JHtml::_('phpsetting.integer', $this->php_settings['session.auto_start']); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
