<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
jimport('mulan.mlstring');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authoriseInCustom('core.edit.state', 'com_newsfeeds.category');
$saveOrder	= $listOrder == 'a.ordering';

?>
<form action='<?php echo JRoute::_('index.php?option=com_leavemessage'); ?>' method='post' name='adminForm' >
	<fieldset id='filter-bar'>
		<div class='filter-search fltlft'>
			<label class='filter-search-lbl' for='filter_search'><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type='text' name='filter_search' id='filter_search' value='<?php echo $this->escape($this->state->get('filter.search')); ?>' title='<?php echo JText::_('COM_CONTACT_SEARCH_IN_NAME'); ?>' />
			<button type='submit'><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type='button' onclick='document.id("filter_search").value="";this.form.submit();'><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
		<a id='save-list-ordering' class='custome-button-red fltrt' href='index.php?option=<?php echo JRequest::getVar('option') ?>&task=<?php echo substr(JRequest::getVar('option'),4)?>s.saveOrderByOrder&orderfield=<?php echo $this->state->get('list.ordering'); ?>&orderby=<?php echo $this->state->get('list.direction'); ?>&filter_category_id=<?php echo JRequest::getVar('filter_category_id')?JRequest::getVar('filter_category_id'):0?>'>
			保存当前排序
		</a>
		
		<div class='filter-select fltrt'>
			<select name='filter_published' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name='filter_category_id' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_leavemessage'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
		</div>
	</fieldset>
	<div class='clr'> </div>
	<table class='adminlist'>
		<thead>
			<tr>
				<th width='1%'>
					<input type='checkbox' name='checkall-toggle' value='' title='<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>' onclick='Joomla.checkAll(this)' />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_DEMAND', 'a.demand', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_SCOPE', 'a.scope', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_SEX', 'a.sex', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_PHONE', 'a.phone', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_EMAIL', 'a.email', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_LEAVEMESSAGE_LEAVEMESSAGE_HEADING_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width='10%'>
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
				<?php if ($canOrder && $saveOrder) :?>
					<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'leavemessages.saveorder'); ?>
				<?php endif; ?>
				</th>
				<th width='1%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan='9'>
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$ordering	= $listOrder == 'a.ordering';
			$canCreate	= $user->authoriseInCustom('core.create',		'com_leavemessage.category.'.$item->catid);
			$canEdit	= $user->authoriseInCustom('core.edit',			'com_leavemessage.category.'.$item->catid);
			$canCheckin	= $user->authoriseInCustom('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authoriseInCustom('core.edit.own',		'com_leavemessage.category.'.$item->catid) && $item->created_by == $userId;
			$canChange	= $user->authoriseInCustom('core.edit.state',	'com_leavemessage.category.'.$item->catid) && $canCheckin;

			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_leavemessage&task=edit&type=other&id='.$item->catid);
			?>
			<tr class='row<?php echo $i % 2; ?>'>
				<td class='center'>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td >
					<a href="<?php echo JRoute::_('index.php?option=com_leavemessage&task=leavemessage.edit&id='.$item->id);?>">
						<?php echo MulanStringUtil::substr_en($item->demand,40); ?>
					</a>
					<a class="top-this" 
						href="index.php?option=<?php echo JRequest::getVar("option") ?>&task=<?php echo substr(JRequest::getVar("option"),4)?>s.topthis&cid=<?php echo $item->id?>">置顶</a>
				</td>
				<td align='center'>
					<?php echo $item->title; ?>
				</td>
				<td align='center'>
					<?php echo $item->scope == 'inquiry' ? '其他方式' : '留言中心'; ?>
				</td>
				<td align='center'>
					<?php echo $item->username; ?>
				</td>
				<td align='center'>
					<?php
					$sex = '未填写';
					if ($item->sex == 'mr') {
						$sex = '先生';
					} else if ($item->sex == 'miss') {
						$sex = '小姐';
					} else if ($item->sex == 'ms') {
						$sex = '女士';
					} else if ($item->sex == 'mrs') {
						$sex = '夫人';
					}
					echo $sex; ?>
				</td>
				<td align='center'>
					<?php echo $item->phone; ?>
				</td>
				<td align='center'>
					<?php echo $item->email; ?>
				</td>
				<td class='center'>
					<div class="messages">
						<?php echo JHtml::_('grid.boolean', $i, $item->published, 'leavemessage.published', 'leavemessage.unpublished'); ?>
					</div>
				</td>
				<td class='order'>
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'leavemessages.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'leavemessages.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'leavemessages.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i+1]->catid), 'leavemessages.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type='text' name='order[]' size='5' value='<?php echo $item->ordering;?>' <?php echo $disabled ?> class='text-area-order' />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
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
