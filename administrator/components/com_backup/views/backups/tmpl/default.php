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
$saveOrder	= $listOrder == 'a.id';

$root = JURI::root();
?>
<form action='<?php echo JRoute::_('index.php?option=com_backup'); ?>' method='post' name='adminForm' >
	<fieldset id='filter-bar'>
		<div class='filter-search fltlft'>
			<label class='filter-search-lbl' for='filter_search'><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type='text' name='filter_search' id='filter_search' value='<?php echo $this->escape($this->state->get('filter.search')); ?>' title='<?php echo JText::_('COM_CONTACT_SEARCH_IN_NAME'); ?>' />
			<button type='submit'><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type='button' onclick='document.id("filter_search").value="";this.form.submit();'><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
		<a id='save-list-ordering' class='custome-button-red fltrt' href='index.php?option=com_backup&task=backup.checkBackup&to=backups'>
			检查备份
		</a>
		
		<div class='filter-select fltrt'>
<?php
/*
			<select name='filter_published' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name='filter_category_id' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_backup'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
*/
?>
           
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
					<?php echo JHtml::_('grid.sort', 'COM_BACKUP_BACKUP_HEADING_SQLFILE', 'a.sqlfile', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_BACKUP_BACKUP_HEADING_STATE'); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_BACKUP_BACKUP_HEADING_ADDTIME', 'a.addtime', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_BACKUP_BACKUP_HEADING_CATID', 'a.catid', $listDirn, $listOrder); ?>
				</th>
				<th width='1%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan='6'>
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
		<?php
		$n = count($this->items);
		$config = JFactory::getConfig();
		foreach ($this->items as $i => $item) :
			$ordering	= $listOrder == 'a.id';
			$canCreate	= $user->authoriseInCustom('core.create',		'com_backup.category.'.$item->catid);
			$canEdit	= $user->authoriseInCustom('core.edit',			'com_backup.category.'.$item->catid);
			$canCheckin	= $user->authoriseInCustom('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authoriseInCustom('core.edit.own',		'com_backup.category.'.$item->catid) && $item->created_by == $userId;
			$canChange	= $user->authoriseInCustom('core.edit.state',	'com_backup.category.'.$item->catid) && $canCheckin;

			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_backup&task=edit&type=other&id='.$item->catid);
			$link = JRoute::_('index.php?option=com_backup&view=backup&layout=edit&task=backup.edit&id='.$item->id.'&'.JUtility::getToken().'=1&filter_order='.$listOrder.'&filter_order_Dir='.$listDirn);
			
			$GMTTime = new DateTime($item->addtime, new DateTimeZone('GMT'));
			$GMTTime->setTimezone(new DateTimeZone($config->get('offset')));
			$item->addtime = $GMTTime->format('Y-m-d H:i:s');
			?>
			<tr class='row<?php echo $i % 2; ?>'>
				<td class='center'>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<td align='center'>
					<a href='<?php echo $link; ?>'><?php echo str_replace('/backup/','',$item->sqlfile); ?></a>
				</td>
				<td align='center'>
					<?php echo file_exists(JPATH_ROOT.$item->sqlfile) ? '<span style="color:green;">正常</span>&nbsp;&nbsp;&nbsp;<a target="_blank" href="index.php?option=com_backup&task=backup.downloadSQL&bid='.$item->id.'">[下载]</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="resumeDB(\''.$item->id.'\',this)">[恢复数据库]</a>' : '<span style="color:red;">缺失文件</span>'; ?>
				</td>
				<td align='center'>
					<?php echo $item->addtime; ?>
				</td>
				<td align='center'>
					<?php echo $item->category_title?$item->category_title:'未分类'; ?>
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

<script type="text/javascript">
	var is_busy = false;
	function resumeDB(bid,ele){
		if (!confirm('此操作不可逆，确定要恢复数据库？恢复后您可能需要重新登录。')) return;
		if (is_busy == true) {
			alert('数据库操作正在进行，请耐心等待...');
			return;
		}
		is_busy = true;
		$(ele).html("[正在恢复...]").addClass("active");
		$.ajax({
			url:"index.php?option=com_backup&task=backup.resumeDB&bid="+parseInt(bid),
			success:function(data){
				data = data.trim();
				if (data=="true") {
					$(ele).html("[恢复成功]");
				} else {
					$(ele).html("[失败，重新恢复]").removeClass("active");
				}
				is_busy = false;
			}
		});
	}
</script>