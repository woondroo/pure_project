<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php jimport('joomla.html.pagination'); ?>
<?php $numCols = 0; // number of td tag... ie does not support colspan="0" :( 

$derzz =& JFactory::getDate()->toFormat(); 
$db =& JFactory::getDBO();
$query = "SELECT * FROM `#__sitelinkx_config`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$ihrev = $rows[0]->version;
$derzv = $ihrev;
$testen = 'http://www.extro-media.de/images/slcv.txt';

?>
<form action="index.php?option=com_sitelinkx" method="post" name="adminForm">
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'SL_LABEL', 'm.wort', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>				
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'SL_REPLACE_WITH_URL', 'm.ersatz', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>				
			</th>
			<th width="20">
				<?php echo JText::_( 'SL_URLAVAIL' ); ?>				
			</th>			
			<th>
				<?php echo JText::_( 'SL_TAG' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'SL_OPENIN' ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'SL_BEGPUB', 'm.begpub', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>				
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   'SL_ENDPUB', 'm.endpub', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>				
			</th>
			<th width="20">
				<?php echo JText::_( 'SL_ZEITRAUM' ); ?>
			</th>                              			
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="11">
				<?php echo $this->pagination->getListFooter(); ?>
				<!-- <br />
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="79JGZPNUSH6JN" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/DE/i/btn/btn_donateCC_LG.gif" type="image" /> <img src="https://www.paypal.com/de_DE/i/scr/pixel.gif" border="0" width="1" height="1" /> </form>
				<br /> -->
        <?php 
          $datei = @fopen($testen,'r');
          $derzv = @fread($datei,16);
          @fclose($datei);
          if ($derzv == '') $derzv = $ihrev;
          if ($ihrev<$derzv) echo "<p style='color:red;'><strong>A newer version is available, visit <a href='http://www.extro-media.de' title='www.extro-media.de' target='_blank'>www.extro-media.de</a></strong></p>";
        ?>				
			</td>
		</tr>
	</tfoot>
	
  <tbody>	
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)	{
		$row = &$this->rows[$i]; //items
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_sitelinkx&controller=sitelinkxlist&task=edit&cid[]='. $row->id );
		$published 	= JHTML::_('grid.published', $row, $i );		
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
        <?php echo $this->pagination->getRowOffset( $i ); ?>				
			</td>     			
			<td>
				<?php echo $checked; ?>
			</td>			
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->wort; ?></a>                   
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->ersatz; ?></a>
			</td>
			<td align="center">
				<?php
        switch ($row->published){
          case ('0');
            echo '<img src="components/com_sitelinkx/images/b_ye.gif">';
            break;
          case ('1');
				    $datei = @fopen($row->ersatz,'r');
            if ($datei) echo '<img src="components/com_sitelinkx/images/b_gr.gif">';
              else echo '<img src="components/com_sitelinkx/images/bulb.gif">';
            break;
        }
        ?>
			</td>			
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->schlagwort; ?></a>
			</td>
			<td align="center">
				<a href="<?php echo $link; ?>"><?php if ( $row->fenster == "_blank" ) echo JText::_( 'SL_NEWWIN' ); elseif ( $row->fenster == "_self" ) echo JText::_( 'SL_OLDWIN' ); else echo JText::_( 'SL_NOWIN' ); ?></a>
			</td>
			<td align="center">
				<a href="<?php echo $link; ?>"><?php echo $row->begpub; ?></a>
			</td>
			<td align="center">
				<a href="<?php echo $link; ?>">
        <?php 
        if ($row->endpub == '0000-00-00 00:00:00') echo JText::_( 'SL_NEVER' );
        else echo $row->endpub; 
        ?>
        </a>
			</td>
			<td align="center">
				<?php
          $ep = $row->endpub;
          $bp = $row->begpub;
           if ($ep < $bp) $ep =& JFactory::getDate( '31 December 2037 00:00:00' )->toFormat();
             switch($derzz) {
               case ($derzz < $bp);
                 echo '<img src="components/com_sitelinkx/images/ampel-gelb.png">';
                 break;
               case ($derzz > $ep);
                 echo '<img src="components/com_sitelinkx/images/ampel-rot.png">';
                 break;
               default:
                 echo '<img src="components/com_sitelinkx/images/ampel-gruen.png">';
                 break;      
             }
          $ep = '';
          $bp = '';
        ?>
			</td>                        			
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_sitelinkx" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="controller" value="sitelinkxlist" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
