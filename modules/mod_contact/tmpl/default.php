<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
jimport('mulan.mlhtml');
jimport('mulan.mltools');

$app = JFactory::getApplication();
$pagelanguage = $app->getCfg('pagelanguage');
$site_model = $app->getCfg('site_model');
if ($pagelanguage == 1) {
	$labels = array('username'=>'姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名'
					,'sex'=>'称&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;谓'
					,'mr'=>'先生'
					,'miss'=>'小姐'
					,'ms'=>'女士'
					,'mrs'=>'夫人'
					,'phone'=>'电&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;话'
					,'qq'=>'MSN/QQ'
					,'email'=>'邮&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;箱'
					,'company'=>'公&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;司'
					,'company_p'=>'预&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;算'
					,'title'=>'主&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;题'
					,'demand'=>'留言内容'
					,'submit'=>'提交'
					,'reset'=>'重填'
					,'titleVal'=>'来自留言中心的消息'
					,'1000'=>'1000以下'
					,'2000'=>'1000-2000'
					,'5000'=>'2000-5000'
					,'5000+'=>'5000以上');
} else {
	$labels = array('username'=>'Your name:'
					,'sex'=>'Sex:'
					,'mr'=>'Mr'
					,'miss'=>'Miss'
					,'ms'=>'Ms'
					,'mrs'=>'Mrs'
					,'phone'=>'Contact Phone:'
					,'qq'=>'MSN/QQ:'
					,'email'=>'E-mail:'
					,'company'=>'Company Name:'
					,'company_p'=>'Budget:'
					,'title'=>'Title:'
					,'demand'=>'Content:'
					,'submit'=>'Submit'
					,'reset'=>'Reset'
					,'titleVal'=>'From the message center\'s message'
					,'1000'=>'Less than 1000'
					,'2000'=>'1000-2000'
					,'5000'=>'2000-5000'
					,'5000+'=>'More than 5000');
}

$tipicon = $params->get('tipicon');
$tipicon_pos = MulanToolsUtil::getMaterialPos($tipicon);
$tipicon = $tipicon_pos['img'];

$tipwidth = $params->get('tipwidth');
$tipheight = $params->get('tipheight');

$inputbg = $params->get('inputbg');
$inputbg_pos = MulanToolsUtil::getMaterialPos($inputbg);
$inputbg = $inputbg_pos['img'];
$inputwidth = $params->get('inputwidth');
$inputheight = $params->get('inputheight');
$inputpadding = $params->get('inputpadding');
$inputpadding_params = explode(' ',$inputpadding);
$inputwidth = intval($inputwidth)-intval($inputpadding_params[1])-intval($inputpadding_params[3]);
$inputwidth = $inputwidth < 0 ? 0 : $inputwidth;
$inputheight = intval($inputheight)-intval($inputpadding_params[0])-intval($inputpadding_params[2]);
$inputheight = $inputheight < 0 ? 0 : $inputheight;

$textareabg = $params->get('textareabg');
$textareabg_pos = MulanToolsUtil::getMaterialPos($textareabg);
$textareabg = $textareabg_pos['img'];
$textareawidth = $params->get('textareawidth');
$textareaheight = $params->get('textareaheight');
$textareapadding = $params->get('textareapadding');
$textareapadding_params = explode(' ',$textareapadding);
$textareawidth = intval($textareawidth)-intval($textareapadding_params[1])-intval($textareapadding_params[3]);
$textareawidth = $textareawidth < 0 ? 0 : $textareawidth;
$textareaheight = intval($textareaheight)-intval($textareapadding_params[0])-intval($textareapadding_params[2]);
$textareaheight = $textareaheight < 0 ? 0 : $textareaheight;

$btbg = $params->get('btbg');
$btbg_pos = MulanToolsUtil::getMaterialPos($btbg);
$btbg = $btbg_pos['img'];
$btwidth = $params->get('btwidth');
$btheight = $params->get('btheight');

$base = JURI::base();
$insert_style = '#leavemessage .input{width:'.$inputwidth.'px;height:'.$inputheight.'px;padding:'.implode('px ',$inputpadding_params).'px;'.($inputbg ? 'background:url('.$base.$inputbg.') '.(-1*$inputbg_pos['x']).'px '.(-1*$inputbg_pos['y']).'px;' : '').'}' .
				'#leavemessage textarea{width:'.$textareawidth.'px;height:'.$textareaheight.'px;padding:'.implode('px ',$textareapadding_params).'px;'.($textareabg ? 'background:url('.$base.$textareabg.') '.(-1*$textareabg_pos['x']).'px '.(-1*$textareabg_pos['y']).'px;' : '').'}' .
				'#l-submit,#l-reset{width:'.$btwidth.';height:'.$btheight.';line-height:'.$btheight.';'.($btbg ? 'background:url('.$base.$btbg.') '.(-1*$btbg_pos['x']).'px '.(-1*$btbg_pos['y']).'px;' : '').'}'.($btbg ? '#l-submit:hover,#l-reset:hover{background-position:'.(-1*$btbg_pos['x']).'px '.(-1*$btbg_pos['y']-intval($btheight)).'px;}' : '');
