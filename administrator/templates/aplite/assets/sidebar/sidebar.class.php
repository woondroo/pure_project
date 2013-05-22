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

class APsidebar
{
	/**
 * 把从数据库中查询出来的对象列表数据转换成数组
 * $k = 由$k做关键字
 */
	function objectToArray($os,$k='id',$sign=''){
		$a = array();
		foreach($os as $o){
			if($sign){
				$a[]=$o->$k;	
			}else{
				$a[$o->$k]=$o;
			}
		}
		return $a;
	}
	function load_sidebar($authCheck = true)
	{
	// Initialise variables.
			$lang	= JFactory::getLanguage();
			$user	= JFactory::getUser();
			$userGroup = $user->groups;//array();注超级管理员的group_id 为8
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$result	= array();
			$langs	= array();
			$query->select('a.id,a.img img, a.title, a.componenturl as link, a.catid as parent_id,a.element,a.rules');
			$query->from('#__aclmanager AS a');
			$query->where('catid <> 0');
			$query->where('published=1');
			$query->order('a.ordering');
			$query->order('a.id');
			$db->setQuery($query);
			// component list
			$components	= $db->loadObjectList();
			$queryTop =  $db->getQuery(true);
			$queryTop->select('* FROM #__aclmanager WHERE catid = 0 and published=1 order by ordering');
			$db->setQuery($queryTop);
			$topMenu = $db->loadObjectList();
			foreach ($topMenu as &$component) {
				$result[$component->id] = $component;
				if (!isset($result[$component->id]->submenu)) {
					$result[$component->id]->submenu = array();
				}
			}
			foreach ($components as &$component) {
				// Trim the menu link.
				$component->link = trim($component->link);
				//待修改的逻辑 通过 aclmanager 表中的 rules 判断访问逻辑 if ($authCheck == false || ($authCheck && $user->authorise('core.manage', $component->element))) {
					// Sub-menu level.
					if (isset($result[$component->parent_id])) {
						// Add the submenu link if it is defined.
						if (isset($result[$component->parent_id]->submenu) && !empty($component->link)) {
							$result[$component->parent_id]->submenu[] = &$component;
						}
					}
				//}
			}
			if(in_array(8,$userGroup))//超级管理员返回所有目录
			{
				return $result;
			}
			else
			{
				foreach ($components as $component) {
					if($component->rules)
					{
						$rules = json_decode($component->rules);
						foreach($rules as $accessGroup)
						{	
							$accessable = array();
							$unaccessable =array();
							foreach($accessGroup as $index=>$access)//index为权限组的值 access=1 有权限访问 =0 无权限访问
							{
								if($access==1)array_push($accessable,$index);
								if($access==0)array_push($unaccessable,$index);
							}
							$accessable = array_intersect($accessable,$userGroup);//计算可访问的权限组 交集
							$unaccessable = array_intersect($unaccessable,$userGroup);
							if(!(count($accessable)>0 && count($unaccessable)==0))
							{
								if($result[$component->parent_id]->submenu)
								{
									foreach($result[$component->parent_id]->submenu as $i=>$sub)
									{
										if($sub->id==$component->id)
										{
											unset($result[$component->parent_id]->submenu[$i]);
										}
									}
								}
							}
						}
					}
					else
					{
						if(count($result[$component->parent_id]->submenu)>0)
						{
							foreach($result[$component->parent_id]->submenu as $i=>$sub)
							{
								if($sub->id==$component->id)
								{
									unset($result[$component->parent_id]->submenu[$i]);
								}
							}
						}
						
					}
					if(count($result[$component->parent_id]->submenu)==0)
					{
						unset($result[$component->parent_id]);
					}
				}
				return $result;
			}
			    
	}
}
?>
