<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$session = JFactory::getSession();
$select_table = $session->get('selectTable');
$show_fields = $session->get('showFields');
$show_fields_namestr = $session->get('showFields_name');
$search_field = $session->get('searchField');

$containt_field = array('name','title','status','featured','published','ordering');
$show_fields_names = array();
if ($show_fields) {
	$show_fields = strtolower($show_fields);
	$fields = explode('-',$show_fields);
	$show_fields_names_array = explode('-',$show_fields_namestr);
	if (count($show_fields_names_array)) {
		foreach ($show_fields_names_array as $fn) {
			$fns = explode('|',$fn);
			$show_fields_names[$fns[0]] = $fns[1];
		}
	}
}

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_newsfeeds.category');
$saveOrder	= $listOrder == 'a.ordering';

$view = JRequest::getVar('view');
$tmpl = JRequest::getVar('tmpl');
?>
<form action='<?php echo JRoute::_('index.php?option=com_'.selectpro.($view ? '&view='.$view : '').($tmpl ? '&tmpl='.$tmpl : '')); ?>' method='post' name='adminForm' >
	<?php
	if ($search_field) {
	?>
	<fieldset id='filter-bar'>
		<div class='filter-search fltlft'>
			<label class='filter-search-lbl' for='filter_search'><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type='text' name='filter_search' id='filter_search' value='<?php echo $this->escape($this->state->get('filter.search')); ?>' title='<?php echo JText::_('COM_CONTACT_SEARCH_IN_NAME'); ?>' />
			<button type='submit'><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type='button' onclick='document.id("filter_search").value="";this.form.submit();'><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<div class='clr'> </div>
	<?php
	}
	?>
	<table class='adminlist'>
		<thead>
			<tr>
				<th width='1%'>
					<input type='checkbox' name='checkall-toggle' value='' title='<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>' onclick='Joomla.checkAll(this)' />
				</th>
				<?php
				if (in_array('name',$fields)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', $show_fields_names['name'] ? $show_fields_names['name'] : 'COM_SELECTPRO_SELECTPRO_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<?php
				}
				?>
				
				<?php
				if (in_array('title',$fields)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', $show_fields_names['title'] ? $show_fields_names['title'] : 'COM_SELECTPRO_SELECTPRO_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<?php
				}
				?>
				
				<?php
				foreach ($fields as $field) {
					if (!in_array($field,$containt_field)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', ''.($show_fields_names[$field] ? $show_fields_names[$field] : $field), 'a.'.$field, $listDirn, $listOrder); ?>
				</th>
				<?php
					}
				}
				?>
				
				<?php
				if (in_array('status',$fields)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', $show_fields_names['status'] ? $show_fields_names['status'] : 'COM_SELECTPRO_SELECTPRO_HEADING_STATUS', 'a.status', $listDirn, $listOrder); ?>
				</th>
				<?php
				}
				?>
				
				<?php
				if (in_array('featured',$fields)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', $show_fields_names['featured'] ? $show_fields_names['featured'] : 'COM_SELECTPRO_SELECTPRO_HEADING_FEATURED', 'a.featured', $listDirn, $listOrder); ?>
				</th>
				<?php
				}
				?>
				
				<?php
				if (in_array('published',$fields)) {
				?>
				<th>
					<?php echo JHtml::_('grid.sort', $show_fields_names['published'] ? $show_fields_names['published'] : 'COM_SELECTPRO_SELECTPRO_HEADING_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<?php
				}
				?>
				
				<?php
				if (in_array('ordering',$fields)) {
				?>
				<th width='10%'>
					<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
				</th>
				<?php
				}
				?>
				
				<th width='1%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan='7'>
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$ordering	= $listOrder == 'a.ordering';
			$canCreate	= $user->authorise('core.create',		'com_selectpro.category.'.$item->catid);
			$canEdit	= $user->authorise('core.edit',			'com_selectpro.category.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own',		'com_selectpro.category.'.$item->catid) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state',	'com_selectpro.category.'.$item->catid) && $canCheckin;
			?>
			<tr class='row<?php echo $i % 2; ?>'>
				<td class='center'>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				
				<?php
				if (in_array('name',$fields)) {
				?>
				<td align='center'>
					<?php echo $item->name; ?>
				</td>
				<?php
				}
				?>
				
				<?php
				if (in_array('title',$fields)) {
				?>
				<td align='center'>
					<?php echo $item->title; ?>
				</td>
				<?php
				}
				?>
				
				<?php
				foreach ($fields as $field) {
					if (!in_array($field,$containt_field)) {
				?>
				<td align='center'>
					<?php echo $item->$field; ?>
				</td>
				<?php
					}
				}
				?>
				
				<?php
				if (in_array('status',$fields)) {
				?>
				<td align='center'>
					<?php echo $item->status; ?>
				</td>
				<?php
				}
				?>
				
				<?php
				if (in_array('featured',$fields)) {
				?>
				<td align='center'>
					<?php echo $item->featured; ?>
				</td>
				<?php
				}
				?>
				
				<?php
				if (in_array('published',$fields)) {
				?>
				<td class='center'>
					<?php echo JHtml::_('grid.boolean', $i, $item->published, 'selectpro.published', 'selectpro.unpublished'); ?>
				</td>
				<?php
				}
				?>
				
				<?php
				if (in_array('ordering',$fields)) {
				?>
				<td class='order'>
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'selectpros.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'selectpros.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'selectpros.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'selectpros.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type='text' name='order[]' size='5' value='<?php echo $item->ordering;?>' <?php echo $disabled ?> class='text-area-order' />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<?php
				}
				?>
				
				<td align='center'>
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type='hidden' name='task' value='' />
		<input type='hidden' name='boxchecked' value='0' />
		<input type='hidden' name='filter_order' value='<?php echo $listOrder; ?>' />
		<input type='hidden' name='filter_order_Dir' value='<?php echo $listDirn; ?>' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<script type="text/javascript">
	window.addEvent('domready', function(){
		document.formvalidator.initProPage();
	});
</script>
