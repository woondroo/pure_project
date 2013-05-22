<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_hr
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
jimport('mulan.mltools');

$titlepreicon = $this->params->get('titlepreicon');
$titlepreicon_pos = MulanToolsUtil::getMaterialPos($titlepreicon);
$titlepreicon = $titlepreicon_pos['img'];
$titleprewidth = $this->params->get('titleprewidth');
$titlepreheight = $this->params->get('titlepreheight');

$quickbtbg = $this->params->get('quickbtbg');
$quickbtbg_pos = MulanToolsUtil::getMaterialPos($quickbtbg);
$quickbtbg = $quickbtbg_pos['img'];
$quickbtwidth = $this->params->get('quickbtwidth');
$quickbtheight = $this->params->get('quickbtheight');
$quickbtshowtext = $this->params->get('quickbtshowtext');

$inputbg = $this->params->get('inputbg');
$inputbg_pos = MulanToolsUtil::getMaterialPos($inputbg);
$inputbg = $inputbg_pos['img'];
$inputwidth = $this->params->get('inputwidth');
$inputheight = $this->params->get('inputheight');
$inputpadding = $this->params->get('inputpadding');
$inputpadding_params = explode(' ',$inputpadding);
$inputwidth = intval($inputwidth)-intval($inputpadding_params[1])-intval($inputpadding_params[3]);
$inputwidth = $inputwidth < 0 ? 0 : $inputwidth;
$inputheight = intval($inputheight)-intval($inputpadding_params[0])-intval($inputpadding_params[2]);
$inputheight = $inputheight < 0 ? 0 : $inputheight;

$submitbg = $this->params->get('submitbg');
$submitbg_pos = MulanToolsUtil::getMaterialPos($submitbg);
$submitbg = $submitbg_pos['img'];
$submitwidth = $this->params->get('submitwidth');
$submitheight = $this->params->get('submitheight');

$base = JURI::base();
$insert_style = '';
if ($titlepreicon) {
	$insert_style .= '.hr-title-pre-icon{width:'.$titleprewidth.';height:'.$titlepreheight.';background:url('.$base.$titlepreicon.') '.(-1*$titlepreicon_pos['x']).'px '.(-1*$titlepreicon_pos['y']).'px;}';
}
$insert_style .= '.hr-rapidsub{width:'.$quickbtwidth.';height:'.$quickbtheight.';line-height:'.$quickbtheight.';'.($quickbtbg ? 'background:url('.$base.$quickbtbg.') '.(-1*$quickbtbg_pos['x']).'px '.(-1*$quickbtbg_pos['y']).'px;' : '').'}' .
				($quickbtbg ? '.hr-rapidsub:hover{background-position:'.(-1*$quickbtbg_pos['x']).'px '.(-1*$quickbtbg_pos['y']-intval($quickbtheight)).'px;}' : '') .
				'.hr-submit{width:'.$submitwidth.';height:'.$submitheight.';line-height:'.$submitheight.';'.($submitbg ? 'background:url('.$base.$submitbg.') '.(-1*$submitbg_pos['x']).'px '.(-1*$submitbg_pos['y']).'px;' : '').'}'.($submitbg ? '.hr-submit:hover{background-position:'.(-1*$submitbg_pos['x']).'px '.(-1*$submitbg_pos['y']-intval($submitheight)).'px;}' : '') .
				'.quick input.input{width:'.$inputwidth.'px;height:'.$inputheight.'px;padding:'.implode('px ',$inputpadding_params).'px;'.($inputbg ? 'background:url('.$base.$inputbg.') '.(-1*$inputbg_pos['x']).'px '.(-1*$inputbg_pos['y']).'px;' : '').'}';
$document = JFactory::getDocument();
if ($insert_style) $document->addStyleDeclaration($insert_style);
?>
<div id="allhr">
	<?php
	if (count($this->items)) {
		foreach($this->items as $item){
	?>
	<div class="item">
		<div class="hr-title">
			<span>
				<font class="hr-title-pre-icon"></font>
				<?php echo $item->title; ?>
			</span>
			<div class="hr-title-bottom-line"></div>
		</div>
		<div class="hr-content">
			<div class="hr-content-pre">工作内容：</div>
			<div class="hr-content-text"><?php echo $item->desc; ?></div>
			<div class="clr"></div>
		</div>
		<div class="hr-content">
			<div class="hr-content-pre">工作要求：</div>
			<div class="hr-content-text"><?php echo $item->require; ?></div>
			<div class="clr"></div>
			<a href="javascript:void(0);" class="hr-rapidsub left" onclick="nowapp(this);" job="<?php echo $item->title; ?>"><?php echo $quickbtshowtext ? '快速申请' : ''; ?></a>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="none quick"></div>
	</div>
	<?php
		}
	} else {
		echo '<div class="no-list">暂无数据！</div>';
	}
	?>
	<div class="content-bottom-line"></div>
	<div id="quick" class="none">
		<div class="ddt">
			<div class="hr-quick-title"><span>快速申请</span></div>
			<form method="post" class="quickform" onsubmit="return checkQuickForm(this);" enctype="multipart/form-data">
				<table>
					<tr>
						<td align="right"><span class="hr-ele-title">职 位：</span></td>
						<td><input class="input job" readonly="readonly" name="job"/></td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td align="right"><span class="hr-ele-title">姓 名：</span></td>
						<td><input class="input name required-input" name="name"/></td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td align="right"><span class="hr-ele-title">性 别：</span></td>
						<td><input type="radio" checked="checked" id="sex1" name="sex" value="1"/> <label for="sex1">先生</label> <input type="radio" id="sex2" name="sex" value="0"/> <label for="sex2">小姐</label> </td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td align="right"><span class="hr-ele-title">电 话：</span></td>
						<td><input class="input tel required-input" name="tel"/></td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td align="right"><span class="hr-ele-title">邮 箱：</span></td>
						<td><input class="input email required-input" name="email"/></td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td height="40" align="right"><span class="hr-ele-title">附 件：</span></td>
						<td>
							<input type="file" class="fujian hrfile required-input" name="fujian"/>						
						</td>
						<td><span class="red pl9">*</span></td>
					</tr>
					<tr>
						<td height="30" align="right"><span class="hr-ele-title"></span></td>
						<td valign="top"><span class="hr-file-text">(最大 5M,仅限 PDF, WORD 文档)</span></td>
						<td></td>
					</tr>
					<tr>
						<td align="right"></td>
						<td>
							<input type="hidden" name="task" value="savehr"/>
							<input type="hidden" name="url" value="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>"/>	
							<input class="hr-submit" type="submit" value="提交申请"/>
						</td>
						<td></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
	<div class="clr"></div>
</div>

<script>
function nowapp(the){
	if($(the).parents('.item:first').find('.quick').hasClass('none')){		
		$('.quick').addClass('none');
		var html = $('#quick');
		/* if (html.find('.hr-quick-title') != undefined) { html.find('.hr-quick-title').find('span').html($(the).attr('job')); } */
		$(the).parents('.item:first').find('.quick').removeClass('none').html(html.html()).find('.job:first').val($(the).attr('job'));
	}else{
		$(the).parents('.item:first').find('.quick').addClass('none').html('');
	}
}
</script>