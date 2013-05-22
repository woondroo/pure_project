<?php
/**
* @version   1.x
* @package   AdminPraise Lite
* @copyright (C) 2008 - 2011 Pixel Praise LLC
* @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

/**
*    This file is part of AdminPraise Lite.
*    
*    AdminPraise Lite is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with AdminPraise Lite.  If not, see <http://www.gnu.org/licenses/>.
*
**/

defined('_JEXEC') or die('Direct access is not allowed');
jimport('mulan.mldb');

class APsidebarhtml
{
	function list_sidebar(&$components) {
		$ap_task = (JRequest::getVar('ap_task'));
		$showChildren  = $this->params->get('showChildren', 0);
		echo JHtml::_('sliders.start','sidebar-sliders',array('useCookie'=>'1'));
		
		$mainframe = JFactory::getApplication();
		$acl = &JFactory::getACL();
		$user = &JFactory::getUser();
		$option = JRequest::getCmd('option');
		
		$k = 0;
		foreach ($components as $i => $component) {
			echo JHtml::_('sliders.panel', JText::_($component->title).'', 'cpanel-panel-'.$component->title);
			echo "<ul class=\"child-list\" id=\"child-list-".JText::_($component->title)."\">";
			
			if(count($component->submenu)) {
				foreach ($component->submenu as $i2 => $child)
				{
					/**
					 * 2012-06-27 wengebin test
					 * 
					 * 通过获取 option 不能精确定位每个链接的激活状态，因为可能有多个相同组件的管理同时出现
					 
					$child_link = $child->link;
					$child_link_params = explode('?',$child->link);
					$child_option = '';
					if ($child_link_params[1]) {
						$params_array = explode('&',$child_link_params[1]);
						if (count($params_array)) {
							foreach ($params_array as $p) {
								if (strpos($p,'option') == 0) {
									$option_val = explode('=',$p);
									$child_option = $option_val[1];
									break;
								}
							}
						}
					}
					 */
					
					if(JURI::getInstance() == JURI::base().$child->link){
						$active = "active";
					} else {
						$active = "";
					}
					
					if(file_exists($child->img)) {
						$childimage = "style=\"background-image:url(".$child->img.");\"";
					} else {
						$childimage = "class=\"".substr($child->img, 6)."\"";
					}
					
					echo "<li class=\"child ".substr($child->img, 6)."\">";
					echo "<a href='$child->link' class='child-link $active'><span>".JText::_($child->title)."</span></a></li>";
					$k = 1 - $k;
				}
            } else {
				if(JURI::getInstance() == JURI::base().$component->link){
					$active = "active";
				} else {
					$active = "";
				}
				if(file_exists($component->img)) {
					$componentimage = "style=\"background-image:url(".$component->img.");\"";
				} else {
					$componentimage = "class=\"".substr($component->img, 6)."\"";
				}
				echo "<li class=\"child\"><a href=\"".$component->link."\" class=\"child-link ".$active."\"><span $componentimage>".JText::_($component->title)."</span></a></li>";
            }
	        echo "</ul>";
			$k = 1 - $k;
		}
		      
		/**
		 * 2012-02-24 wengebin edit
		 */
		$display_menu = MulanDBUtil::getObjectBySql('select * from #__modules where position=\'menu\' and module=\'mod_menu\' limit 1')->published;
		if ($user->authorise('com_config.manage') && $display_menu) {
			echo JHtml::_('sliders.panel', JText::_('系统设置'), 'cpanel-panel-system');
?>
<ul class="child-list" id="child-list-settings">
	<li class="child system">
		<a href="index.php?option=com_backup&view=backups" class="child-link <?php if ($option =="com_backup") { echo "active"; } ?>">
			<span>网站备份</span>
		</a>
	</li>
	<li class="child system">
		<a href="index.php?option=com_admin&view=sysinfo" class="child-link <?php if ($option =="com_admin") { echo "active"; } ?>">
			<span>系统信息</span>
		</a>
	</li>
	<li class="child config">
		<a href="index.php?option=com_config" class="child-link <?php if ($option =="com_config") { echo "active"; } ?>">
			<span>全局设置</span>
		</a>
	</li>
	<li class="child checkin">
		<a href="index.php?option=com_checkin" class="child-link <?php if ($option =="com_checkin") { echo "active"; } ?>">
			<span>全站回存</span>
		</a>
	</li>
	<li class="child install">
		<a href="index.php?option=com_installer" class="child-link <?php if ($option =="com_installer") { echo "active"; } ?>">
			<span>安装插件</span>
		</a>
	</li>
	<li class="child aclmanager">
		<a href="index.php?option=com_aclmanager" class="child-link <?php if ($option =="com_aclmanager") { echo "active"; } ?>">
			<span>权限管理</span>
		</a>
	</li>
	<li class="child comcreater">
		<a href="index.php?option=com_comcreater" class="child-link <?php if ($option =="com_comcreater") { echo "active"; } ?>">
			<span>组件生成</span>
		</a>
	</li>
</ul>
<?php
		}
		echo JHtml::_('sliders.end');
	}
	
}
?>
