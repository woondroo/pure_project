<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo 'ID'; ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo '目录标题'; ?>
	</th>
	<th>
		<?php echo '访问路径'; ?>
	</th>
	<th>
		<?php echo '所属权限分类'; ?>
	</th>
</tr>
