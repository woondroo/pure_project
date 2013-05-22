<?php
/**
 * @version		$Id: default.php 21726 2011-07-02 05:46:46Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_inquirybox
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('mulan.mlstring');
jimport('mulan.mlhtml');
jimport('mulan.mldb');

$base = JURI::base();
$session = JFactory::getSession();
$cart = $session->get('cart');
if (count($cart)) {
	$cart_pros = array();
	foreach ($cart as $ck=>$c) {
		$cart_pros[] = $ck;
	}
}
$os = MulanDBUtil::getObjectListBySql('select id,title,catid,image from #__product where id in ('.(is_array($cart_pros) ? implode(',',$cart_pros) : '').')');
$itemLink = 'index.php?option=com_product&view=product&Itemid=512&limitstart=0';
?>
<div class="inquiry-title"><span>Inquiry Products</span></div>
<div class="inquiry-pros">
	<?php
	if (count($os)) {
		foreach ($os as $key=>$o) {
		?>
		<div<?php echo $key%3 == 0 ? ' style="margin-left:0;"' : '' ?> id="product-item-<?php echo $o->id; ?>" class='product-item'>
			<a target="_blank" href="<?php echo $itemLink.'&id='.$o->catid.'&pid='.$o->id; ?>" class="product-img-area">
				<div class="product-img" style="background:url(<?php echo $base.$o->image; ?>);"></div>
			</a>
			<a target="_blank" href="<?php echo $itemLink.'&id='.$o->catid.'&pid='.$o->id; ?>" class='title' title='<?php echo $o->title?>'><?php echo MulanStringUtil::substr_zh($o->title,27,'...');?></a>
			<a class="remove-pro" href="javascript:removeCart('<?php echo $o->id; ?>');"></a>
		</div>
		<?php
		}
		echo '<div class="clr"></div>';
	} else {
		echo '<p class="inquiry-none">Your cart is empty! If you want to add product,please <a href="'.MulanHTMLUtil::getUrlByAlias('products').'">Go to products center!</a></p>';
	}
	?>
</div>
<div class="inquiry-title"><span>How can we help you?</span></div>
<div class="inquiry-form">
	<form action="<?php echo MulanHtmlUtil::getUrlByAlias('feedback').'?task=leavemessage'; ?>" method="post" name="leavemessage">
		<table width="100%" class="contact_email">
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr class="contact_line">
				<td colspan="2">
					<label for="contact_subject" >
						Subject : <span>*</span>
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<input type="text" name="title" id="r-title" size="30" class="inputbox required-input" value="" />
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2" >
					<label id="contact_textmsg" for="contact_text">
						Message : <span>*</span>
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<input type="text" name="demand" id="r-demand" size="30" class="inputbox required-input" value="" />
					<p class="color9 size11">As much as information you provide, we will reply by more detail.</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="inquiry-info-title">Your Contact Info:</b>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2" >
					<label for="contact_name">
						Your Name : <span>*</span>
					</label>
				</td>
			</tr>
		    <tr class="contact_line">
				<td colspan="2">
					<select id="r-sex" name="sex" class="left">
						<option value="mr">Mr</option>
						<option value="miss">Miss</option>
						<option value="ms">Ms</option>
						<option value="mrs">Mrs</option>
					</select>
					<input style="width:392px;margin-left:9px;" type="text" name="username" id="r-username" size="30" class="inputbox required-input" value="" />
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2" >
					<label for="contact_copname" >
						Company Name :
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<input type="text" id="r-company" name="company" size="30" class="inputbox" value="" />
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<label id="contact_emailmsg" for="contact_email">
						E-mail : <span>*</span>
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<input type="text" id="r-email" name="email" size="30" value="" class="inputbox required-input" maxlength="100" />
				</td>
			</tr>
			<tr class="contact_line">
				<td >
					<label for="contact_tel" >
						Phone Number : <span>*</span>
					</label>
				</td>
				<td >
					<label for="contact_fax" >
						Fax Number :
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td>
					<input type="text" name="phone" id="r-phone" size="30" class="inputbox1 required-input" value="" />
				</td>
				<td>
					<input type="text" name="fax" id="r-fax" size="30" class="inputbox1" value="" />
				</td>
			</tr>
			<tr class="contact_line">
				<td valign="top" colspan="2">
					<label for="contact_address" >
						Country : <span>*</span>
					</label>
				</td>
			</tr>
			<tr class="contact_line">
				<td colspan="2">
					<input type="text" name="country" id="r-country" size="30" class="inputbox required-input" value="" />
					<p class="color9 size11">This is only use to reply your inquiry.</p>
					<p class="color9 size11">Your information is kept 100% confidential.</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<a href="javascript:;" id="bk-l-submit"></a>
					<a href="javascript:;" id="bk-l-reset"></a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				&nbsp;
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_blank" />
		<input type="hidden" name="task" value="leavemessage" />
		<input type="hidden" name="scope" value="inquiry" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
