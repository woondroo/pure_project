<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php jimport( 'joomla.html.editor' ); $editor =& JFactory::getEditor(); ?>
<?php jimport( 'joomla.html.html' ); ?>
<?php 
jimport ( 'joomla.methods' );
$data =& $this->data; ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'SL_CONFIG' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="fenster">
					<b><?php echo JText::_( 'SL_OPENIN' ); ?>:</b>
				</label>
			</td>
			<td>
        <fieldset class="radio">
        <input type="radio" name="fenster" id="fenster" value="_blank" <?php if ( $this->sitelinkxconfig->fenster == "_blank" ) echo 'checked="checked"'; ?> /><label for="fenster"><?php echo JText::_( 'SL_NEWWIN' ); ?></label><br />
        <input type="radio" name="fenster" id="fenster" value="_self" <?php if ( $this->sitelinkxconfig->fenster == "_self" ) echo 'checked="checked"'; ?> /><label for="fenster"><?php echo JText::_( 'SL_OLDWIN' ); ?></label>			
			  </fieldset>
      </td>
		</tr>
		

		<tr>
			<td width="100" align="right" class="key">
			  <label for="erreichb">
				  <b><?php echo JText::_( 'SL_CHECK' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio"><?php echo JHTML::_( 'select.booleanlist',  'erreichb', null, $this->sitelinkxconfig->erreichb ); ?></fieldset>
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
				<label for="suchm">
					<b><?php echo JText::_( 'SL_SUCHM' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio">
        <input type="radio" name="suchm" id="suchm" value="0" <?php if ( $this->sitelinkxconfig->suchm == "0" ) echo 'checked="checked"' ?> /><label for="suchm"><?php echo JText::_( 'SL_SUCHM_GW' ); ?></label><br />				
				<input type="radio" name="suchm" id="suchm" value="1" <?php if ( $this->sitelinkxconfig->suchm == "1" ) echo 'checked="checked"' ?> /><label for="suchm"><?php echo JText::_( 'SL_SUCHM_TW' ); ?></label>
        </fieldset>			
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			  <label for="hinweis">
				  <b><?php echo JText::_( 'SL_NOFOLLOW' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio"><?php echo JHTML::_( 'select.booleanlist',  'hinweis', null, $this->sitelinkxconfig->hinweis ); ?></fieldset>
			</td>
		</tr>		

		<tr>
			<td width="100" align="right" class="key">
			  <label for="anzahl">
				  <b><?php echo JText::_( 'SL_ANZAHL' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio">
				  <?php //echo JHTML::_( 'select.booleanlist',  'hinweis', null, $this->sitelinkxconfig->hinweis ); ?>
				  <input type="text" name="anzahl" id="anzahl" value="<?php echo $this->sitelinkxconfig->anzahl ; ?>" /> 
				</fieldset>
			</td>
		</tr>

	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_sitelinkx" />
<input type="hidden" name="id" value="1" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="sitelinkxconfig" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
