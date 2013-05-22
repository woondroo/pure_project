<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authoriseInCustom('core.edit.state', 'com_newsfeeds.category');
$saveOrder	= $listOrder == 'a.ordering';

$state = $this->state->get('filter.state');
?>
<form action='<?php echo JRoute::_('index.php?option=com_integral_history'); ?>' method='post' name='adminForm' >
	<fieldset id='filter-bar'>
		<div class='filter-search fltlft'>
			<label class='filter-search-lbl' for='filter_search'><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type='text' name='filter_search' id='filter_search' value='<?php echo $this->escape($this->state->get('filter.search')); ?>' title='<?php echo JText::_('COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_UID'); ?>' />
			<button type='submit'><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type='button' onclick='document.id("filter_search").value="";this.form.submit();'><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class='filter-select fltrt'>
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option<?php echo $state == '' ? ' selected="selected"' : ''; ?> value="">- 选择状态 -</option>
				<option<?php echo is_numeric($state) && $state == -1 ? ' selected="selected"' : ''; ?> value="-1">已取消</option>
				<option<?php echo is_numeric($state) && $state == 0 ? ' selected="selected"' : ''; ?> value="0">待领取</option>
				<option<?php echo is_numeric($state) && $state == 1 ? ' selected="selected"' : ''; ?> value="1">已领取</option>
				<option<?php echo is_numeric($state) && $state == 2 ? ' selected="selected"' : ''; ?> value="2">已完成</option>
			</select>
<!--
			<select name='filter_published' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name='filter_category_id' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_integral_history'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
-->
		</div>

	</fieldset>
	<div class='clr'> </div>
	<table class='adminlist'>
		<thead>
			<tr>
				<th width='1%'>
					<input type='checkbox' name='checkall-toggle' value='' title='<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>' onclick='Joomla.checkAll(this)' />
				</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_UID', 'a.uid', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_PID', 'a.pid', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_REASON', 'a.reason', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_USE', 'a.use', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_GET', 'a.get', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_LAST', 'a.last', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_STATE', 'a.state', $listDirn, $listOrder); ?>
					</th><th>
						<?php echo JHtml::_('grid.sort', 'COM_INTEGRAL_HISTORY_INTEGRAL_HISTORY_HEADING_WAY', 'a.way', $listDirn, $listOrder); ?>
					</th><th width='1%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan='10'>
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
		$n = count($this->items);
		if ($n <= 0) {
			echo '<tr><td colspan="10"><div style="width:100%;text-align:center;padding:20px 0;">暂无数据！</div></td></tr>';
		}
		foreach ($this->items as $i => $item) :
			$ordering	= $listOrder == 'a.ordering';
			$canCreate	= $user->authoriseInCustom('core.create',		'com_integral_history.category.'.$item->catid);
			$canEdit	= $user->authoriseInCustom('core.edit',			'com_integral_history.category.'.$item->catid);
			$canCheckin	= $user->authoriseInCustom('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authoriseInCustom('core.edit.own',		'com_integral_history.category.'.$item->catid) && $item->created_by == $userId;
			$canChange	= $user->authoriseInCustom('core.edit.state',	'com_integral_history.category.'.$item->catid) && $canCheckin;

			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_integral_history&task=edit&type=other&id='.$item->catid);
			$link = JRoute::_('index.php?option=com_integral_history&view=integral_history&layout=edit&task=integral_history.edit&id='.$item->id.'&'.JUtility::getToken().'=1&filter_order='.$listOrder.'&filter_order_Dir='.$listDirn);
			?>
			<tr class='row<?php echo $i % 2; ?>'>
				<td class='center'>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td align='center'>
					<a href='<?php echo $link; ?>'><?php echo $item->uusername; ?></a>
				</td>
				<td align='center'>
					<?php echo $item->ptitle ? $item->ptitle : '未兑换商品'; ?>
				</td>
				<td align='center'>
					<?php echo $item->reason; ?>
				</td>
				<td align='center'>
					<?php echo $item->use; ?>
				</td>
				<td align='center'>
					<?php echo $item->get; ?>
				</td>
				<td align='center'>
					<?php echo $item->last; ?>
				</td>
				<td align='center'>
					<?php
					if ($item->state == '-1') {
						echo '<font style="color:gray">已取消</font>';
					} else if ($item->state == '0') {
						echo '<font style="color:red">待领取</font>';
					} else if ($item->state == '1') {
						echo '<font style="color:blue">已领取</font>';
					} else if ($item->state == '2') {
						echo '<font style="color:green">已完成</font>';
					}
					?>
				</td>
				<td align='center'>
					<?php
					if ($item->way == '0') {
						echo '<font style="color:green">积分获得</font>';
					} else if ($item->way == '1') {
						echo '<font style="color:red">积分使用</font>';
					}
					?>
				</td>
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
