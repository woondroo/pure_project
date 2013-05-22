<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_comcreater&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="comcreater-form" class="form-validate">
 
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( '细节编辑' ); ?></legend>
			<ul class="adminformlist">
				<?php echo $this->form->getInput('id');?>
				<li>
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
					<span id="warnning"> </span>
					<div class="clr"></div>
				</li>
				<li>
					<?php echo $this->form->getLabel('zhname'); ?>
					<?php echo $this->form->getInput('zhname'); ?>
					<div class="clr"></div>
				</li>
				<?php
				if (!$this->item->id) {
				?>
				<li>
					<div class="innerFrame">
						<div class="h1">为组件添加字段 <a id="addRow" href="javascript:;">+</a></div>
						<table width="95%" id="componentKeyTable" class="hasTheadBorder">
							<tr>
								<th>对应标题</th>
								<th>功能类型</th>
								<th>字段命名</th>
								<th>字段类型</th>
								<th>字段长度</th>
								<th>默认值</th>
								<th>字段备注</th>
								<th class='width80'>允许为空</th>
								<th class='width80'>列表中显示</th>
							</tr>
							<tr>
								<td>
									<input name="geform[title1]" type="text" id="geform-title1" value="编号(主键,自增)" readonly="readonly" />
								</td>
								<td>
									<select onchange="this.options[1].selected=true;" name="geform[function1]" id="geform-function1" readonly="readonly">
										<option value="text">text</option>
										<option selected="selected" value="hidden">hidden</option>
										<option value="editor">editor(编辑器)</option>
										<option value="radio">radio(是/否)</option>
										<option value="media">media(图片上传)</option>
										<option value="calendar">calendar(日期选择)</option>
									</select>
								</td>
								
								<td>
									<input name="geform[name1]" type="text" id="geform-name1" value="id" readonly="readonly" />
								</td>
								<td>
									<select onchange="this.options[0].selected=true;" name="geform[type1]" id="geform-type1" readonly="readonly">
										<option selected="selected" value="int">int</option>
										<option value="tinyint">tinyint</option>
										<option value="double">double</option>
										<option value="varchar">varchar</option>
										<option value="text">text</option>
										<option value="datetime">datetime</option>
									</select>
								</td>
								<td>
									<input name="geform[length1]" type="text" id="geform-length1" readonly="readonly" value="11" size="3" />
								</td>
								<td>
									<input name="geform[default1]" type="text" id="geform-default1" readonly="readonly" />
								</td>
								<td>
									<input name="geform[remark1]" type="text" id="geform-remark1" readonly="readonly" value="唯一编号" />
								</td>
								<td>
									<input class="checkbox_center_90" onchange="this.checked=false;" name="geform[null1]" value="1" type="checkbox" id="geform-null1" readonly="readonly" />
								</td>
								<td>
								</td>
							</tr>
						</table>
					</div>
				</li>
				<li>
					<label>添加分类功能</label><input   name="geform[category]" value="1" type="checkbox" id="geform-category"  />
				</li>
				<li>
					<label>添加published字段</label><input   name="geform[published]" value="1" type="checkbox" id="geform-published"  />
				</li>
				<li>
					<label>添加ordering字段</label><input   name="geform[ordering]" value="1" type="checkbox" id="geform-ordering" />
				</li>
				
				<li >
					<label class="red">同时生成前台组件</label><input   name="geform[fontendcoding]" value="1" type="checkbox" id="geform-ordering" />
				</li>
				<?php
				}
				?>
				<li>
				<?php echo $this->form->getLabel('description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('description'); ?>		
				</li>
				
			</ul>
			
	</div>
 
	<div class="width-40 fltrt" style="display:none;">
		<?php echo JHtml::_('sliders.start', 'comcreater-slider'); ?>
		<?php foreach ($params as $name => $fieldset): ?>
			<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
			<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
				<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
			<?php endif;?>
			<fieldset class="panelform" >
				<ul class="adminformlist">
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php endforeach; ?>
 
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type="hidden" name="task" value="comcreater.edit" />
		<input type="hidden" value="1" name="rows" id="rows"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	
	$(function(){
		var row = 1;
		$("#jform_title").change(function(){
			$.ajax({
				url:'<?php echo JURI::base(true); ?>/index.php?option=com_comcreater&task=checkNameByAjax',
				data:'comname='+$(this).val(),
				type:'post',
				dataType:'text',
				timeout:15000,
				error: function(){
					alert('通讯失败');
				},
				success:function(json){
					if(json){
						$("#warnning").html('<font style="color:green">可用</font>');
					}else{
						$("#warnning").html('<font style="color:red">已有同名组件</font>');
					}
				}	
			});
		});
		$("#addRow").click(function(){
			row++;
			var htmlstr = initalRowHTML(row,0);	
			$("#componentKeyTable").append(htmlstr);
		});
		$("#geform-category").click(function(){
			if($(this).attr("checked")=="checked")
			{
				row++;
				var htmlstr = initalRowHTML(row,'category');	
				$("#componentKeyTable").append(htmlstr);
			}
			else
			{
				row--;
				$("#componentKeyTable").find('.category').remove();
				$("#rows").val(row);
			}
		});
		
		$("#geform-published").click(function(){
			if($(this).attr("checked")=="checked")
			{
				row++;
				var htmlstr = initalRowHTML(row,'published');	
				$("#componentKeyTable").append(htmlstr);
			}
			else
			{
				row--;
				$("#componentKeyTable").find('.published').remove();
				$("#rows").val(row);
			}
		});
		$("#geform-ordering").click(function(){
			if($(this).attr("checked")=="checked")
			{
				row++;
				var htmlstr = initalRowHTML(row,'ordering');	
				$("#componentKeyTable").append(htmlstr);
				
			}
			else
			{
				row--;
				$("#componentKeyTable").find('.ordering').remove();
				$("#rows").val(row);
			}
		});
		
		//初始化行元素specialRow 包含  category ordering published
		function initalRowHTML(currentRow,specialRow)
		{
			var chname = specialRow?(specialRow=='published'?'是否发布':(specialRow=='category'?'类别ID':'排序')):0;
			var enname = specialRow?(specialRow=='published'?'published':(specialRow=='category'?'catid':'ordering')):0;
			var htmlstr = '<tr '+(specialRow?'class="'+specialRow+'"':'')+'>'+
						'<td><input name="geform[title'+currentRow+']" type="text" id="geform-title'+currentRow+'" '+(specialRow?'value="'+chname+'"':'')+'/></td>'+
						'<td>'+
							'<select name="geform[function'+currentRow+']"  id="geform-function'+currentRow+'">'+
								'<option value="text">text</option>'+
								'<option value="hidden">hidden</option>'+
								'<option value="editor">editor(编辑器)</option>'+
								'<option value="radio" '+(enname=='published'?'selected="selected"':'')+' >radio(是/否)</option>'+
								'<option value="media">media(图片上传)</option>'+
								'<option value="category" '+(enname=='catid'?'selected="selected"':'')+'>category(分类)</option>'+
								'<option value="calendar">calendar(日期选择)</option>'+
							'</select>'+
						'</td>'+
						'<td><input name="geform[name'+currentRow+']" type="text" id="geform-name'+currentRow+'" '+(enname?'value="'+enname+'"':'')+' /></td>'+
						'<td>'+
							'<select name="geform[type'+currentRow+']"  id="geform-type'+currentRow+'">'+
								'<option value="int">int</option>'+
								'<option value="tinyint" '+(enname=='published'?'selected="selected"':'')+' >tinyint</option>'+
								'<option value="double">double</option>'+
								'<option value="varchar">varchar</option>'+
								'<option value="text">text</option>'+
								'<option value="datetime">datetime</option>'+
							'</select>'+
						'</td>'+
						'<td><input name="geform[length'+currentRow+']" type="text" size="3"  id="geform-length'+currentRow+'" '+(enname=='published'?'value="1"':'value="11"')+' /></td>'+
						'<td><input name="geform[default'+currentRow+']" type="text" id="geform-default'+currentRow+'" '+(enname=='ordering'?'value="0"':'')+' /></td>'+
						'<td><input name="geform[remark'+currentRow+']" type="text" id="geform-remark'+currentRow+'" /></td>'+
						'<td><input class="checkbox_center_90" name="geform[null'+currentRow+']" value="1" type="checkbox" id="geform-null'+currentRow+'" /></td>'+
						'<td><input class="checkbox_center_90" name="geform[display'+currentRow+']" value="1" type="checkbox" id="geform-display'+currentRow+'" '+(enname?'checked="checked"':'')+' /></td>'+
					'</tr>';
			$("#rows").val(currentRow);
			return htmlstr;
		}
	});
</script>