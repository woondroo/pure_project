<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php jimport( 'joomla.html.editor' ); $editor =& JFactory::getEditor(); ?>
<?php jimport( 'joomla.html.html' ); 
  jimport ( 'joomla.methods' );
  require_once( JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php' );
?>
<?php $data =& $this->data; ?>
<script type="text/javascript">

	Joomla.submitbutton = function (pressbutton){
		var form = document.adminForm;
	
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
	
		// do field validation
		if (form.wort.value.length < "1"){
			alert( "<?php echo JText::_( 'SL_NO_SHORT', true ); ?>" );
		} else if (form.ersatz.value == ""){
			alert( "<?php echo JText::_( 'SL_ENTER_URL', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}

function auswaehlen (select) {
  var wert = select.options[select.options.selectedIndex].value;
  document.adminForm.ersatz.value = wert;
  document.adminForm.fenster0.checked = "";
  document.adminForm.fenster1.checked = "checked";
  if (wert == "leer") {
    select.form.reset();
    return;
  }
}

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'SL_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="200" align="right" class="key">
				<label for="wort">
					<b><?php echo JText::_( 'SL_LABEL' ); ?>:</b>
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="wort" id="wort" size="60" maxlength="250" value="<?php echo $this->sitelinkx->wort;?>" />
			</td>
		</tr>

		<tr>
			<td width="200" align="right" class="key">
				<label for="ersatz">
					<b><?php echo JText::_( 'SL_REPLACE_WITH_URL' ); ?>:</b>
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="ersatz" id="ersatz" size="60" maxlength="250" value="<?php echo $this->sitelinkx->ersatz;?>" />
			</td>
      <td>

          <p><select size="1" name="Auswahl" onchange="auswaehlen(this)" width="100">
          <option value="leer" selected="selected">-- <?php echo JText::_( 'SL_ARTIKELWAHL' ); ?> --</option>
          
          <?php
            $db =& JFactory::getDBO();
            $query = "SELECT id,catid,alias,sectionid,title FROM `#__content` ORDER BY id";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            

            for ($x=0, $n = count($rows); $x < $n; $x++) {

               $artslug = $rows[$x]->id . ':' . $rows[$x]->alias;

               $cdb =& JFactory::getDBO();
               $cquery = "SELECT * FROM `#__categories` WHERE id = " . $rows[$x]->catid;
               $cdb->setQuery($cquery);
               $crows = $cdb->loadObjectList();
            
               if ($rows[$x]->catid == '0') { $katslug = '';} else {$katslug = $crows[0]->id . ':' . $crows[0]->alias;}
               $sektionid = $rows[$x]->sectionid;
               $link = JRoute::_(ContentHelperRoute::getArticleRoute($artslug, $katslug, $sektionid));
               $link1 = strstr($link, 'index.php');
               echo '<option value="'.$link1.'" >'.$rows[$x]->title.'</option>';
            }
          ?>
          <option value="leer">-- <?php echo JText::_( 'SL_MENUEWAHL' ); ?> --</option>
          <?php
            $dbd =& JFactory::getDBO();
            $queryd = "SELECT id,title,link FROM `#__menu` ORDER BY id";
            $dbd->setQuery($queryd);
            $rowsd = $dbd->loadObjectList();
            for ($x=0, $n = count($rowsd); $x < $n; $x++ ) {
            	 $link = $rowsd[$x]->link;
            	 echo '<option value="'.$link.'" >'.$rowsd[$x]->title.'</option>'; 
            }
          ?>
          
          </select></p>
          
      </td>
		</tr>


		<tr>
			<td width="200" align="right" class="key">
				<label for="schlagwort">
					<b><?php echo JText::_( 'SL_TAG' ); ?>:</b>
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="schlagwort" id="schlagwort" size="60" maxlength="250" value="<?php echo $this->sitelinkx->schlagwort;?>" />
			</td>
		</tr>
		
		
		<tr>
			<td width="200" align="right" class="key">
				<label for="begpub">
					<b><?php echo JText::_( 'SL_BEGPUB' ); ?>:</b>
				</label>
			</td>
			<td>
				<?php
				echo JHTML::calendar($this->sitelinkx->begpub,'begpub','begpub', '%Y-%m-%d %H:%M:%S', 'size="60"' );
				?>
			</td>
		</tr>		
		<tr>
			<td width="200" align="right" class="key">
				<label for="endpub">
					<b><?php echo JText::_( 'SL_ENDPUB' ); ?>:</b>
				</label>
			</td>
			<td>
				<?php
				$einf = '';
        if ($this->sitelinkx->endpub == '0000-00-00 00:00:00') $this->sitelinkx->endpub = JText::_( 'SL_NEVER' );
				echo JHTML::calendar($this->sitelinkx->endpub,'endpub','endpub', '%Y-%m-%d %H:%M:%S', 'size="60"' );
        $this->sitelinkx->endpub = '0000-00-00 00:00:00' ;
        ?>				
			</td>
		</tr>		

		<tr>
			<td width="200" align="right" class="key">
				<label for="fenster">
					<b><?php echo JText::_( 'SL_OPENIN' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio">
        <input type="radio" name="fenster" id="fenster0" value="_blank" <?php if ( $this->sitelinkx->fenster == "_blank" ) echo 'checked="checked"' ?> /><label for="fenster"><?php echo JText::_( 'SL_NEWWIN' ); ?></label><br />				
				<input type="radio" name="fenster" id="fenster1" value="_self" <?php if ( $this->sitelinkx->fenster == "_self" ) echo 'checked="checked"' ?> /><label for="fenster"><?php echo JText::_( 'SL_OLDWIN' ); ?></label>
        </fieldset>			
			</td>
		</tr>
		

		<tr>
			<td width="200" align="right" class="key">
			  <label for="published">
				  <b><?php echo JText::_( 'SL_CHECK' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio"><?php echo JHTML::_( 'select.booleanlist',  'published', null, $this->sitelinkx->published ); ?></fieldset>
			</td>
		</tr>		

		<tr>
			<td width="200" align="right" class="key">
				<label for="suchm">
					<b><?php echo JText::_( 'SL_SUCHM' ); ?>:</b>
				</label>
			</td>
			<td>
				<fieldset class="radio">
        <input type="radio" name="suchm" id="suchm" value="0" <?php if ( $this->sitelinkx->suchm == "0" ) echo 'checked="checked"' ?> /><label for="suchm"><?php echo JText::_( 'SL_SUCHM_GW' ); ?></label><br />				
				<input type="radio" name="suchm" id="suchm" value="1" <?php if ( $this->sitelinkx->suchm == "1" ) echo 'checked="checked"' ?> /><label for="suchm"><?php echo JText::_( 'SL_SUCHM_TW' ); ?></label>
        </fieldset>			
			</td>
		</tr>
    
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_sitelinkx" />
<input type="hidden" name="id" value="<?php echo $this->sitelinkx->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="sitelinkx" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
