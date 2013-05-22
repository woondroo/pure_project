<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');

?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset class="loginform">
		<label id="mod-login-username-lbl" for="mod-login-username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
		<input name="username" id="mod-login-username" type="text" class="inputbox" size="15" />

		<label id="mod-login-password-lbl" for="mod-login-password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
		<input name="passwd" id="mod-login-password" type="password" class="inputbox" size="15" />
		<label id="mod-login-checkcode-lbl" for="mod-login-checkcode"><?php echo JText::_('JGLOBAL_CHECKCODE'); ?></label>
		<input name="code" id="mod-login-checkcode" type="text" class="inputbox" maxlength="4"/>
		<img title="点击刷新验证码" id="codeimg" class="check_code inputbox" onclick="changeCode('<?php echo JURI::base(true).'/'; ?>',this)" src="index.php?option=com_login&task=displaycaptcha"/>

		<label id="mod-login-language-lbl" for="lang"><?php echo JText::_('MOD_LOGIN_LANGUAGE'); ?></label>
		<?php echo $langs; ?>
		
		<div class="button-holder">
			<div class="button1">
				<div class="next">
					<a href="#" onclick="document.getElementById('form-login').submit();">
						<?php echo JText::_('MOD_LOGIN_LOGIN'); ?></a>
				</div>
			</div>
		</div>

		<div class="clr"></div>
		<input type="submit" class="hidebtn" value="<?php echo JText::_( 'MOD_LOGIN_LOGIN' ); ?>" />
		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
<script type="text/javascript">
// 请求刷新验证码
function changeCode(url,ele){
	var src=url+'index.php?option=com_login&task=displaycaptcha&ran='+(new Date().getTime().toString(36))+'';
	var checkImage=$(ele).parent().find('.check_code').attr('class')==undefined?$(ele).parent().parent().find('.check_code'):$(ele).parent().find('.check_code');
	if($(checkImage).attr('class')!=undefined){
		$(checkImage).attr('src',src);
	}
}
</script>