if ($tipicon) {
	$insert_style .= '.contact-tip-icon{width:'.$tipwidth.';height:'.$tipheight.';background:url('.$base.$tipicon.') '.(-1*$tipicon_pos['x']).'px '.(-1*$tipicon_pos['y']).'px;}';
}
$document = JFactory::getDocument();
$document->addStyleDeclaration($insert_style);
?>
<div class="contact-tip"><?php echo $tipicon ? '<div class="contact-tip-icon"></div>' : ''; ?><span><font>留言中心</font>&nbsp;&nbsp;请把您的疑问与建议在线提交给我们，我们将会有专人进行跟进，谢谢！</span><div class="clr"></div></div>
<div class="leavemessage-container">
	<form id="leavemessage" name="leavemessage" method="post" action="<?php echo MulanHtmlUtil::getUrlByAlias('feedback').'?task=leavemessage';?>">
		<table>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['username']; ?></span></td>
				<td><input type="text" name="username" class="input required-input" /></td>
				<td><span class="red pl9">*</span></td>
			</tr>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['sex']; ?></span></td>
				<td>
					<input type="radio" name="sex" class="radio-input" value="mr" id="mr" checked /><label for="mr"><?php echo $labels['mr']; ?></label>
					<input type="radio" name="sex" class="radio-input" value="miss" id="miss" /><label for="miss"><?php echo $labels['miss']; ?></label>
					<input type="radio" name="sex" class="radio-input" value="ms" id="ms" /><label for="ms"><?php echo $labels['ms']; ?></label>
					<input type="radio" name="sex" class="radio-input" value="mrs" id="mrs" /><label for="mrs"><?php echo $labels['mrs']; ?></label>
				</td>
				<td></td>
			</tr>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['phone']; ?></span></td>
				<td><input type="text" name="phone" class="input" /></td>
				<td></td>
			</tr>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['qq']; ?></span></td>
				<td><input type="text" name="qq" class="input" /></td>
				<td></td>
			</tr>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['email']; ?></span></td>
				<td><input type="text" name="email" class="input required-input" id="r-email"/></td>
				<td><span class="red pl9">*</span></td>
			</tr>
			<?php
			if ($site_model) {
			?>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['company_p']; ?></span></td>
				<td>
					<input type="radio" name="company" class="radio-input" value="1000-" id="1000" checked /><label for="1000"><?php echo $labels['1000']; ?></label>
					<input type="radio" name="company" class="radio-input" value="1000-2000" id="2000" /><label for="2000"><?php echo $labels['2000']; ?></label>
					<input type="radio" name="company" class="radio-input" value="2000-5000" id="5000" /><label for="5000"><?php echo $labels['5000']; ?></label>
					<input type="radio" name="company" class="radio-input" value="5000+" id="5000+" /><label for="5000+"><?php echo $labels['5000+']; ?></label>
				</td>
				<td></td>
			</tr>
			<?php
			} else {
			?>
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['company']; ?></span></td>
				<td><input type="text" name="company" class="input" /></td>
				<td></td>
			</tr>
			<?php
			}
			/*
			<tr>
				<td align="right"><span class="leavemessage-title"><?php echo $labels['title']; ?></span></td>
				<td><input type="text" name="title" class="input required-input" value="" /></td>
				<td><span class="red pl9">*</span></td>
			</tr>
			 */ ?>
			<tr>
				<td valign="top" align="right"><span class="leavemessage-title"><?php echo $labels['demand']; ?></span></td>
				<td><textarea name="demand" id="demand" class="left" class="input required-input" ></textarea></td>
				<td valign="top"><span class="red pl9">*</span></td>
			</tr>
			<tr>
				<td align="right"></td>
				<td>
					<a href="javascript:;" id="l-submit"><?php echo $labels['submit']; ?></a>
					<a href="javascript:;" id="l-reset"><?php echo $labels['reset']; ?></a>
					<input type="hidden" name="scope" value="feedback"/>
					<input type="hidden" name="title" value="<?php echo $labels['titleVal']; ?>" />
				</td>
				<td></td>
			</tr>
		</table>
	</form>
</div>
<div class="content-bottom-line"></div>
