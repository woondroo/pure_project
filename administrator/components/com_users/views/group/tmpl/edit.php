<?php
/**
 * @version		$Id: edit.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$canDo = UsersHelper::getActions();

jimport('mulan.mldb');
jimport('mulan.mlhtml');
$top_views = MulanDBUtil::getObjectlistBySql('select * from #__aclmanager where catid > 0 order by ordering asc,id asc');
if (count($top_views)) {
	$coms = MulanHTMLUtil::arrangeArrayBySomeElement('element',$top_views);
	$coms = MulanHTMLUtil::inputArrayBySomeElement('id','element',$top_views,$coms);
	$top_views = MulanHTMLUtil::arrangeArrayKey('id',$top_views);
	
	$roles_com = array();
	if (count($coms)) {
		foreach ($coms as $key=>$r) {
			if ($r) {
				$roles_com[] = '\''.$key.'\'';
			}
		}
	}
	
	$has_com = MulanDBUtil::getObjectlistBySql('select * from #__assets where name in('.implode(',',$roles_com).')');
	$has_com = MulanHTMLUtil::arrangeArrayKey('name',$has_com);
	/*
	if (count($coms)) {
		foreach ($coms as $k=>$r) {
			echo $k.':';var_dump($r);echo '<br/>';
		}
	}
	*/
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'group.cancel' || document.formvalidator.isValid(document.id('group-form'))) {
			Joomla.submitform(task, document.getElementById('group-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_users&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="group-form" class="form-validate">
	<div class="width-100">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_USERS_USERGROUP_DETAILS');?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<?php $parent_id = $this->form->getField('parent_id');?>
				<li><?php if (!$parent_id->hidden) echo $parent_id->label; ?>
				<?php echo $parent_id->input; ?></li>
			</ul>
		</fieldset>
		<fieldset class="adminform_assets">
			<legend><?php echo JText::_('COM_USERS_USERGROUP_ACL');?></legend>
			<ul class="adminformlist com_0">
				<li class="com_title">
					权限组件
				</li>
				<li class="com_acls">
					左侧栏目权限(显示请勾选)
				</li>
				<li class="com_comacls">
					组件访问权限
				</li>
			</ul>
			<?php
			if (count($top_views) && count($coms)) {
				$cur_com = 1;
				foreach ($coms as $key=>$com) {
					if (count($coms[$key])) {
			?>
			<ul class="adminformlist com_<?php echo $cur_com ?>">
				<li class="com_title">
					组件：<?php echo JText::_(strtoupper($key)); ?>
					<input type="hidden" name="coms_name[]" value="<?php echo $key; ?>" />
				</li>
				<li class="com_acls">
					<?php
						$get_views = $coms[$key];
						$contains_boxes = array();
						$contains_acls = array();
						foreach ($get_views as $v) {
							$get_view = $top_views[$v];
							$contains_boxes[] = $get_view->id;
							$rules = (array)json_decode($get_view->rules);
							if (count($rules)) {
								foreach ($rules as $r_key=>$rule) {
									if ($r_key == 'core.display') {
										$parse_rule = (array)$rule;
										if ($this->item->id) {
											$itemid = $this->item->id;
											$dis_result = (boolean)$rule->$itemid;
										}
					?>
					<input class="com_box" type="checkbox" id="box_<?php echo $get_view->id; ?>" name="<?php echo $get_view->element.'_box[]' ?>" value="<?php echo $get_view->id ?>" <?php echo $dis_result === true ? 'checked="checked"' : ''; ?> /><label for="box_<?php echo $get_view->id; ?>"><?php echo $get_view->title; ?></label><div class="clr"></div>
					<?php
									}
								}
							} else {
					?>
					<input class="com_box" type="checkbox" id="box_<?php echo $get_view->id; ?>" name="<?php echo $get_view->element.'_box[]' ?>" value="<?php echo $get_view->id ?>" /><label for="box_<?php echo $get_view->id; ?>"><?php echo $get_view->title; ?></label><div class="clr"></div>
					<?php
							}
						}
					?>
				</li>
				<li class="com_comacls">
					<?php
					$get_com = $has_com[$key];
					$rules = (array)json_decode($get_com->rules);
					$canDo = null;
					if (empty($canDo)) {
						$user = JFactory::getUser();
						$canDo = array();
						$actions = array(
							'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'core.expexcel'
						);
						foreach ($actions as $action) {
							$get = $user->authoriseByGroupId($this->item->id, $action, $key);
							$canDo[$action] = $get;
//							echo $action.':';var_dump($get);echo '<br/>';
						}
					}
					
					if (count($canDo)) {
						$use_num = 0;
						foreach ($canDo as $do_key=>$do) {
							if ($do_key == 'core.manage') {
								$do_title = '查看权限';
							} else if ($do_key == 'core.create') {
								$do_title = '创建权限';
							} else if ($do_key == 'core.edit') {
								$do_title = '编辑权限';
							} else if ($do_key == 'core.delete') {
								$do_title = '删除权限';
							} else if ($do_key == 'core.expexcel') {
								$do_title = '导出权限';
							} else {
								continue;
							}
							$contains_acls[] = $do_key;
							$use_num ++;
							if ($use_num > 2) {
								$use_num = 1;
							}
					?>
					<input class="acls_box" type="checkbox" id="acls_<?php echo $key.'_'.$do_key; ?>" name="<?php echo $key; ?>_acls[]" value="<?php echo $do_key; ?>" <?php echo $canDo[$do_key] === true ? 'checked="checked"' : ''; ?> /><label for="acls_<?php echo $key.'_'.$do_key; ?>"><?php echo $do_title; ?></label><?php echo $use_num == 2 ? '<div class="clr"></div>' : '' ?>
					<?php
						}
					}
					?>
					<input type="hidden" name="<?php echo $key; ?>_contains_boxes" value="<?php echo implode(',',$contains_boxes); ?>" />
					<input type="hidden" name="<?php echo $key; ?>_contains_acls" value="<?php echo implode(',',$contains_acls); ?>" />
				</li>
			</ul>
			<?php
					}
					$cur_com++;
				}
			}
			?>
		</fieldset>
		<input type="hidden" name="group_id" value="<?php echo $this->item->id; ?>">
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('.adminformlist').each(function(){
		var com_acls_height = $(this).find('.com_acls').height();
		var com_comacls_height = $(this).find('.com_comacls').height();
		if (com_acls_height > com_comacls_height) {
			$(this).find('.com_comacls').css({height:com_acls_height+"px"});
		} else {
			$(this).find('.com_acls').css({height:com_comacls_height+"px"});
		}
	});
});
</script>
<div class="clr"></div>
