<?php
/**
 * HTML layout for the editing or creating a product
 *
 * PHP versions 4 and 5
 *
 * @author     Su Xiong <suxiongit@163.com>
 * @copyright 2011 Woondroo (Copyright notice)
 * @license    GNU/GPL
 * @version    CVS: ID
 * Nov 12, 2009
 */

defined( '_JEXEC' ) or die ( 'Restricted Access'); 
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
jimport('mulan.mldb');
jimport('mulan.mlhtml');

$position = MulanDBUtil::getConfigByKey('positionwm');
$imagewm = MulanDBUtil::getConfigByKey('imagewm');
$opacitywm = MulanDBUtil::getConfigByKey('opacitywm');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'file.cancel' || task == 'file.saveWatermark') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_media'); ?>" method="post" name="adminForm" id="adminForm">
	<?php
	/**
	 * 初始化配置，只需要调用一次即可，注意引入“jimport('mulan.mldb')”
	 * 初始化后会得到当前文件上传的路径（如果是修改则给出对应的文件夹，如果是新建则给出一个临时文件夹并将临时文件夹放入“tempFolder”输入框中，后台获取后可对此文件夹重命名）
	 */
	$upload_root_uri = 'watermark';
	echo '<input type="hidden" id="tempFolder" name="tempFolder" value="'.$upload_root_uri.'" />';
	?>
	<fieldset class="adminform">
        <legend>水印管理</legend>
		<table class="admintable">
	       <tr>
	            <td width="100" align="right" class="key">
	                <label for="title">是否启用</label>
	            </td>
	            <td width="222">
	                <?php
					$mcs = array(
						 array('id'=>'0','name'=>'否')
						,array('id'=>'1','name'=>'是')
						);
					?>
					<select name="published" id="published"><?php echo MulanHtmlUtil::getSelectOption($mcs,'id','name',MulanDBUtil::getConfigByKey('activewm'),true,true); ?></select>
	            </td>
	            <td><div id="titleTip"></div></td>
	        </tr>
	        
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="title"><?php echo JText::_( '水印图片' ); ?></label>
	            </td>
	            <td>
	                <input type="text" value="<?php echo $imagewm; ?>" id="z_img" size="26" name="image" readonly />
					<div class="button2-left">
						<div class="image">
							<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name=z_img&amp;e_show=z_show&amp;type=insert&amp;unsetwm=1&amp;tofolder=watermark" class="modal" id="">图片</a>
						</div>
					</div>
	            </td>
	            <td>格式是：jpg,png,gif</td>
	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="title"><?php echo JText::_( '图片预览' ); ?></label>
	            </td>
	            <td>
	                <img <?php echo $imagewm ? 'src="../'.$imagewm.'"' : ''; ?> id="z_show"/>
	            </td>
	            <td></td>
	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="title">水印透明度</label>
	            </td>
	            <td width="222">
	                <input name="opacitywm" value="<?php echo $opacitywm; ?>" size="26"/>
	            </td>
	            <td><div id="titleTip">0为全透明，50为半透明，100为不透明</div></td>
	        </tr>
	         <tr>
	            <td width="100" align="right" class="key">
	                <label for="title">水印位置</label>
	            </td>
	            <td>
	                <?php //
					$mcs = array(
						 array('id'=>'0','name'=>'随机')
						,array('id'=>'1','name'=>'左上角')
						,array('id'=>'2','name'=>'上面中间')
						,array('id'=>'3','name'=>'右上角')
						,array('id'=>'4','name'=>'左面中间')
						,array('id'=>'5','name'=>'居中')
						,array('id'=>'6','name'=>'底部中间')
						,array('id'=>'7','name'=>'左下角')
						,array('id'=>'8','name'=>'右面中间')
						,array('id'=>'9','name'=>'右下角')
						);
					?>
					<select name="position" id="position" onchange="showtip(this.selectedIndex);"><?php echo  MulanHtmlUtil::getSelectOption($mcs,'id','name',$position,true); ?></select>
	            </td>
	            <td><div id="titleTip">水印在原图的相对位置</div></td>
	        </tr>
	        <tr>
	        	<td colspan="3">如果不设置以下偏移x,y二个值，水印将紧贴着原图的左上角或右上角或左下角等，适当设置可以使水印偏移原图的四边</td>
	        </tr>
	         <tr>
	            <td width="100" align="right" class="key">
	                <label for="title"><?php echo JText::_( '水印偏移x(像素)' ); ?></label>
	            </td>
	            <td>
	                <input type="text" value="<?php echo (int)MulanDBUtil::getConfigByKey('offxwm'); ?>" size="26" name="x" />
	            </td>
	            <td><span id="x0"<?php echo $position!=0?' style="display:none"':''; ?>>水印位置随机：此项无效；</span>
	            	<span id="x1"<?php echo $position==1?'':' style="display:none"'; ?>>水印位置左上：偏移x即偏移原图左面距离；</span>
	            	<span id="x2"<?php echo $position==2?'':' style="display:none"'; ?>>水印位置上面中间：偏移x即水印在原图上面中间后再往右移动距离；</span>
	            	<span id="x3"<?php echo $position==3?'':' style="display:none"'; ?>>水印位置右上：偏移x即偏移原图右面距离；</span>
	            	<span id="x4"<?php echo $position==4?'':' style="display:none"'; ?>>水印位置左面中间：偏移x即水印在原图左面中间后再往右移动距离；</span>
	            	<span id="x5"<?php echo $position==5?'':' style="display:none"'; ?>>水印位置居中：偏移x即居中后往右移动距离；</span>
	            	<span id="x6"<?php echo $position==6?'':' style="display:none"'; ?>>水印位置底部中间：偏移x即水印在原图底部中间后再往右移动距离；</span>
	            	<span id="x7"<?php echo $position==7?'':' style="display:none"'; ?>>水印位置左下：偏移x即偏移原图左面距离；</span>
	            	<span id="x8"<?php echo $position==8?'':' style="display:none"'; ?>>水印位置右面中间：偏移x即水印在原图右面中间后再往左移动距离；</span>
	            	<span id="x9"<?php echo $position==9?'':' style="display:none"'; ?>>水印位置右下：偏移x即偏移原图右面距离；</span>
	            </td>
	        </tr>
	        <tr>
	            <td width="100" align="right" class="key">
	                <label for="title"><?php echo JText::_( '水印偏移y(像素)' ); ?></label>
	            </td>
	            <td>
	                <input type="text" value="<?php echo (int)MulanDBUtil::getConfigByKey('offywm'); ?>" size="26" name="y" />
	            </td>
	            <td><span id="y0"<?php echo $position!=0?' style="display:none"':''; ?>>水印位置随机：此项无效；</span>
	            	<span id="y1"<?php echo $position==1?'':' style="display:none"'; ?>>水印位置左上：偏移y即偏移原图上面距离；</span>
	            	<span id="y2"<?php echo $position==2?'':' style="display:none"'; ?>>水印位置上面中间：偏移y即水印在原图上面中间后再往下移动距离；</span>
	            	<span id="y3"<?php echo $position==3?'':' style="display:none"'; ?>>水印位置右上：偏移y即偏移原图上面距离；</span>
	            	<span id="y4"<?php echo $position==4?'':' style="display:none"'; ?>>水印位置左面中间：偏移y即水印在原图左面中间后再往下移动距离；</span>
	            	<span id="y5"<?php echo $position==5?'':' style="display:none"'; ?>>水印位置居中：偏移y即居中后往下移动距离；</span>
	            	<span id="y6"<?php echo $position==6?'':' style="display:none"'; ?>>水印位置底部中间：偏移y即水印在原图底部中间后再往上移动距离；</span>
	            	<span id="y7"<?php echo $position==7?'':' style="display:none"'; ?>>水印位置左下：偏移y即偏移原图下面距离；</span>
	            	<span id="y8"<?php echo $position==8?'':' style="display:none"'; ?>>水印位置右面中间：偏移y即水印在原图右面中间后再往下移动距离；</span>
	            	<span id="y9"<?php echo $position==9?'':' style="display:none"'; ?>>水印位置右下：偏移y即偏移原图底部距离；</span></td>
	        </tr>
	   	</table>
   	</fieldset>
	<input type="hidden" name="task" value="" />
</form>
<script>
function showtip(index){
	var itemx,itemy;
	for(var i=0;i<9;i++){
		itemx=document.getElementById('x'+i);
		itemy=document.getElementById('y'+i);
		if(i==index){
			itemx.style.display='block';
			itemy.style.display='block';
		}else{
			itemx.style.display='none';
			itemy.style.display='none';
		}
	}
}
</script>