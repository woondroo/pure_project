<?php
defined('JPATH_BASE') or die();

class MulanDBUtil extends JObject{
	public static $mldb = array();
	public static $mods = array();
	
	public function __construct(){
		// init content here...
	}
	
	static function checkSameSignAndDB($sign) {
		if (!array_key_exists($sign,self::$mldb) || self::$mldb[$sign] == null) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * 其它数据库配置
	 * for example getObjectlistBySql('select * from video','xinxin');
	 */
	static function getDBConfig($sign){
		if(!$sign)return '';
		switch ($sign){
			case 'mulan_blog' : 
				return array(host=>'localhost',user=>'mulan',password=>'CWJJ4nwsUxpvnXhy',db=>'mulan_blog',dbprefix=>'wp_',dbtype=>'mysql',debug=>0,sign=>$sign);
			case 'local_mulan_blog' : 
				return array(host=>'localhost',user=>'root',password=>'greenwen',db=>'www_mulanblog',dbprefix=>'wp_',dbtype=>'mysql',debug=>0,sign=>$sign);
			default : 
				return '';
		}
	}
	
	static function getDBConnection($sign) {
		if (!MulanDBUtil::checkSameSignAndDB($sign)) {
			$config = MulanDBUtil::getDBConfig($sign);
			$db = &JFactory::getDbo($config);
			self::$mldb[$sign] = $db;
			return $db;
		} else {
			return self::$mldb[$sign];
		}
	}
	
	/**
	 * 解决SQL中的绰号和反斜杠问题
	 * 例子：如要插入 '\a这三个字符的数据sql可以这样写(发现于phpmyadmin)
	 * insert into tablename `value`='''\\a'; 这样的语句插入后在数据库中的值还是'\a，不会像其它的解决方案多个\,即\'\\a
	 */
	static function dbQuote($str=''){//返回的前后带上',  '\a => '''\\a'
		if($str==='')return'\'\'';
		return '\''.preg_replace('/([\\\\\'])/','$1$1',$str).'\'';
	}
	
	/**
	 * 查询SQL对应的结果
	 */
	static function getObjectBySql($sql,$sign='nosign'){
		if(!$sql)return null;
		$db = MulanDBUtil::getDBConnection($sign);
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	/**
	 * 查询SQL对应的结果
	 */
	static function getObjectlistBySql($sql,$sign='nosign'){
		if(!$sql)return null;
		$db = MulanDBUtil::getDBConnection($sign);
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	/**
	 * 执行SQL语句，增删改查都可以
	 */
	static function executeSql($sql='',$sign='nosign'){
		if(!$sql)return false;
		$db	= MulanDBUtil::getDBConnection($sign);
		$db->setQuery($sql);
		$r = $db->Query();
		if (preg_match('/^insert/',strtolower($sql))) {
			if ($id = $db->insertid()) {
				return $id;
			} else {
				return $r;
			}
		} else {
			return $r;	
		}
	}
	/**
	 *自动执行INSERT或UPDATE 
	*/
	static function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $sign = 'nosign')
	{
		$db	= MulanDBUtil::getDBConnection($sign);
		$field_names_tmp = $db->getTableFields($table);
		$field_names=array_keys($field_names_tmp[$table]);

		$sql = '';
		if ($mode == 'INSERT')
		{
		   $fields = $values = array();
			foreach ($field_names AS $value)
			{
				if (array_key_exists($value, $field_values) == true)
				{
				 $fields[] = '`'.$value.'`';
					 $values[] =  MulanDBUtil::dbQuote($field_values[$value]) ;
				 }
			}
			if (!empty($fields))
			{
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
			}
		}
		else
		{
			$sets = array();
			foreach ($field_names AS $value)
			{
				if (array_key_exists($value, $field_values) == true)
				{
					$sets[] = '`'.$value . "` = " . MulanDBUtil::dbQuote($field_values[$value]) ;
				}
			}
			if (!empty($sets))
			{
				$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
			}
		}
		
		if ($sql)
		{
			$db->setQuery($sql);
			if($mode=='INSERT')
			{ 
			 $ther = $db->Query();
			 if($theid = $db->insertid()){
				return $theid;
			 }else{
				return $ther;	
			 }
			}
		return $db->Query();
		}
		else
		{
			return false;
		}
	}

	
	/**
	 * 连接是否是登录状态的
	 * 在后台上传的连接是
	 * http://localhost/huada/index.php/upload?layout=upload&tmpl=component&sname=b489d856ec80b578b64e17776179a11c
	 * 查看数据库中有没有对应的sname记录，有则是登录的
	 *
	 */
	static function loginByAdmin(){
		$svalue = JRequest::getVar('sname');
		if($svalue){
			$dbuser = getObjectBySql('select userid from #__session where session_id='.MulanDBUtil::dbQuote($svalue));
			return $dbuser->userid;  	 
		} else {
			return 0;
		}
	}
	
	/**
	 * 得到相关配置数据
	 */
	static function getConfigByKey($key,$sign='nosign'){
		$db = MulanDBUtil::getDBConnection($sign);
		$query = 'select value from #__config where `key`='.$db->Quote($key).' limit 1';
		$db->setQuery($query);
		$r = $db->loadObject()->value;
		return $r;
	}
	
	/**
	 * 设置相关配置数据
	 */
	static function setConfigByKey($key,$value){
		return MulanDBUtil::executeSql('update #__config set `value` = '.MulanDBUtil::dbQuote($value).' where `key`='.MulanDBUtil::dbQuote($key));
	}
	
	/**
	 * 获取一个临时文件夹的名称
	 */
	static function getTempFolder(){
		$user = JFactory::getUser();
		$folder = 'temp-'.($user->id ? $user->id.'-' : '').time();
		return $folder;
	}
	
	/**
	 * @method getPreNextPro 获取上一个/下一个产品，适合所有组件
	 * 
	 * @param $pre_next 大于零表示从 $id 开始往前一个，即上一个
	 * @param $table 查询的表名，比如 #__product
	 * @param $table_alias 将表名重命名，便于使用查询条件
	 * @param $wheres 加入已有的 where 条件语句
	 * @param $ordering 用于排序的字段
	 * @param $id 当前产品的 ID
	 * @param $limit 获取从 $id 开始往前/往后多少条数据
	 * @param $is_zero_last 当前产品的 ordering 排序是否为 0，如果是 0 则需要特殊处理
	 * @param $only_sql 是否只返回 sql 语句而不用查询数据 true 为是，false 为否
	 * @param $is_count 此次查询是否是查询指定条件下的数据条数
	 */
	static function getPreNextPro($pre_next,$table,$table_alias,$wheres,$ordering,$id,$limit=1,$is_zero_last=false,$only_sql=false,$is_count=false) {
		$sql = 'select '.($is_count ? 'count(*) as count' : '*').' from '.$table.($table_alias ? ' as '.$table_alias.' ' : ' ');
		$orders = array();
		if (is_array($wheres) && count($wheres)) {
			$set_wheres = $wheres;
		} else if ($wheres) {
			$set_wheres = array($wheres);
		} else {
			$set_wheres = array();
		}
		if ($ordering == 0 && $is_zero_last == false) {
			$set_wheres[] = ($table_alias ? $table_alias.'.' : '').'ordering=0';
			$set_wheres[] = ($table_alias ? $table_alias.'.' : '').'id'.($pre_next > 0 ? '<' : '>').$id;
			
			$orders[] = ($table_alias ? $table_alias.'.' : '').'id '.($pre_next > 0 ? 'DESC' : 'ASC');
		} else if ($is_zero_last == true) {
			$set_wheres[] = ($table_alias ? $table_alias.'.' : '').'ordering>0';
			
			$orders[] = ($table_alias ? $table_alias.'.' : '').'ordering '.($pre_next > 0 ? 'ASC' : 'DESC');
			$orders[] = ($table_alias ? $table_alias.'.' : '').'id '.($pre_next > 0 ? 'DESC' : 'ASC');
		} else {
			$set_wheres[] = ($table_alias ? $table_alias.'.' : '').'ordering'.($pre_next > 0 ? '>' : '<').$ordering;
			
			$orders[] = ($table_alias ? $table_alias.'.' : '').'ordering '.($pre_next > 0 ? 'ASC' : 'DESC');
			$orders[] = ($table_alias ? $table_alias.'.' : '').'id '.($pre_next > 0 ? 'DESC' : 'ASC');
		}
		
		if (count($set_wheres)) {
			$sql .= ' where '.implode(' AND ',$set_wheres);
		}
		
		if ($only_sql) {
			return $sql;
		} else {
			if (count($orders)) {
				$sql .= ' order by '.implode(',',$orders);
			}
			
			$sql .= ' limit '.$limit;
			
			$result = MulanDBUtil::getObjectlistBySql($sql);
			if (!count($result) && $ordering == 0 && $pre_next > 0 && $is_zero_last == false) {
				$result = MulanDBUtil::getPreNextPro($pre_next,$table,$table_alias,$wheres,$ordering,$id,$limit,true);
			}
			
			return $result;
		}
	}
	
	static function loadmod($module, $position, $set_params=array(), $is_buffer=true)
	{
		if (!isset(self::$mods[$position]) || !$is_buffer) {
			self::$mods[$position] = '';
			$mod = JModuleHelper::getModules($position);
			
			$get_mod = null;
			if (count($mod)) {
				foreach ($mod as $key=>$m) {
					if ($m->module == $module && $m->position == $position) {
						$get_mod = $m;
						break;
					}
				}
			}
			
			if (!is_array($set_params)) {
				$set_params = array($set_params);
			}
			
			$params = json_decode($get_mod->params);
			
			/**
			 * mod_switcher
			 */
			if (isset($set_params['isdetail'])) $params->isdetail = $set_params['isdetail'];
			if (isset($set_params['isdetail'])) $params->detailpid = JRequest::getInt('pid');
			if (isset($set_params['from'])) $params->from = $set_params['from'];
			if (isset($set_params['pimgsdesc'])) $params->pimgsdesc = $set_params['pimgsdesc'];
			/**
			 * mod_share
			 */
			$params->hasscript = (isset($set_params['hasscript']) ? $set_params['hasscript'] : 1);
			if (isset($set_params['simg'])) $params->simg = $set_params['simg'];
			if (isset($set_params['stitle'])) $params->stitle = $set_params['stitle'];
			if (isset($set_params['surl'])) $params->surl = $set_params['surl'];
			
			$get_mod->params = json_encode($params);
			
			self::$mods[$position] = JModuleHelper::renderModule($get_mod);
		}
		return self::$mods[$position];
	}
}
?>
