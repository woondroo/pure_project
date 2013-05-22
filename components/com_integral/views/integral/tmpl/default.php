<?php
/**
 * @version		$Id: default.php 2012-05-19 13:25:06
 * @subpackage	com_integral
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;
$itemid = JRequest::getVar('Itemid');
$cateid = JRequest::getVar('id');
$start = $this->start;

jimport('mulan.mlimage');
jimport('mulan.mlhtml');

$base = JURI::base();
$itemLink = 'index.php?option=com_integral&view=integral&Itemid='.$itemid.($start > 0 ? '&start='.$start : '&limitstart=0').'&id='.$cateid.'&pid=';
?>
<div class='integral-item-view'>
	<div class="integral-title"><span><?php echo $this->item->title; ?></span></div>
	<div class="integral-mess-area">
		<div class="integral-img-view"></div>
		<div class="integral-mess right">
			<div class="line-mess"><span>需要积分：</span><?php echo $this->item->integral; ?></div>
			<div class="line-mess"><a onclick="javascript:return confirm('确定要兑换？确认兑换将加少<?php echo $this->item->integral; ?>积分！');" href="<?php echo JRoute::_('index.php?option=com_users&view=integral&task=integral.exchange&pid='.$this->item->id.'&r='.rand()); ?>" class="integral-choise">立即兑换</a></div>
			<div class="integral-text-desc">
				<?php echo MulanStringUtil::substr_zh(strip_tags($this->item->description),130,'...'); ?>
			</div>
			<?php
			echo MulanDBUtil::loadmod('mod_switcher','switcher-page',array('isdetail'=>true,'from'=>$this->item->proimgs));
			?>
		</div>
		<div class="clr"></div>
	</div>
	<div class="integral-desc-tip">商品描述</div>
	<div class="integral-desc">
		<?php echo $this->item->description; ?>
	</div>
	<div class="content-bottom-line"></div>
</div>