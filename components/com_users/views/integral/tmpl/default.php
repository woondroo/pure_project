<?php
/**
 * @version		$Id: default.php 21397 2011-05-26 23:58:47Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

jimport('mulan.mldb');
$itemLink = 'index.php?option=com_integral&view=integral&Itemid=546&limitstart=0&id=';
?>
<div class="integral-center<?php echo $this->pageclass_sfx?>">
	<div class="integral-row integral-row-title">
		<div class="integral-row-pro">兑换产品</div>
		<div class="integral-row-reason">扣除积分原因</div>
		<div class="integral-row-use">扣除积分</div>
		<div class="integral-row-last">剩余积分</div>
		<div class="integral-row-order">兑换日期</div>
		<div class="integral-row-receive">领取日期</div>
		<div class="integral-row-complete">完成日期</div>
		<div class="integral-row-execute">操作</div>
		<p class="clr"></p>
	</div>
	<?php
	if (count($this->items)) {
		foreach ($this->items as $key=>$item) {
			if ($item->pid) {
				$pro = MulanDBUtil::getObjectBySql('select id,catid,title from #__integral where id='.$item->pid);
			}
	?>
	<div class="integral-row <?php echo $key%2 == 0 ? 'integral-row-light' : 'integral-row-deep'; ?>">
		<div class="integral-row-pro"><?php echo $pro->id ? '<a target="_blank" href="'.$itemLink.$pro->catid.'&pid='.$pro->id.'">'.$pro->title.'</a>' : '无产品兑换'; ?></div>
		<div class="integral-row-reason"><?php echo $item->reason; ?></div>
		<div class="integral-row-use"><?php echo $item->use; ?></div>
		<div class="integral-row-last"><?php echo $item->last; ?></div>
		<div class="integral-row-order"><?php echo $item->ordertime == '' || $item->ordertime == '0000-00-00 00:00:00' ? '未经预订' : date('Y-m-d',strtotime($item->ordertime)); ?></div>
		<div class="integral-row-receive"><?php echo $item->receivetime == '' || $item->receivetime == '0000-00-00 00:00:00' ? '未领取' : date('Y-m-d',strtotime($item->receivetime)); ?></div>
		<div class="integral-row-complete"><?php echo $item->completetime == '' || $item->completetime == '0000-00-00 00:00:00' ? '未完成' : date('Y-m-d',strtotime($item->completetime)); ?></div>
		<div class="integral-row-execute">
			<?php
			if ($item->state == -1) {
				echo '已取消';
			} else if ($item->state == 0) {
				echo '<a onclick="javascript:return confirm(\'确定要取消？取消后将归还您的积分！\');" href="index.php?option=com_users&view=integral&task=integral.cancel&oid='.$item->id.'&r='.rand().'">取消兑换</a>';
			} else if ($item->state == 1) {
				echo '<a onclick="javascript:return confirm(\'确定已经领取？确认后兑换将完成，不可再做更改！\');" href="index.php?option=com_users&view=integral&task=integral.received&oid='.$item->id.'&r='.rand().'">确认领取</a>';
			} else if (($item->ordertime == '' || $item->ordertime == '0000-00-00 00:00:00') && $item->state == 2) {
				echo '已完成';
			} else if ($item->state == 2) {
				echo '兑换成功';
			}
			?>
		</div>
		<p class="clr"></p>
	</div>
	<?php
		}
	}
	?>
</div>
<div class='pagination'>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>