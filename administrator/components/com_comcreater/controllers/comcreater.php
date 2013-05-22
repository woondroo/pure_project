<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('mulan.mldb');
/**
 * Comcreater Controller
 */
class ComcreaterControllerComcreater extends JControllerForm
{
	
	protected $files_area;
	
	protected $cname;//组件名
	protected $comNameUp;//组件名首字母大写 Xxx
	protected $comNameLag;//组件在语言包中的大写字母标识
	protected $comName;//组件在语言包中的中文名
	
	protected $rows = 0;//动态添加的字段行数
	protected $formData;//表单数据
	
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('apply',	'saveNew');
		$this->registerTask('save',		'saveNew');
		$this->registerTask('save2New',	'saveNew');
	}
	
	public function saveNew()
	{
		jimport('mulan.mldb');
		$this->rows = JRequest::getVar('rows');
		$post = JRequest::get('post');
		$this->formData = JRequest::getVar('geform');
		
		$this->files_area = $post['jform']['title'];
		$folder_url = JPATH_ROOT.'/administrator/components/com_'.$post['jform']['title'];
		if($this->checkName($folder_url))
		{
			$first_floor_folders = array();
			array_push($first_floor_folders, 'controllers');
			array_push($first_floor_folders, 'helpers');
			array_push($first_floor_folders, 'models');
			array_push($first_floor_folders, 'sql');
			array_push($first_floor_folders, 'tables');
			array_push($first_floor_folders, 'views');
			$this->generateFolders($first_floor_folders,$folder_url);//STEP1
			
			$root_files = array();
			array_push($root_files, $folder_url.'/access.xml');
			array_push($root_files, $folder_url.'/'.$this->files_area.'.php');
			//array_push($root_files, $folder_url.'/'.$this->files_area.'.xml');
			array_push($root_files, $folder_url.'/config.xml');
			array_push($root_files, $folder_url.'/controller.php');
			//array_push($root_files, $folder_url.'/script.php');
			array_push($root_files, $folder_url.'/index.html');
			$this->generateFiles($root_files);//STEP2
			
			$this->generateCode();//STEP3
			if($this->rows>1){
				$generate_sql = $this->generateSQL($post);//STEP4
				$this->reBuildCode();//STEP5
				$this->reBuildLanguage();//STEP6
				$this->reBuildListView();//STEP7
			}
			$this->registInExtention();//STEP8
			$this->registInMenu();//STEP9
			
			if($this->formData['fontendcoding'])
			{
				$this->generateFontEndComponent();//STEP10
			}
			$this->addComAssetsName();//STEP11
			
			$data = array();
			$data['title'] = $post['jform']['title'];
			$data['zhname'] = $post['jform']['zhname'];
			$data['description'] = $post['jform']['description'];
			$data['params'] = serialize($post);
			$model = $this->getModel();
			if ($model->save($data)) {
				MulanDBUtil::executeSql($generate_sql);
				$this->setRedirect('index.php?option=com_comcreater&view=comcreaters','生成组件成功','message');
			} else {
				$this->setRedirect('index.php?option=com_comcreater&view=comcreaters','生成组件失败，请重试！','error');
			}
		} else {
			$this->setRedirect('index.php?option=com_comcreater&view=comcreaters','组件已经存在，请重试！','error');
		}
	}
	
	
	protected function checkName($folder_url)
	{
		if (!is_dir($folder_url)) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function generateFolders($folders,$folder_url)
	{
		@mkdir($folder_url);
		if (file_exists($folder_url)) {
			if (count($folders)) {
				foreach ($folders as $folder) {
					@mkdir($folder_url.'/'.$folder);
					if ($folder == 'controllers') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.php');
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'s.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'helpers') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'models') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.php');
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'s.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						$models_folders = array();
						array_push($models_folders, 'fields');
						array_push($models_folders, 'forms');
						array_push($models_folders, 'rules');
						$this->generateFolders($models_folders,$folder_url.'/'.$folder);
					} else if ($folder == 'fields') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'forms') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.js');
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.xml');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'rules') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/title.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'sql') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/install.mysql.utf8.sql');
						array_push($files, $folder_url.'/'.$folder.'/uninstall.mysql.utf8.sql');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						$sql_folders = array();
						array_push($sql_folders, 'updates');
						$this->generateFolders($sql_folders,$folder_url.'/'.$folder);
					} else if ($folder == 'updates') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						$mysql_folders = array();
						array_push($mysql_folders, 'mysql');
						$this->generateFolders($mysql_folders,$folder_url.'/'.$folder);
					} else if ($folder == 'mysql') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/0.0.1.sql');
						array_push($files, $folder_url.'/'.$folder.'/0.0.12.sql');
						array_push($files, $folder_url.'/'.$folder.'/0.0.13.sql');
						array_push($files, $folder_url.'/'.$folder.'/0.0.6.sql');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'tables') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->files_area.'.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'views') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						$views_folders = array();
						array_push($views_folders, $this->files_area);
						array_push($views_folders, $this->files_area.'s');
						$this->generateFolders($views_folders,$folder_url.'/'.$folder);
					} else if ($folder == $this->files_area) {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/submitbutton.js');
						array_push($files, $folder_url.'/'.$folder.'/view.html.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						if (!is_dir($folder_url.'/'.$folder.'/tmpl')) {
							@mkdir($folder_url.'/'.$folder.'/tmpl');
						}
						
						$files = array();
						
						array_push($files, $folder_url.'/'.$folder.'/tmpl/index.html');
						$this->generateFiles($files);
					} else if ($folder == $this->files_area.'s') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/view.html.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						if (!is_dir($folder_url.'/'.$folder.'/tmpl')) {
							@mkdir($folder_url.'/'.$folder.'/tmpl');
						}
						
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default_body.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default_foot.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default_head.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/index.html');
						$this->generateFiles($files);
					}
				}
			}
		}
	}
	protected function generateFiles($files)
	{
		if (count($files)) {
			foreach ($files as $file) {
				if (!file_exists($file)) {
					$this->writCode($file,'');
				}
			}
		}
	}
	protected function generateCode()
	{
		$requestForm		= JRequest::getVar('jform');
		$this->cname		= $requestForm['title'];
		$this->comName		= $requestForm['zhname'];
		$this->comNameUp 	= ucfirst($this->cname);
		$this->comNameLag	= strtoupper($this->cname);
		
		$this->codeOfRoot();
		$this->codeOfController();
		$this->codeOfHelpers();
		$this->codeOfModels();
		$this->codeOfTables();
		$this->codeOfViews();
		$this->codeOfLanguage();
	}
	
	protected function generateSQL($post)
	{
		$added_rows = $this->rows;
		$no_length_types = array('datetime','double','text');
		if ($added_rows > 0) {
			$config = JFactory::getConfig();
			$sql = 'create table `#__'.$this->files_area.'` (';
			for ($i = 0; $i < $added_rows; $i++) {
				$title = $post['geform']['title'.($i+1)];
				$function = $post['geform']['function'.($i+1)];
				$name = $post['geform']['name'.($i+1)];
				$type = $post['geform']['type'.($i+1)];
				$length = $post['geform']['length'.($i+1)];
				$default = $post['geform']['default'.($i+1)];
				$remark = $post['geform']['remark'.($i+1)];
				$null = $post['geform']['null'.($i+1)];
				$display = $post['geform']['display'.($i+1)];
				
				if ($name && $type) {
					$sql .= $name == 'id'
						?
						'`'.$name.'` '.$type.($length && !in_array($type,$no_length_types) ? '('.$length.') ' : ' ').($null ? 'NULL ' : 'NOT NULL ').'AUTO_INCREMENT PRIMARY KEY '.($remark ? 'COMMENT '.MulanDBUtil::dbQuote($remark) : '').($i != $added_rows-1 ? ',' : '')
						:
						'`'.$name.'` '.$type.($length && !in_array($type,$no_length_types) ? '('.$length.') ' : ' ').($null ? 'NULL ' : 'NOT NULL ').($default ? 'DEFAULT '.MulanDBUtil::dbQuote($default).' ' : '').($remark ? 'COMMENT '.MulanDBUtil::dbQuote($remark) : '').($i != $added_rows-1 ? ',' : '');
				}
			}
			$sql .= ',`metadesc` varchar(1024) NOT NULL COMMENT \'Metadata描述\'';
			$sql .= ',`metakey` varchar(1024) NOT NULL COMMENT \'Metadata关键词\'';
			$sql .= ',`metadata` varchar(1024) NOT NULL COMMENT \'Metadata其他参数\'';
			$sql .= ',`params` TEXT NULL COMMENT \'其他参数\'';
			$sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		}
		return $sql;
	}
	/*
		在数据库extention表中注册组件
	*/
	protected function registInExtention()
	{
		$insertSQL = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)  
					VALUES ('com_".$this->cname."', 'component', 'com_".$this->cname."', '', '0', '1', '1', '0', '', '{}', ' ', ' ', '0', '0000-00-00 00:00:00', '0', '0')";
		if(!MulanDBUtil::executeSql($insertSQL))
		{
			//抛出异常
		}
	}
	
	/*
		在目录中显示组件
	*/
	protected function registInMenu()
	{
	
	}
	/*
	*生成基础代码后 基于动态添加的字段重新生成表结构
	*since 113Q 1.0
	*/
	protected function reBuildCode()
	{
		$this->reBuildFormXML();
		$this->reBuildLanguage();
	}
	
	protected function addComAssetsName() {
		$filePath = 'language/zh-CN/zh-CN.com_users.ini';
		$code = '
COM_'.$this->comNameLag.'="'.$this->comName.'"';
		$this->writCode($filePath,$code);
	}
	
	/*
		生成代码勿用缩进，会导致读取错误，每条记录必须换行
	*/
	protected function reBuildLanguage()
	{
		
		$filePath = 'language/zh-CN/zh-CN.com_'.$this->cname.'.ini';
		
		$code = 'COM_'.$this->comNameLag.'="'.$this->comName.'"
COM_'.$this->comNameLag.'_ADMINISTRATION="'.$this->comName.' - 管理"
COM_'.$this->comNameLag.'_ADMINISTRATION_CATEGORIES="'.$this->comName.' - 分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_CREATING="'.$this->comName.' - 创建中"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_DETAILS="Details"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_EDITING="'.$this->comName.' - 编辑中"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_ERROR_UNACCEPTABLE="有一些不合法的输入"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_DESC="属于某个分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_LABEL="分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_LABEL="显示分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_DESC="如果设置显示，则会在记录中显示分类信息"

COM_'.$this->comNameLag.'_'.$this->comNameLag.'_MENU_SETTINGS_LABEL="菜单选项"

COM_'.$this->comNameLag.'_'.$this->comNameLag.'_HEADING_ID="Id"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'_EDIT="'.$this->comName.' 管理: 编辑"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'_NEW="'.$this->comName.' 管理: 新建"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'S="'.$this->comName.' 管理"
COM_'.$this->comNameLag.'_N_ITEMS_DELETED_1="一条记录已删除"
COM_'.$this->comNameLag.'_N_ITEMS_DELETED_MORE="%d 条记录已删除"
COM_'.$this->comNameLag.'_SUBMENU_MESSAGES="消息"
COM_'.$this->comNameLag.'_SUBMENU_CATEGORIES="分类"
COM_'.$this->comNameLag.'_CONFIGURATION="'.$this->comName.' 配置"
COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_LABEL="标题配置"
COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_DESC="配置将默认应用到所有记录"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_NO_ITEM_SELECTED="请选择一条记录"
COM_'.$this->comNameLag.'_N_'.$this->comNameLag.'_UNPUBLISED="已取消发布"
COM_'.$this->comNameLag.'_N_'.$this->comNameLag.'_PUBLISED="已发布"

COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_TITLE="'.$this->comName.'列表页"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_OPTION="'.$this->comName.'列表页默认选项"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_DESC="'.$this->comName.'列表页（相关描述）"

COM_'.$this->comNameLag.'_FIELD_SELECT_CATEGORY_DESC="选择默认显示的类别"
COM_'.$this->comNameLag.'_FIELD_SELECT_CATEGORY_LABEL="默认显示的类别"
COM_'.$this->comNameLag.'_FIELD_ISOPEN_DESC="是否打开将分类生成为子菜单的生成器"
COM_'.$this->comNameLag.'_FIELD_ISOPEN_LABEL="将分类显示为菜单"
COM_'.$this->comNameLag.'_FIELD_PARENTID_DESC="填写父级分类的ID"
COM_'.$this->comNameLag.'_FIELD_PARENTID_LABEL="父级分类ID"

COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_TITLE="'.$this->comName.'详细页"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_OPTION="'.$this->comName.'详细页默认选项"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_DESC="'.$this->comName.'详细页（相关描述）"


';
		for($i=1;$i<=$this->rows;$i++)
		{	
			$rowLanguage = '';
			if($this->formData['name'.$i])
			{
				
				switch($this->formData['function'.$i])
				{
					case 'text' 	: 	$rowLanguage = $this->getTextLanguage($this->formData,$i);break;
					case 'editor' 	: 	$rowLanguage = $this->getEditorLanguage($this->formData,$i);break;
					case 'radio' 	: 	$rowLanguage = $this->getRadioLanguage($this->formData,$i);break;
					case 'media' 	: 	$rowLanguage = $this->getMediaLanguage($this->formData,$i);break;
					case 'calendar'	: 	$rowLanguage = $this->getCalendarLanguage($this->formData,$i);break;
					
				}
			}
			if($this->formData['display'.$i]=="1")
			{
				$code .= 'COM_'.$this->comNameLag.'_'.$this->comNameLag.'_HEADING_'.(strtoupper($this->formData['name'.$i])).'="'.$this->formData['title'.$i].'"
';
			}
			$code.=$rowLanguage;
		}
		$this->replaceCode($filePath,$code);
		
		
	}
	
	protected function getTextLanguage($data,$row)
	{
		return 'COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL="'.$data['title'.$row].'"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC="输入'.$data['title'.$row].'"
';
	}
	protected function getEditorLanguage($data,$row)
	{
		return 'COM_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL="'.$data['title'.$row].'"
COM_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC="输入'.$data['title'.$row].'"
';
	}
	protected function getRadioLanguage($data,$row)
	{
		return 'COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL="'.$data['title'.$row].'"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC="选择'.$data['title'.$row].'"
';
	}
	protected function getMediaLanguage($data,$row)
	{
		return 'COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL="'.$data['title'.$row].'"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC="上传'.$data['title'.$row].'"
';
	}
	protected function getCalendarLanguage($data,$row)
	{
		return 'COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL="'.$data['title'.$row].'"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC="选择'.$data['title'.$row].'"
';
	}
	
	
	protected function reBuildFormXML()
	{	
		$filePath = 'components/com_'.$this->cname.'/models/forms/'.$this->cname.'.xml';
		$XML ='<?xml version="1.0" encoding="utf-8"?>
<form
	addrulepath="/administrator/components/com_'.$this->cname.'/models/rules"
>
	<fieldset name="details">
';
		for($i=1;$i<=$this->rows;$i++)
		{	
			$subFieldXML = '';
			if($this->formData['name'.$i])
			{
				
				switch($this->formData['function'.$i])
				{
					case 'hidden' 	: 	$subFieldXML = $this->getHiddenXML($this->formData,$i);break;
					case 'text' 	: 	$subFieldXML = $this->getTextXML($this->formData,$i);break;
					case 'editor' 	: 	$subFieldXML = $this->getEditorXML($this->formData,$i);break;
					case 'radio' 	: 	$subFieldXML = $this->getRadioXML($this->formData,$i);break;
					case 'media' 	: 	$subFieldXML = $this->getMediaXML($this->formData,$i);break;
					case 'category' : 	$subFieldXML = $this->getCategoryXML($this->formData,$i);break;
					case 'calendar' :	$subFieldXML = $this->getCalendarXML($this->formData,$i);break;
				}
			}
			$XML.=$subFieldXML;
		}
		$XML.='</fieldset>
	<fields name="params">
		<fieldset
			name="params"
			label="JGLOBAL_FIELDSET_DISPLAY_OPTIONS"
		>
			<field
				name="show_category"
				type="list"
				label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_LABEL"
				description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_DESC"
				default=""
			>
				<option value="">JGLOBAL_USE_GLOBAL</option>
				<option value="0">JHIDE</option>
				<option value="1">JSHOW</option>
			</field>
		</fieldset>
	</fields>
	
	<field
		name="metadesc"
		type="textarea"
		label="JFIELD_META_DESCRIPTION_LABEL"
		description="JFIELD_META_DESCRIPTION_DESC"
		rows="3"
		cols="40"/>

	<field
		name="metakey"
		type="textarea"
		label="JFIELD_META_KEYWORDS_LABEL"
		description="JFIELD_META_KEYWORDS_DESC"
		rows="3"
		cols="40"/>
	
	<fields name="metadata">
		<fieldset
			name="metadata"
			label="JGLOBAL_FIELDSET_DISPLAY_OPTIONS"
		>
			<field
				name="author"
				type="text"
				label="JAUTHOR"
				description="JFIELD_METADATA_AUTHOR_DESC"
				size="30"/>
	
			<field name="robots"
				type="list"
				label="JFIELD_METADATA_ROBOTS_LABEL"
				description="JFIELD_METADATA_ROBOTS_DESC"
			>
				<option value="">JGLOBAL_USE_GLOBAL</option>
				<option value="index, follow">JGLOBAL_INDEX_FOLLOW</option>
				<option value="noindex, follow">JGLOBAL_NOINDEX_FOLLOW</option>
				<option value="index, nofollow">JGLOBAL_INDEX_NOFOLLOW</option>
				<option value="noindex, nofollow">JGLOBAL_NOINDEX_NOFOLLOW</option>
			</field>
		</fieldset>
	</fields>
</form>
';
		$this->replaceCode($filePath,$XML);
		
	}
	
	protected function getHiddenXML($data,$row)
	{
		return '<field
			name="'.($data['name'.$row]).'"
			type="hidden"
			default="'.$data['default'.$row].'"
		/>
		';

	}
	protected function getTextXML($data,$row)
	{
		return '<field
			name="'.$data['name'.$row].'"
			type="text"
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC"
			size="40"
			class="inputbox"
			default="'.$data['default'.$row].'"
			'.($data['null'.$row]?'':'required="true"').'
		/>
		';
		
	}
	protected function getEditorXML($data,$row)
	{
		return '<field 
			name="'.$data['name'.$row].'"
			type="editor" 
			class="inputbox"
			label="COM_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL" 
			description="COM_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC"
			filter="safehtml" buttons="true" 
			'.($data['null'.$row]?'':'required="true"').'
		/>
		';
		 
	}
	protected function getRadioXML($data,$row)
	{
		return '<field
			name="'.$data['name'.$row].'"
			type="radio"
			default="1"
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC">
			<option
				value="1">JYES</option>
			<option
				value="0">JNO</option>
		</field>
		';
	
	}
	protected function getMediaXML($data,$row)
	{
		return '<field
			name="'.$data['name'.$row].'"
			type="media"
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC"
			'.($data['null'.$row]?'':'required="true"').'
		/>
		';
	}
	
	protected function getCategoryXML($data,$row)
	{
		return '<field
			name="'.$data['name'.$row].'"
			type="category"
			extension="com_'.$this->cname.'"
			class="inputbox"
			default=""
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC"
			required="true"
		>
			<option value="0">JOPTION_SELECT_CATEGORY</option>
		</field>
		';
	}
	
	protected function getCalendarXML($data,$row)
	{
		return '<field 
			name="'.$data['name'.$row].'" 
			type="calendar" 
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_'.(strtoupper($data['name'.$row])).'_DESC"
			class="inputbox" size="22"
			format="%Y-%m-%d %H:%M:%S" 
			filter="user_utc" 
		/>
		';
	}
	
	
	protected function reBuildListView()
	{
		$filePath = 'components/com_'.$this->cname.'/views/forms/'.$this->cname.'.xml';
		$colunm = 2;
		$head_html = "<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width='5'>
		<?php echo JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_HEADING_ID'); ?>
	</th>
	<th width='20'>
		<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count(\$this->items); ?>);' />
	</th>			
"
;		
		$body_html = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach(\$this->items as \$i => \$item): ?>
	<tr class='row<?php echo \$i % 2; ?>'>
		<td>
			<?php echo \$item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', \$i, \$item->id); ?>
		</td>
";

		for($i=1;$i<=$this->rows;$i++)
		{	
			if($this->formData['display'.$i]=="1")
			{
				$colunm++;
				$head_html .="	<th>
		<?php echo JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_HEADING_".(strtoupper($this->formData['name'.$i]))."'); ?>
	</th>
";
				if($this->formData['name'.$i]=="published")
				{
					$body_html .="	<td>
			<?php echo JHtml::_('grid.boolean', \$i, \$item->published, '".$this->cname.".published', '".$this->cname.".unpublished'); ?>
	</td>
";
				}
				else if($this->formData['name'.$i]=="ordering")
				{
				
				}
				else
				{
					$body_html .="	<td>
		<?php echo \$item->".$this->formData['name'.$i]."; ?>
	</td>
";
				}
			}
		}
		
		$head_html .= "</tr>";
		$body_html .= "	</tr>
<?php endforeach; ?>";

		$foot_html = "<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<td colspan='".$colunm."'><?php echo \$this->pagination->getListFooter(); ?></td>
</tr>
";	
		$filePath  = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_head.php';
		$this->replaceCode($filePath,$head_html);
		$filePath  = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_body.php';
		$this->replaceCode($filePath,$body_html);
		$filePath  = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_foot.php';
		$this->replaceCode($filePath,$foot_html);
		
	}
	
	/*
		组件root根目录下的文件
		access.xml 控制访问权限
		XXX.php 入口文件
		config.xml
		controller.php 控制文件
	*/
	protected function codeOfRoot()
	{
		//access.xml
		$filePath = 'components/com_'.$this->cname.'/access.xml';
		$code = '<?xml version="1.0" encoding="utf-8" ?>
<access component="com_'.$this->comNameUp.'">
	<section name="component">
		<action name="core.admin" title="JACTION_ADMIN" description="JACTION_ADMIN_COMPONENT_DESC" />
		<action name="core.manage" title="JACTION_MANAGE" description="JACTION_MANAGE_COMPONENT_DESC" />
		<action name="core.create" title="JACTION_CREATE" description="JACTION_CREATE_COMPONENT_DESC" />
		<action name="core.delete" title="JACTION_DELETE" description="JACTION_DELETE_COMPONENT_DESC" />
		<action name="core.edit" title="JACTION_EDIT" description="JACTION_EDIT_COMPONENT_DESC" />
	</section>
	<section name="message">
		<action name="core.delete" title="JACTION_DELETE" description="COM_'.$this->comNameLag.'_ACCESS_DELETE_DESC" />
		<action name="core.edit" title="JACTION_EDIT" description="COM_'.$this->comNameLag.'_ACCESS_EDIT_DESC" />
	</section>
</access>
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}//access.xml写入完毕
	
		//XXX.php 入口文件
		$filePath = 'components/com_'.$this->cname.'/'.$this->cname.'.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authoriseInCustom('core.manage', 'com_".$this->cname."')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
 
// require helper file
JLoader::register('".$this->comNameUp."Helper', dirname(__FILE__) . DS . 'helpers' . DS . '".$this->cname.".php');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by ".$this->comNameUp."
\$controller = JController::getInstance('".$this->comNameUp."');
 
// Perform the Request task
\$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
\$controller->redirect();
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		
		}//xxx.php写入完毕
		
		//config.xml
		$filePath = 'components/com_'.$this->cname.'/config.xml';
		$code = '<?xml version="1.0" encoding="utf-8"?>
<config>
	<fieldset
		name="titles"
		label="COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_LABEL"
		description="COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_DESC"
	>
		<field
			name="show_category"
			type="radio"
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_DESC"
			default="0"
		>
			<option value="0">JHIDE</option>
			<option value="1">JSHOW</option>
		</field>
	</fieldset>
	<fieldset
		name="permissions"
		label="JCONFIG_PERMISSIONS_LABEL"
		description="JCONFIG_PERMISSIONS_DESC"
	>
		<field
			name="rules"
			type="rules"
			label="JCONFIG_PERMISSIONS_LABEL"
			class="inputbox"
			validate="rules"
			filter="rules"
			component="com_'.$this->cname.'"
			section="component"
		/>
	</fieldset>
</config>
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}//config.xml写入完毕
		
		//controller.php
		$filePath = 'components/com_'.$this->cname.'/controller.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of ".$this->comNameUp." component
 */
class ".$this->comNameUp."Controller extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display(\$cachable = false) 
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', '".$this->comNameUp."s'));
 
		// call parent behavior
		parent::display(\$cachable);
 
		// Set the submenu
		".$this->comNameUp."Helper::addSubmenu('messages');
	}
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}//controller.php写入完毕
	}
	
	protected function codeOfController()
	{
		//xxx.php
		$filePath = 'components/com_'.$this->cname.'/controllers/'.$this->cname.'.php';
		$code = "<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * ".$this->comNameUp." Controller
 */
class ".$this->comNameUp."Controller".$this->comNameUp." extends JControllerForm
{
	".($this->formData['published']==1?"
	public function __construct(\$config = array())
	{
		parent::__construct(\$config);

		\$this->registerTask('published',		'changepublished');
		\$this->registerTask('unpublished',		'changepublished');
	}
	
	public function getModel(\$name = '".$this->comNameUp."', \$prefix = '".$this->comNameUp."Model', \$config = array('ignore_request' => true))
	{
		return parent::getModel(\$name, \$prefix, \$config);
	}

	
	public function changepublished()
	{
		
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		\$ids	= JRequest::getVar('cid', array(), '', 'array');
		\$values	= array('published' => 1, 'unpublished' => 0);
		\$task	= \$this->getTask();
		\$value	= JArrayHelper::getValue(\$values, \$task, 0, 'int');

		if (empty(\$ids)) {
			JError::raiseWarning(500, JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			\$model = \$this->getModel();
		
			// Change the state of the records.
			if (!\$model->published(\$ids, \$value)) {
				JError::raiseWarning(500, \$model->getError());
			} else {
				if (\$value == 1){
					\$this->setMessage(JText::plural('COM_".$this->comNameLag."_N_".$this->comNameLag."_PUBLISED', count(\$ids)));
				} else if (\$value == 0){
					\$this->setMessage(JText::plural('COM_".$this->comNameLag."_N_".$this->comNameLag."_UNPUBLISED', count(\$ids)));
				}
			}
		}

		\$this->setRedirect('index.php?option=com_".$this->cname."&view=".$this->cname."s');
	}
	":"")."
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}//xxx.php写入完毕
		
		//xxxs.php
		$filePath = 'components/com_'.$this->cname.'/controllers/'.$this->cname.'s.php';
		$code = "<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * ".$this->comNameUp."s Controller
 */
class ".$this->comNameUp."Controller".$this->comNameUp."s extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel(\$name = '".$this->comNameUp."', \$prefix = '".$this->comNameUp."Model') 
	{
		\$model = parent::getModel(\$name, \$prefix, array('ignore_request' => true));
		return \$model;
	}
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}//xxxs.php写入完毕
	}
	
	protected function codeOfHelpers()
	{
		//xxx.php
		$filePath = 'components/com_'.$this->cname.'/helpers/'.$this->cname.'.php';
		$code = "<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * ".$this->comNameUp." component helper.
 */
abstract class ".$this->comNameUp."Helper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu(\$submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_".$this->comNameLag."_SUBMENU_MESSAGES'), 'index.php?option=com_".$this->cname."', \$submenu == 'messages');
		JSubMenuHelper::addEntry(JText::_('COM_".$this->comNameLag."_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_".$this->cname."', \$submenu == 'categories');
		// set some global property
		\$document = JFactory::getDocument();
		\$document->addStyleDeclaration('.icon-48-".$this->cname." {background-image: url(../media/com_".$this->cname."/images/tux-48x48.png);}');
		if (\$submenu == 'categories') 
		{
			\$document->setTitle(JText::_('COM_".$this->comNameLag."_ADMINISTRATION_CATEGORIES'));
		}
	}
	/**
	 * Get the actions
	 */
	public static function getActions(\$messageId = 0)
	{
		\$user	= JFactory::getUser();
		\$result	= new JObject;
 
		if (empty(\$messageId)) {
			\$assetName = 'com_".$this->cname."';
		}
		else {
			\$assetName = 'com_".$this->cname.".message.'.(int) \$messageId;
		}
 
		\$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.expexcel'
		);
 
		foreach (\$actions as \$action) {
			\$result->set(\$action,	\$user->authoriseInCustom(\$action, \$assetName));
		}
 
		return \$result;
	}
}

";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	
	protected function codeOfModels()
	{
		//models/xxx.php
		$filePath = 'components/com_'.$this->cname.'/models/'.$this->cname.'.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * ".$this->comNameUp." Model
 */
class ".$this->comNameUp."Model".$this->comNameUp." extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	\$data	An array of input data.
	 * @param	string	\$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit(\$data = array(), \$key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authoriseInCustom('core.edit', 'com_".$this->cname.".message.'.((int) isset(\$data[\$key]) ? \$data[\$key] : 0)) or parent::allowEdit(\$data, \$key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable(\$type = '".$this->comNameUp."', \$prefix = '".$this->comNameUp."Table', \$config = array()) 
	{
		return JTable::getInstance(\$type, \$prefix, \$config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	\$data		Data for the form.
	 * @param	boolean	\$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm(\$data = array(), \$loadData = true) 
	{
		// Get the form.
		\$form = \$this->loadForm('com_".$this->cname.".".$this->cname."', '".$this->cname."', array('control' => 'jform', 'load_data' => \$loadData));
		if (empty(\$form)) 
		{
			return false;
		}
		return \$form;
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_".$this->cname."/models/forms/".$this->cname.".js';
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		\$data = JFactory::getApplication()->getUserState('com_".$this->cname.".edit.".$this->cname.".data', array());
		if (empty(\$data)) 
		{
			\$data = \$this->getItem();
		}
		return \$data;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save(\$data)
	{
		// Alter the title for save as copy
		if (JRequest::getVar('task') == 'save2copy') {
			list(\$title,\$alias) = \$this->generateNewTitle(\$data['alias'], \$data['title']);
			\$data['title']	= \$title;
			\$data['alias']	= \$alias;
		}
		
		\$result = parent::save(\$data);
		return \$result;
	}
	
	/**
	 * Method to change the title & alias.
	 *
	 * @param	int     The value of the parent category ID.
	 * @param   sting   The value of the category alias.
	 * @param   sting   The value of the category title.
	 *
	 * @return	array   Contains title and alias.
	 * @since	1.7
	 */
	function generateNewTitle(&\$alias, &\$title)
	{
		// Alter the title & alias
		\$table = \$this->getTable();
		if (\$table->load(array('alias'=>\$alias))) {
			\$m = null;
			if (preg_match('#-(\d+)\$#', \$alias, \$m)) {
				\$alias = preg_replace('#-(\d+)\$#', '-'.(\$m[1] + 1).'', \$alias);
			} else {
				\$alias .= '-2';
			}
			if (preg_match('#\((\d+)\)\$#', \$title, \$m)) {
				\$title = preg_replace('#\(\d+\)\$#', '('.(\$m[1] + 1).')', \$title);
			} else {
				\$title .= ' (2)';
			}
		}

		return array(\$title, \$alias);
	}
	
	".($this->formData['published']==1?"
	function published(&\$pks, \$value = 1)
	{
		// Initialise variables.
		
		\$dispatcher	= JDispatcher::getInstance();
		\$user		= JFactory::getUser();
        // Check if I am a Super Admin
		\$iAmSuperAdmin	=\$user->authoriseInCustom('core.admin');
		\$table		= \$this->getTable();
		\$pks		= (array) \$pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach (\$pks as \$i => \$pk)
		{
			if (\$table->load(\$pk)) {
				\$old	= \$table->getProperties();
				\$allow	= \$user->authoriseInCustom('core.edit.state', 'com_".$this->cname."');
				// Don't allow non-super-admin to delete a super admin
				\$allow = (!\$iAmSuperAdmin && JAccess::check(\$pk, 'core.admin')) ? false : \$allow;

				// Prepare the logout options.
				\$options = array(
					'clientid' => array(0, 1)
				);

				if (\$allow) {
					// Skip changing of same state
					if (\$table->published == \$value) {
						unset(\$pks[\$i]);
						continue;
					}

					\$table->published = (int) \$value;

					// Allow an exception to be thrown.
					try
					{
						if (!\$table->check()) {
							\$this->setError(\$table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						\$result = \$dispatcher->trigger('onUserBeforeSave', array(\$old, false, \$table->getProperties()));
						if (in_array(false, \$result, true)) {
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!\$table->store()) {
							\$this->setError(\$table->getError());
							return false;
						}

						// Trigger the onAftereStoreUser event
						\$dispatcher->trigger('onUserAfterSave', array(\$table->getProperties(), false, true, null));
					}
					catch (Exception \$e)
					{
						\$this->setError(\$e->getMessage());

						return false;
					}
				}
				else {
					// Prune items that you can't change.
					unset(\$pks[\$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}
	":"")."
	
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//models/xxxs.php
		$filePath = 'components/com_'.$this->cname.'/models/'.$this->cname.'s.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * ".$this->comNameUp."List Model
 */
class ".$this->comNameUp."Model".$this->comNameUp."s extends JModelList
{

	public function __construct(\$config = array())
	{
		if (empty(\$config['filter_fields'])) {
			\$config['filter_fields'] = array(
				'category_title',
				".$this->modelsGetSQLStatus()."
				
			);
		}

		parent::__construct(\$config);
	}
	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState(\$ordering = null, \$direction = null)
	{
		// Initialise variables.
		\$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if (\$layout = JRequest::getVar('layout')) {
			\$this->context .= '.'.\$layout;
		}

		\$search = \$this->getUserStateFromRequest(\$this->context.'.filter.search', 'filter_search');
		\$this->setState('filter.search', \$search);

		\$published = \$this->getUserStateFromRequest(\$this->context.'.filter.published', 'filter_published', '');
		\$this->setState('filter.published', \$published);

		\$categoryId = \$this->getUserStateFromRequest(\$this->context.'.filter.category_id', 'filter_category_id');
		\$this->setState('filter.category_id', \$categoryId);

		// List state information.
		parent::populateState('a.ordering', 'asc'); // 手动修改排序规则 默认为id
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		\$db = JFactory::getDBO();
		\$query = \$db->getQuery(true);
		\$query->select(
			\$this->getState(
				'list.select',
				'".$this->modelsGetSQLStatus(1)."'
			)
		);
		\$query->from('#__".$this->cname." AS a');
		
		// Join over the categories.
		\$query->select('c.title AS category_title');
		\$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by published state
		\$published = \$this->getState('filter.published');

		
		if (is_numeric(\$published)) {
			\$query->where('a.published = ' . (int) \$published);
		}
		else if (\$published === '') {
			\$query->where('(a.published = 0 OR a.published = 1)');
		}
		
		// Filter by a single or group of categories.
		\$categoryId = \$this->getState('filter.category_id');
				
		if (is_numeric(\$categoryId)) {
			\$query->where('a.catid = '.(int) \$categoryId);
		}
		else if (is_array(\$categoryId)) {
			JArrayHelper::toInteger(\$categoryId);
			\$categoryId = implode(',', \$categoryId);
			\$query->where('a.catid IN ('.\$categoryId.')');
		}
		
		// Filter by search in name.
		\$search = \$this->getState('filter.search');
		if (!empty(\$search)) {
			if (stripos(\$search, 'id:') === 0) {
				\$query->where('a.id = '.(int) substr(\$search, 3));
			}
			else {
				\$search = \$db->Quote('%'.\$db->getEscaped(\$search, true).'%');
				\$query->where('(a.title LIKE '.\$search.')');// 注意手动修改搜索目标 title 为默认，如果无 title 字段则会出错
			}
		}
		
		
		// Add the list ordering clause.
		\$orderCol	= \$this->state->get('list.ordering');
		\$orderDirn	= \$this->state->get('list.direction');
		if (\$orderCol == 'a.ordering' || \$orderCol == 'category_title') {
			\$orderCol = 'category_title '.\$orderDirn.', a.ordering';
		}
		\$query->order(\$db->getEscaped(\$orderCol.' '.\$orderDirn));
		return \$query;
	}
}

";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//models/rules/xxxs.php
		$filePath = 'components/com_'.$this->cname.'/models/rules/title.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla formrule library
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleGreeting extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 * @since	1.6
	 */
	protected \$regex = '^[^0-9]+\$';
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//models/forms/xxx.js
		$filePath = 'components/com_'.$this->cname.'/models/forms/'.$this->cname.'.js';
		$code = "
window.addEvent('domready', function() {
	document.formvalidator.setHandler('title',
		function (value) {
			regex=/^[^0-9]+\$/;
			return regex.test(value);
	});
});

";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//models/forms/xxx.xml
		$filePath = 'components/com_'.$this->cname.'/models/forms/'.$this->cname.'.xml';
		$code = '<?xml version="1.0" encoding="utf-8"?>
<form
	addrulepath="/administrator/components/com_'.$this->cname.'/models/rules"
>
	<fieldset name="details">
		<field
			name="id"
			type="hidden"
		/>
		<field
			name="title"
			type="text"
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_TITLE_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_TITLE_DESC"
			size="40"
			class="inputbox validate-title"
			
			required="true"
			default=""
		/>
		<field
			name="catid"
			type="category"
			extension="com_'.$this->cname.'"
			class="inputbox"
			default=""
			label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_LABEL"
			description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_DESC"
			required="true"
		>
			<option value="0">JOPTION_SELECT_CATEGORY</option>
		</field>
	</fieldset>
	<fields name="params">
		<fieldset
			name="params"
			label="JGLOBAL_FIELDSET_DISPLAY_OPTIONS"
		>
			<field
				name="show_category"
				type="list"
				label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_LABEL"
				description="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_DESC"
				default=""
			>
				<option value="">JGLOBAL_USE_GLOBAL</option>
				<option value="0">JHIDE</option>
				<option value="1">JSHOW</option>
			</field>
		</fieldset>
	</fields>
</form>
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//models/fields/xxx.php
		$filePath = 'components/com_'.$this->cname.'/models/fields/'.$this->cname.'.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * ".$this->comNameUp." Form Field class for the ".$this->comNameUp." component
 */
class JFormField".$this->comNameUp." extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected \$type = '".$this->comNameUp."';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		\$db = JFactory::getDBO();
		\$query = new JDatabaseQuery;
		\$query->select('#__".$this->cname.".id as id,title,#__categories.title as category,catid');
		\$query->from('#__".$this->cname."');
		\$query->leftJoin('#__categories on catid=#__categories.id');
		\$db->setQuery((string)\$query);
		\$messages = \$db->loadObjectList();
		\$options = array();
		if (\$messages)
		{
			foreach(\$messages as \$message) 
			{
				\$options[] = JHtml::_('select.option', \$message->id, \$message->title . (\$message->catid ? ' (' . \$message->category . ')' : ''));
			}
		}
		\$options = array_merge(parent::getOptions(), \$options);
		return \$options;
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	
	protected function codeOfTables()
	{
		//tables/xxx.php
		$filePath = 'components/com_'.$this->cname.'/tables/'.$this->cname.'.php';
		$code = "
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * ".$this->comNameUp."Table".$this->comNameUp." Table class
 */
class ".$this->comNameUp."Table".$this->comNameUp." extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&\$db) 
	{
		parent::__construct('#__".$this->cname."', 'id', \$db);
	}
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind(\$array, \$ignore = '') 
	{
		if (isset(\$array['params']) && is_array(\$array['params'])) 
		{
			// Convert the params field to a string.
			\$parameter = new JRegistry;
			\$parameter->loadArray(\$array['params']);
			\$array['params'] = (string)\$parameter;
		}
		
		if (isset(\$array['metadata']) && is_array(\$array['metadata'])) 
		{
			// Convert the params field to a string.
			\$metadatas = new JRegistry;
			\$metadatas->loadArray(\$array['metadata']);
			\$array['metadata'] = (string)\$metadatas;
		}
		return parent::bind(\$array, \$ignore);
	}
 
	/**
	 * Overloaded load function
	 *
	 * @param       int \$pk primary key
	 * @param       boolean \$reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load(\$pk = null, \$reset = true) 
	{
		if (parent::load(\$pk, \$reset)) 
		{
			// Convert the params field to a registry.
			\$params = new JRegistry;
			\$params->loadJSON(\$this->params);
			\$this->params = \$params;
			
			\$metadata = new JRegistry;
			\$metadata->loadString(\$this->metadata);
			\$this->metadata = \$metadata->toArray();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Override check function
	 *
	 * @return  boolean
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{
		// Check for a title.
		if (trim(\$this->title) == '') {
			\$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_DETAIL'));
			return false;
		}
		\$this->alias = trim(\$this->alias);
		if (empty(\$this->alias)) {
			\$this->alias = \$this->title;
		}

		\$this->alias = JApplication::stringURLSafe(\$this->alias);
		if (trim(str_replace('-', '', \$this->alias)) == '') {
			\$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetName()
	{
		\$k = \$this->_tbl_key;
		return 'com_".$this->cname.".message.'.(int) \$this->\$k;
	}
 
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle()
	{
		return \$this->title;
	}
 
	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId()
	{
		\$asset = JTable::getInstance('Asset');
		\$asset->loadByName('com_".$this->cname."');
		return \$asset->id;
	}
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	
	protected function codeOfViews()
	{
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'/submitbutton.js';
		$code = "
Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = \$\$('form.form-validate');
			for (var i=0;i<forms.length;i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}
 
		if (isValid)
		{
			Joomla.submitform(task);
			return true;
		}
		else
		{
			alert(Joomla.JText._('COM_".$this->comNameLag."_".$this->comNameLag."_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'/view.html.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * ".$this->comNameUp." View
 */
class ".$this->comNameUp."View".$this->comNameUp." extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display(\$tpl = null) 
	{
		// get the Data
		\$form = \$this->get('Form');
		\$item = \$this->get('Item');
		\$script = \$this->get('Script');
 
		// Check for errors.
		if (count(\$errors = \$this->get('Errors'))) 
		{
			JError::raiseError(500, implode('\<br \/\>', \$errors));
			return false;
		}
		// Assign the Data
		\$this->form = \$form;
		\$this->item = \$item;
		\$this->script = \$script;
 
		// Set the toolbar
		\$this->addToolBar();
 
		// Display the template
		parent::display(\$tpl);
 
		// Set the document
		\$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);
		\$user = JFactory::getUser();
		\$userId = \$user->id;
		\$isNew = \$this->item->id == 0;
		\$canDo = ".$this->comNameUp."Helper::getActions(\$this->item->id);
		JToolBarHelper::title(\$isNew ? JText::_('COM_".$this->comNameLag."_MANAGER_".$this->comNameLag."_NEW') : JText::_('COM_".$this->comNameLag."_MANAGER_".$this->comNameLag."_EDIT'), '".$this->cname."');
		// Built the actions for new and existing records.
		if (\$isNew) 
		{
			// For new records, check the create permission.
			if (\$canDo->get('core.create')) 
			{
				JToolBarHelper::apply('".$this->cname.".apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('".$this->cname.".save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('".$this->cname.".save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('".$this->cname.".cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if (\$canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('".$this->cname.".apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('".$this->cname.".save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see if we can return to make a new one.
				if (\$canDo->get('core.create')) 
				{
					JToolBarHelper::custom('".$this->cname.".save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if (\$canDo->get('core.create')) 
			{
				JToolBarHelper::custom('".$this->cname.".save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('".$this->cname.".cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		\$isNew = \$this->item->id == 0;
		\$document = JFactory::getDocument();
		\$document->setTitle(\$isNew ? JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_CREATING') : JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_EDITING'));
		\$document->addScript(JURI::root() . \$this->script);
		\$document->addScript(JURI::root() . \"/administrator/components/com_".$this->cname."/views/".$this->cname."/submitbutton.js\");
		JText::script('COM_".$this->comNameLag."_".$this->comNameLag."_ERROR_UNACCEPTABLE');
	}
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'/tmpl/edit_metadata.php';
		$code = "
<?php
/**
 * @version		\$Id: edit_metadata.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

defined('_JEXEC') or die;
?>
<ul class='adminformlist'>
	<li><?php echo \$this->form->getLabel('metadesc'); ?>
	<?php echo \$this->form->getInput('metadesc'); ?></li>

	<li><?php echo \$this->form->getLabel('metakey'); ?>
	<?php echo \$this->form->getInput('metakey'); ?></li>

	<?php foreach(\$this->form->getGroup('metadata') as \$field): ?>
		<?php if (\$field->hidden): ?>
			<li><?php echo \$field->input; ?></li>
		<?php else: ?>
			<li><?php echo \$field->label; ?>
			<?php echo \$field->input; ?></li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'/tmpl/edit.php';
		$code = "
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
\$params = \$this->form->getFieldsets('params');
?>
<form action='<?php echo JRoute::_('index.php?option=com_".$this->cname."&layout=edit&id='.(int) \$this->item->id); ?>' method='post' name='adminForm' id='".$this->cname."-form' class='form-validate'>
 
	<div class='width-60 fltlft'>
		<fieldset class='adminform'>
			<legend><?php echo JText::_( 'COM_".$this->comNameLag."_".$this->comNameLag."_DETAILS' ); ?></legend>
			<ul class='adminformlist'>
<?php
foreach(\$this->form->getFieldset('details') as \$field):
	echo \$field->label;
	if (strtolower(\$field->type) == 'editor') {
		echo '<div class=\"clr\"></div>';
	}
	echo \$field->input;
endforeach;
?>
			</ul>
	</div>
 
	<div class='width-40 fltrt'>
		<?php echo JHtml::_('sliders.start', '".$this->cname."-slider'); ?>
<?php foreach (\$params as \$name => \$fieldset): ?>
		<?php echo JHtml::_('sliders.panel', JText::_(\$fieldset->label), \$name.'-params');?>
	<?php if (isset(\$fieldset->description) && trim(\$fieldset->description)): ?>
		<p class='tip'><?php echo \$this->escape(JText::_(\$fieldset->description));?></p>
	<?php endif;?>
		<fieldset class='panelform' >
			<ul class='adminformlist'>
	<?php foreach (\$this->form->getFieldset(\$name) as \$field) : ?>
				<li><?php echo \$field->label; ?><?php echo \$field->input; ?></li>
	<?php endforeach; ?>
			</ul>
		</fieldset>
<?php endforeach; ?>
		
		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
		<fieldset class='panelform'>
			<?php echo \$this->loadTemplate('metadata'); ?>
		</fieldset>
		
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
 
	<div>
		<input type='hidden' name='task' value='".$this->cname.".edit' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
	
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/view.html.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * ".$this->comNameUp."s View
 */
class ".$this->comNameUp."View".$this->comNameUp."s extends JView
{
	protected \$items;
	protected \$pagination;
	protected \$state;
	/**
	 * ".$this->comNameUp."s view display method
	 * @return void
	 */
	function display(\$tpl = null) 
	{
		// Get data from the model
		\$this->items 		= \$this->get('Items');
		\$this->pagination 	= \$this->get('Pagination');
		\$this->state		= \$this->get('State');
		// Check for errors.
		if (count(\$errors =\$this->get('Errors'))) 
		{
			JError::raiseError(500, implode('\\n', \$errors));
			return false;
		}
		
		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach (\$this->items as &\$item) {
			\$item->order_up = true;
			\$item->order_dn = true;
		}
 
		// Set the toolbar
		\$this->addToolBar();
		// Display the template
		parent::display(\$tpl);
		// Set the document
		\$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		\$canDo = ".$this->comNameUp."Helper::getActions();
		JToolBarHelper::title(JText::_('COM_".$this->comNameLag."_MANAGER_".$this->comNameLag."S'), '".$this->cname."');
		if ($canDo->get('core.expexcel')) {
			JToolBarHelper::exportList('', '".$this->cname."s.expexcel', 'JTOOLBAR_EXPORTEXCEL');
		}
		if (\$canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('".$this->cname.".add', 'JTOOLBAR_NEW');
		}
		if (\$canDo->get('core.edit')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('".$this->cname."s.topthis','topthis','','JTOOLBAR_TOPTHIS');
			JToolBarHelper::divider();
			JToolBarHelper::editList('".$this->cname.".edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
			JToolBarHelper::publish('".$this->cname.".published', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('".$this->cname.".unpublished', 'JTOOLBAR_UNPUBLISH', true);
		}

		if (\$canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', '".$this->cname."s.delete', 'JTOOLBAR_DELETE');
		}
		if (\$canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_".$this->cname."');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		\$document = JFactory::getDocument();
		\$document->setTitle(JText::_('COM_".$this->comNameLag."_ADMINISTRATION'));
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default.php';
		$code = "<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

\$user		= JFactory::getUser();
\$userId		= \$user->get('id');
\$listOrder	= \$this->escape(\$this->state->get('list.ordering'));
\$listDirn	= \$this->escape(\$this->state->get('list.direction'));
\$canOrder	= \$user->authoriseInCustom('core.edit.state', 'com_newsfeeds.category');
\$saveOrder	= \$listOrder == 'a.ordering';

?>
<form action='<?php echo JRoute::_('index.php?option=com_".$this->cname."'); ?>' method='post' name='adminForm' >
	<fieldset id='filter-bar'>
		<div class='filter-search fltlft'>
			<label class='filter-search-lbl' for='filter_search'><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type='text' name='filter_search' id='filter_search' value='<?php echo \$this->escape(\$this->state->get('filter.search')); ?>' title='<?php echo JText::_('COM_CONTACT_SEARCH_IN_NAME'); ?>' />
			<button type='submit'><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type='button' onclick='document.id(\"filter_search\").value=\"\";this.form.submit();'><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
		<a id='save-list-ordering' class='custome-button-red fltrt' href='index.php?option=<?php echo JRequest::getVar('option') ?>&task=<?php echo substr(JRequest::getVar('option'),4)?>s.saveOrderByOrder&orderfield=<?php echo \$this->state->get('list.ordering'); ?>&orderby=<?php echo \$this->state->get('list.direction'); ?>&filter_category_id=<?php echo JRequest::getVar('filter_category_id')?JRequest::getVar('filter_category_id'):0?>'>
			保存当前排序
		</a>
		
		<div class='filter-select fltrt'>

			<select name='filter_published' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', \$this->state->get('filter.published'), true);?>
			</select>

			<select name='filter_category_id' class='inputbox' onchange='this.form.submit()'>
				<option value=''><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_".$this->cname."'), 'value', 'text', \$this->state->get('filter.category_id'));?>
			</select>

           
		</div>
	</fieldset>
	<div class='clr'> </div>
	<table class='adminlist'>
		".$this->viewsListDisplayGetHead()."
		".$this->viewsListDisplayGetFoot()."
		".$this->viewsListDisplayGetBody()."
	</table>
	<div>
		<input type='hidden' name='task' value='' />
		<input type='hidden' name='boxchecked' value='0' />
		<input type='hidden' name='filter_order' value='<?php echo \$listOrder; ?>' />
		<input type='hidden' name='filter_order_Dir' value='<?php echo \$listDirn; ?>' />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
";

		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_body.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach(\$this->items as \$i => \$item): ?>
	<tr class='row<?php echo \$i % 2; ?>'>
		<td>
			<?php echo \$item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', \$i, \$item->id); ?>
		</td>
		<td>
			<?php echo \$item->title; ?>
		</td>
	</tr>
<?php endforeach; ?>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_foot.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<td colspan='3'><?php echo \$this->pagination->getListFooter(); ?></td>
</tr>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default_head.php';
		$code = "
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width='5'>
		<?php echo JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_HEADING_ID'); ?>
	</th>
	<th width='20'>
		<input type='checkbox' name='toggle' value='' onclick='checkAll(<?php echo count(\$this->items); ?>);' />
	</th>			
	<th>
		<?php echo JText::_('COM_".$this->comNameLag."_".$this->comNameLag."_HEADING_TITLE'); ?>
	</th>
</tr>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	
	protected function codeOfLanguage()
	{
		$filePath = 'language/zh-CN/zh-CN.com_'.$this->cname.'.ini';
		$code = 'COM_'.$this->comNameLag.'="'.$this->comName.'"
COM_'.$this->comNameLag.'_ADMINISTRATION="'.$this->comName.' - 管理"
COM_'.$this->comNameLag.'_ADMINISTRATION_CATEGORIES="'.$this->comName.' - 分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_CREATING="'.$this->comName.' - 创建中"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_DETAILS="Details"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_EDITING="'.$this->comName.' - 编辑中"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_ERROR_UNACCEPTABLE="有一些不合法的输入"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_DESC="属于某个分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_CATID_LABEL="分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_TITLE_DESC="标题名称"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_TITLE_LABEL="标题"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_LABEL="显示分类"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_FIELD_SHOW_CATEGORY_DESC="如果设置显示，则会在记录中显示分类信息"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_HEADING_TITLE="标题"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_HEADING_ID="Id"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'_EDIT="'.$this->comName.' 管理: 编辑"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'_NEW="'.$this->comName.' 管理: 新建"
COM_'.$this->comNameLag.'_MANAGER_'.$this->comNameLag.'S="'.$this->comName.' 管理"
COM_'.$this->comNameLag.'_N_ITEMS_DELETED_1="一条记录已删除"
COM_'.$this->comNameLag.'_N_ITEMS_DELETED_MORE="%d 条记录已删除"
COM_'.$this->comNameLag.'_SUBMENU_MESSAGES="消息"
COM_'.$this->comNameLag.'_SUBMENU_CATEGORIES="分类"
COM_'.$this->comNameLag.'_CONFIGURATION="'.$this->comName.' 配置"
COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_LABEL="标题配置"
COM_'.$this->comNameLag.'_CONFIG_TITLE_SETTINGS_DESC="配置将默认应用到所有记录"';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = 'language/zh-CN/zh-CN.com_'.$this->cname.'.sys.ini';
		$code = 'COM_'.$this->comNameLag.'="'.$this->comName.'"
COM_'.$this->comNameLag.'_DESCRIPTION="'.$this->comName.'的描述"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_DESC="该视图显示一个选择的记录"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_TITLE="'.$this->comName.'"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_DESC="该视图显示'.$this->comName.'列表"
COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_TITLE="'.$this->comName.'列表"
COM_'.$this->comNameLag.'_INSTALL_TEXT="'.$this->comName.' 安装脚本"
COM_'.$this->comNameLag.'_MENU="'.$this->comName.'"
COM_'.$this->comNameLag.'_POSTFLIGHT_DISCOVER_INSTALL_TEXT="'.$this->comName.' 已发现安装脚本"
COM_'.$this->comNameLag.'_POSTFLIGHT_INSTALL_TEXT="'.$this->comName.' 已安装脚本"
COM_'.$this->comNameLag.'_POSTFLIGHT_UNINSTALL_TEXT="'.$this->comName.' 已卸载脚本"
COM_'.$this->comNameLag.'_POSTFLIGHT_UPDATE_TEXT="'.$this->comName.' 已更新脚本"
COM_'.$this->comNameLag.'_PREFLIGHT_DISCOVER_INSTALL_TEXT="'.$this->comName.'发现安装脚本"
COM_'.$this->comNameLag.'_PREFLIGHT_INSTALL_TEXT="'.$this->comName.' 准备安装脚本"
COM_'.$this->comNameLag.'_PREFLIGHT_UNINSTALL_TEXT="'.$this->comName.' 准备卸载脚本"
COM_'.$this->comNameLag.'_PREFLIGHT_UPDATE_TEXT="'.$this->comName.' 准备更新脚本"
COM_'.$this->comNameLag.'_UNINSTALL_TEXT="'.$this->comName.' 卸载脚本"
COM_'.$this->comNameLag.'_UPDATE_TEXT="'.$this->comName.' 更新脚本"
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	
	
	protected function writCode($filePath,$code)
	{
		$targetFile = fopen($filePath,'a');
		if($targetFile)
		{
			fwrite($targetFile ,$code);
			fclose($targetFile );
			return true;
		}
		else
		{
			return false;
		}
	}
	protected function replaceCode($filePath,$code)
	{
		if(unlink($filePath))
		{
			if(!$this->writCode($filePath,$code)){
				//抛出异常
			}
		}else{
			//抛出异常
		}
	}
	protected function modelsGetSQLStatus($single=0)
	{
		$str = '';
		if($single)
		{
			for($i=1;$i<=$this->rows;$i++)
			{
				if($this->formData['name'.$i]){
					if($i==$this->rows)
					{
						$str .="a.".$this->formData['name'.$i];
					}
					else
					{
						$str .="a.".$this->formData['name'.$i].",";
					}
				}
			}
		}
		else
		{
			for($i=1;$i<=$this->rows;$i++)
			{
				if($this->formData['name'.$i]){
					$str .="'".$this->formData['name'.$i]."', 'a.".$this->formData['name'.$i]."',
					";
				}
			}
		}
		return $str;
	}
	protected function viewsListDisplayGetBody()
	{
		$str = "<tbody>
		<?php
		\$n = count(\$this->items);
		foreach (\$this->items as \$i => \$item) :
			\$ordering	= \$listOrder == 'a.ordering';
			\$canCreate	= \$user->authoriseInCustom('core.create',		'com_".$this->cname.".category.'.\$item->catid);
			\$canEdit	= \$user->authoriseInCustom('core.edit',			'com_".$this->cname.".category.'.\$item->catid);
			\$canCheckin	= \$user->authoriseInCustom('core.manage',		'com_checkin') || \$item->checked_out == \$userId || \$item->checked_out == 0;
			\$canEditOwn	= \$user->authoriseInCustom('core.edit.own',		'com_".$this->cname.".category.'.\$item->catid) && \$item->created_by == \$userId;
			\$canChange	= \$user->authoriseInCustom('core.edit.state',	'com_".$this->cname.".category.'.\$item->catid) && \$canCheckin;

			\$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_".$this->cname."&task=edit&type=other&id='.\$item->catid);
			\$link = JRoute::_('index.php?option=com_".$this->cname."&view=".$this->cname."&layout=edit&task=".$this->cname.".edit&id='.\$item->id.'&'.JUtility::getToken().'=1&filter_order='.\$listOrder.'&filter_order_Dir='.\$listDirn);
			?>
			<tr class='row<?php echo \$i % 2; ?>'>
				<td class='center'>
					<?php echo JHtml::_('grid.id', \$i, \$item->id); ?>
				</td>
";
		for($i=1;$i<=$this->rows;$i++)
		{
			if($this->formData['display'.$i]){
				if($this->formData['name'.$i]=='ordering') {
					$str .="
				<td class='order'>
					<?php if (\$canChange) : ?>
						<?php if (\$saveOrder) :?>
							<?php if (\$listDirn == 'asc') : ?>
								<span><?php echo \$this->pagination->orderUpIcon(\$i, (\$item->catid == @\$this->items[\$i-1]->catid),'".$this->cname."s.orderup', 'JLIB_HTML_MOVE_UP', \$ordering); ?></span>
								<span><?php echo \$this->pagination->orderDownIcon(\$i, \$n, (\$item->catid == @\$this->items[\$i+1]->catid), '".$this->cname."s.orderdown', 'JLIB_HTML_MOVE_DOWN', \$ordering); ?></span>
							<?php elseif (\$listDirn == 'desc') : ?>
								<span><?php echo \$this->pagination->orderUpIcon(\$i, (\$item->catid == @\$this->items[\$i-1]->catid),'".$this->cname."s.orderdown', 'JLIB_HTML_MOVE_UP', \$ordering); ?></span>
								<span><?php echo \$this->pagination->orderDownIcon(\$i, \$n, (\$item->catid == @\$this->items[\$i+1]->catid), '".$this->cname."s.orderup', 'JLIB_HTML_MOVE_DOWN', \$ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php \$disabled = \$saveOrder ?  '' : 'disabled=\"disabled\"'; ?>
						<input type='text' name='order[]' size='5' value='<?php echo \$item->ordering;?>' <?php echo \$disabled ?> class='text-area-order' />
					<?php else : ?>
						<?php echo \$item->ordering; ?>
					<?php endif; ?>
				</td>";
				} else if($this->formData['name'.$i]=='catid') {
					$str .="
				<td align='center'>
					<?php echo \$item->category_title?\$item->category_title:'未分类'; ?>
				</td>";
				} else if($this->formData['name'.$i]=='published') {
					$str .="
				<td class='center'>
					<?php echo JHtml::_('grid.boolean', \$i, \$item->published, '".$this->cname.".published', '".$this->cname.".unpublished'); ?>
				</td>";
				} else {
					if($i == 2) {
						$str .="
				<td align='center'>
					<a href='<?php echo \$link; ?>'><?php echo \$item->".$this->formData['name'.$i]."; ?></a>
					<a class='top-this' href='index.php?option=com_".$this->cname."&task=".$this->cname."s.topthis&cid=<?php echo \$item->id?>'>置顶</a>
					<p class='smallsub'><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', \$this->escape(\$item->alias));?></p>
				</td>";
					} else {
						$str .="
				<td align='center'>
					<?php echo \$item->".$this->formData['name'.$i]."; ?>
				</td>";
					}
				}
			}
		}
		$str .="
				<td align='center'>
					<?php echo \$item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>";
		return $str;
	}
	
	protected function viewsListDisplayGetFoot()
	{
		$displayColumn = 2;
		for($i==1;$i<=$this->rows;$i++)
		{
			if($this->formData['display'.$i]==1)
			{
				$displayColumn++;
			}
		}
		$str ="<tfoot>
			<tr>
				<td colspan='".$displayColumn."'>
					<?php echo \$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
";
		return $str;
	}
	protected function viewsListDisplayGetHead()
	{
	
		$str = "<thead>
			<tr>
				<th width='1%'>
					<input type='checkbox' name='checkall-toggle' value='' title='<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>' onclick='Joomla.checkAll(this)' />
				</th>";
		for($i=1;$i<=$this->rows;$i++)
		{
			if($this->formData['display'.$i]){
				if($this->formData['name'.$i]!='ordering'){
					$str .="<th>
						<?php echo JHtml::_('grid.sort', 'COM_".$this->comNameLag."_".$this->comNameLag."_HEADING_".strtoupper($this->formData['name'.$i])."', 'a.".$this->formData['name'.$i]."', \$listDirn, \$listOrder); ?>
					</th>";
				}
				else
				{
					$str .="<th width='10%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', \$listDirn, \$listOrder); ?>
					<?php if (\$canOrder && \$saveOrder) :?>
						<?php echo JHtml::_('grid.order',  \$this->items, 'filesave.png', '".$this->cname."s.saveorder'); ?>
					<?php endif; ?>
				</th>";
				}
			}
		}
		$str .= "<th width='1%'>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', \$listDirn, \$listOrder); ?>
				</th>
			</tr>
		</thead>
";
		return $str;
	}
	
	
	/*
		生成前台代码
	*/
	function generateFontEndComponent()
	{
		$folder_url = JPATH_ROOT.'/components/com_'.$this->cname;
	
		$first_floor_folders = array();
		array_push($first_floor_folders, 'controllers');
		array_push($first_floor_folders, 'helpers');
		array_push($first_floor_folders, 'models');
		array_push($first_floor_folders, 'views');
			
		$root_files = array();
		
		array_push($root_files, $folder_url.'/'.$this->cname.'.php');
		array_push($root_files, $folder_url.'/metadata.xml');
		array_push($root_files, $folder_url.'/controller.php');
		array_push($root_files, $folder_url.'/index.html');
		array_push($root_files, $folder_url.'/router.php');
		
		$this->generateFontEndFolders($first_floor_folders,$folder_url);
		$this->generateFiles($root_files);
		$this->generateFontEndCodes();
	}
	
	function generateFontEndFolders($folders,$folder_url)
	{
		@mkdir($folder_url);
		if (file_exists($folder_url)) {
			if (count($folders)) {
				foreach ($folders as $folder) {
					@mkdir($folder_url.'/'.$folder);
					if ($folder == 'controllers') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->cname.'.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'helpers') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/category.php');
						array_push($files, $folder_url.'/'.$folder.'/route.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'models') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/'.$this->cname.'.php');
						array_push($files, $folder_url.'/'.$folder.'/'.$this->cname.'s.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
					} else if ($folder == 'views') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						$views_folders = array();
						array_push($views_folders, $this->cname);
						array_push($views_folders, $this->cname.'s');
						$this->generateFontEndFolders($views_folders,$folder_url.'/'.$folder);
					} else if ($folder == $this->cname) {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/view.html.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						if (!is_dir($folder_url.'/'.$folder.'/tmpl')) {
							@mkdir($folder_url.'/'.$folder.'/tmpl');
						}
						
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/index.html');
						$this->generateFiles($files);
					} else if ($folder == $this->cname.'s') {
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/view.html.php');
						array_push($files, $folder_url.'/'.$folder.'/index.html');
						$this->generateFiles($files);
						
						if (!is_dir($folder_url.'/'.$folder.'/tmpl')) {
							@mkdir($folder_url.'/'.$folder.'/tmpl');
						}
						$files = array();
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default.php');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/default.xml');
						array_push($files, $folder_url.'/'.$folder.'/tmpl/index.html');
						$this->generateFiles($files);
					}
				}
			}
		}
	}
	
	function generateFontEndCodes()
	{
		$this->fontEndCodeOfRoot();
		$this->fontEndCodeOfController();
		$this->fontEndCodeOfHelpers();
		$this->fontEndCodeOfModels();
		$this->fontEndCodeOfViews();
		$this->fontEndCodeOfLanguage();
	}
	
	function fontEndCodeOfRoot()
	{
		//controller.php
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/controller.php';
		$code = "<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * ".$this->comNameUp." Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since 1.5
 */
class ".$this->comNameUp."Controller extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display(\$cachable = false, \$urlparams = false)
	{

		// Initialise variables.
		\$cachable	= true;	// Huh? Why not just put that in the constructor?
		\$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using w_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		\$id		= JRequest::getInt('w_id');
		\$vName	= JRequest::getCmd('view', '".$this->cname."s');
		JRequest::setVar('view', \$vName);

		if (\$user->get('id') ||(\$_SERVER['REQUEST_METHOD'] == 'POST' && \$vName = '".$this->cname."s')) {
			\$cachable = false;
		}

		\$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'filter_order'		=> 'CMD',
			'filter_order_Dir'	=> 'CMD',
			'lang'				=> 'CMD'
		);
		return parent::display(\$cachable,\$safeurlparams);
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		//metadata.xml
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/metadata.xml';
		$code = "<?xml version='1.0' encoding='utf-8'?>
<metadata>
</metadata>";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		//router.php
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/'.$this->cname.'.php';
		$code = "<?php
/**
 * @version		\$Id: ".$this->cname.".php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';

\$controller	= JController::getInstance('".$this->comNameUp."');
\$controller->execute(JRequest::getCmd('task'));
\$controller->redirect();
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/router.php';
		$code = "<?php
/**
 * @version		\$Id: router.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

 /* ".$this->comNameUp." Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since 1.6
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_".$this->cname." component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function ".$this->comNameUp."BuildRoute(&\$query)
{
	\$segments = array();

	// get a menu item based on Itemid or currently active
	\$app		= JFactory::getApplication();
	\$menu		= \$app->getMenu();
	\$params	= \$app->getParams('com_".$this->cname."');
	\$advanced	= \$params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty(\$query['Itemid'])) {
		\$menuItem = \$menu->getActive();
	} else {
		\$menuItem = \$menu->getItem(\$query['Itemid']);
	}

	\$mView	= (empty(\$menuItem->query['view'])) ? null : \$menuItem->query['view'];
	\$mId	= (empty(\$menuItem->query['id'])) ? null : \$menuItem->query['id'];

	if (isset(\$query['view'])) {
		\$view = \$query['view'];
		if (empty(\$query['Itemid'])) {
			\$segments[] = \$query['view'];
		}
		unset(\$query['view']);
	} else {
		\$view = \$mView;
	}

	if (isset(\$view) and (\$view == '".$this->cname."s' or \$view == '".$this->cname."' )) {
		if (isset(\$query['id'])) {
			\$catid = \$query['id'];
		} else {
			\$catid = \$mId;
		}
		\$categories = JCategories::getInstance('".$this->comNameUp."');
		\$".$this->cname."s = \$categories->get(\$catid);
		if(\$".$this->cname."s){
			\$path = \$".$this->cname."s->getPath();
			\$path = array_reverse(\$path);
			
			\$array = array();
			foreach(\$path as \$id) {
				if (\$id) {
					\$aliaslink = explode(':', \$id);
					if (\$view == '".$this->cname."' && \$query['pid']) {
						\$array[] = \$aliaslink[1];
						if(\$query['start']) {
							unset(\$query['start']);
						} else if(\$query['limitstart']==0) {
							unset(\$query['limitstart']);
						}
					} else {
						if(\$query['start']) {
							\$array[] = \$aliaslink[1].'-'.floor(\$query['start']/\$params->get('limit')+1);
							unset(\$query['start']);
						} else if(\$query['limitstart']==0) {
							\$array[] = \$aliaslink[1].'-1';
							unset(\$query['limitstart']);
						} else {
							\$array[] = \$aliaslink[1].'-1';
						}
					}
				}
			}
			\$segments = array_merge(\$segments, array_reverse(\$array));
		}

		if (\$view == '".$this->cname."') {
			if (\$advanced) {
				list(\$tmp, \$pid) = explode(':', \$query['pid'], 2);
			} else {
				\$pid = \$query['pid'];
			}
			if (\$pid) {
				if (\$query['alias']) {
					\$pid .= '-'.\$query['alias'];
				}
				unset(\$query['alias']);
				\$segments[] = \$pid;
			}
		}
		
		unset(\$query['view']);
		unset(\$query['id']);
		unset(\$query['pid']);
	}

	if (isset(\$query['layout'])) {
		if (!empty(\$query['Itemid']) && isset(\$menuItem->query['layout'])) {
			if (\$query['layout'] == \$menuItem->query['layout']) {
				unset(\$query['layout']);
			}
		}
		else {
			if (\$query['layout'] == 'default') {
				unset(\$query['layout']);
			}
		}
	};

	return \$segments;
}
/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function ".$this->comNameUp."ParseRoute(\$segments)
{
	\$vars = array();

	//Get the active menu item.
	\$app	= JFactory::getApplication();
	\$menu	= \$app->getMenu();
	\$item	= \$menu->getActive();
	\$params = \$app->getParams('com_".$this->cname."');
	\$advanced = \$params->get('sef_advanced_link', 0);
	\$seg_last = explode(':',\$segments[count(\$segments)-1]);
	\$seg_last_num = \$seg_last[0];
	
	\$is_tolist = true;
	if(is_numeric(\$seg_last_num) && count(\$segments) >= 2 && \$segments[count(\$segments)-2]) {
		\$is_tolist = false;
		\$segmentStr = str_replace(':','-',\$segments[count(\$segments)-2]);
		\$cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE extension=\"com_".$this->cname."\" and alias =\"'.\$segmentStr.'\" ')->id;
		if (\$cateid) {
			\$vars['id'] = \$cateid;
		}
		
		if (\$vars['id']) {
			\$vars['view'] = '".$this->cname."';

			\$obj = MulanDBUtil::getObjectBySql('SELECT id,catid FROM #__".$this->cname." WHERE id =\"'.\$seg_last_num.'\" ');
			\$vars['pid'] = \$obj ? \$obj->id : 0;
		} else {
			\$is_tolist = true;
		}

		if (!\$vars['id'] || !\$vars['pid']) {
			header('location: '.MulanHtmlUtil::getUrlByAlias('404'));
		}

		return \$vars;
	}
	
	if (\$is_tolist == true) {
		\$segmentsStr = explode(':',\$segments[count(\$segments)-1]);
		\$segmentsStr_num = \$segmentsStr[count(\$segmentsStr)-1];
		array_pop(\$segmentsStr);
		\$segmentsStr_alias = implode('-',\$segmentsStr);
		\$vars['limitstart'] = (\$segmentsStr_num-1)*\$params->get('limit');
		\$cateid = MulanDBUtil::getObjectBySql('SELECT id FROM #__categories WHERE extension=\"com_".$this->cname."\" and alias =\"'.\$segmentsStr_alias.'\" ')->id;
		\$vars['view'] = '".$this->cname."s';
		\$vars['id']	= \$cateid;

		if (!\$vars['id']) {
			header('location: '.MulanHtmlUtil::getUrlByAlias('404'));
		}

		return \$vars;
	}
}";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
	}
	function fontEndCodeOfController()
	{
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/controllers/'.$this->cname.'.php';
		$code = "<?php
/**
 * @version		\$Id: ".$this->cname.".php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since		1.5
 */
class ".$this->comNameUp."Controller".$this->comNameUp." extends JControllerForm
{

}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}

	}
	function fontEndCodeOfHelpers()
	{
		//helpers/category.php
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/helpers/category.php';
		$code = "<?php
/**
 * @version		\$Id: category.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * ".$this->comNameUp." Component ".$this->comNameUp."s Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since 1.6
 */
class ".$this->comNameUp."Categories extends JCategories
{
	public function __construct(\$options = array())
	{
		\$options['table'] = '#__".$this->cname."';
		\$options['extension'] = 'com_".$this->cname."';
		parent::__construct(\$options);
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/helpers/route.php';
		$code = "<?php
/**
 * @version		\$Id: router.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * ".$this->comNameUp." Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since 1.5
 */
abstract class ".$this->comNameUp."HelperRoute
{
	protected static \$lookup;

	/**
	 * @param	int	The route of the ".$this->cname."
	 */
	public static function get".$this->comNameUp."Route(\$id, \$catid)
	{
		\$needles = array(
			'".$this->cname."'  => array((int) \$id)
		);

		//Create the link
		\$link = 'index.php?option=com_".$this->cname."&view=".$this->cname."&pid='. \$id;
		if (\$catid > 1) {
			\$categories = JCategories::getInstance('".$this->comNameUp."');
			\$".$this->cname."s = \$categories->get(\$catid);

			if(\$".$this->cname."s) {
				\$needles['".$this->cname."s'] = array_reverse(\$".$this->cname."s->getPath());
				\$needles['categories'] = \$needles['".$this->cname."s'];
				\$link .= '&id='.\$catid;
			}
		}

		if (\$item = self::_findItem(\$needles)) {
			\$link .= '&Itemid='.\$item;
		}
		else if (\$item = self::_findItem()) {
			\$link .= '&Itemid='.\$item;
		}

		return \$link;
	}

	/**
	 * @param	int		\$id		The id of the ".$this->cname.".
	 * @param	string	\$return	The return page variable.
	 */
	public static function getFormRoute(\$id, \$return = null)
	{
		// Create the link.
		if (\$id) {
			\$link = 'index.php?option=com_".$this->cname."&task=".$this->cname.".edit&w_id='. \$id;
		}
		else {
			\$link = 'index.php?option=com_".$this->cname."&task=".$this->cname.".add&w_id=0';
		}

		if (\$return) {
			\$link .= '&return='.\$return;
		}

		return \$link;
	}

	public static function get".$this->comNameUp."sRoute(\$catid)
	{
		if (\$catid instanceof J".$this->comNameUp."sNode) {
			\$id = \$catid->id;
			\$".$this->cname."s = \$catid;
		}
		else {
			\$id = (int) \$catid;
			\$".$this->cname."s = JCategories::getInstance('".$this->comNameUp."')->get(\$id);
		}

		if (\$id < 1) {
			\$link = '';
		}
		else {
			\$needles = array(
				'".$this->cname."s' => array(\$id)
			);

			if (\$item = self::_findItem(\$needles)) {
				\$link = 'index.php?Itemid='.\$item;
			}
			else {
				//Create the link
				\$link = 'index.php?option=com_".$this->cname."&view=".$this->cname."s&id='.\$id;

				if (\$".$this->cname."s) {
					\$catids = array_reverse(\$".$this->cname."s->getPath());
					\$needles = array(
						'".$this->cname."s' => \$catids,
						'categories' => \$catids
					);

					if (\$item = self::_findItem(\$needles)) {
						\$link .= '&Itemid='.\$item;
					}
					else if (\$item = self::_findItem()) {
						\$link .= '&Itemid='.\$item;
					}
				}
			}
		}

		return \$link;
	}

	protected static function _findItem(\$needles = null)
	{
		\$app		= JFactory::getApplication();
		\$menus		= \$app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::\$lookup === null) {
			self::\$lookup = array();

			\$component	= JComponentHelper::getComponent('com_".$this->cname."');
			\$items		= \$menus->getItems('component_id', \$component->id);
			
			if (\$items) {
				foreach (\$items as \$item)
				{
					if (isset(\$item->query) && isset(\$item->query['view'])) {
						\$view = \$item->query['view'];
	
						if (!isset(self::\$lookup[\$view])) {
							self::\$lookup[\$view] = array();
						}
	
						if (isset(\$item->query['id'])) {
							self::\$lookup[\$view][\$item->query['id']] = \$item->id;
						}
					}
				}
			}
		}

		if (\$needles) {
			foreach (\$needles as \$view => \$ids)
			{
				if (isset(self::\$lookup[\$view])) {
					foreach(\$ids as \$id)
					{
						if (isset(self::\$lookup[\$view][(int)\$id])) {
							return self::\$lookup[\$view][(int)\$id];
						}
					}
				}
			}
		}
		else {
			\$active = \$menus->getActive();
			if (\$active) 
			{
				return \$active->id;
			}
		}

		return null;
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	function fontEndCodeOfModels()
	{
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/models/'.$this->cname.'.php';
		$code = "<?php
/**
 * @version		\$Id: ".$this->cname.".php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access
defined('_JEXEC') or die;


include_once 'components/com_".$this->cname."/models/".$this->cname."s.php';
/**
 * ".$this->comNameUp." Component Model for a ".$this->comNameUp." record
 *
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since		1.5
 */
class ".$this->comNameUp."Model".$this->comNameUp." extends ".$this->comNameUp."Model".$this->comNameUp."s
{
	public static \$cate_array = array();
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected \$_context = 'com_".$this->cname.".".$this->cname."';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		\$app = JFactory::getApplication();
		\$params	= \$app->getParams();
		// Load the object state.
		\$id	= JRequest::getInt('pid');

		\$this->setState('".$this->cname.".id', \$id);
		// Load the parameters.
		\$this->setState('params', \$params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem(\$id = null)
	{

		if (\$this->_item === null)
		{
			\$this->_item = false;

			if (empty(\$id)) {
				\$id = \$this->getState('".$this->cname.".id');
			}

			// Get a level row instance.
			\$table = JTable::getInstance('".$this->comNameUp."', '".$this->comNameUp."Table');

			// Attempt to load the row.
			if (\$table->load(\$id))
			{
				// Check published state.
				if (\$published = \$this->getState('filter.published'))
				{
					if (\$table->state != \$published) {
						return \$this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				\$properties = \$table->getProperties(1);
				\$this->_item = JArrayHelper::toObject(\$properties, 'JObject');
			}
			else if (\$error = \$table->getError()) {
				\$this->setError(\$error);
			}
		}

		return \$this->_item;
	}
	
	public function getPreOne() {
		jimport('mulan.mldb');
		\$id = \$this->getState('".$this->cname.".id');
		\$ordering = \$this->getState('".$this->cname.".ordering');
		if (\$id) {
			\$cateid = JRequest::getVar('id');
			if (\$cateid && !count(self::\$cate_array)) {
				\$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote(\$cateid));
				if (count(\$get_cats)) {
					foreach (\$get_cats as \$cat) {
						array_push(self::\$cate_array,\$cat->cid);
					}
				}
			}
			
			\$ordering = \$ordering ? \$ordering : MulanDBUtil::getObjectbySql('select * from #__".$this->cname." where id='.MulanDBUtil::dbQuote(\$id))->ordering;
			\$this->setState('".$this->cname.".ordering', \$ordering ? \$ordering : 0);
			
			\$wheres = array('published=1','catid in('.implode(',',self::\$cate_array).')');
			\$result = MulanDBUtil::getPreNextPro(-1,'#__".$this->cname."','an',\$wheres,\$ordering,\$id,1);
			if (count(\$result)) {
				return \$result;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	public function getNextOne() {
		jimport('mulan.mldb');
		\$id = \$this->getState('".$this->cname.".id');
		\$ordering = \$this->getState('".$this->cname.".ordering');
		if (\$id) {
			\$cateid = JRequest::getVar('id');
			if (\$cateid && !count(self::\$cate_array)) {
				\$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote(\$cateid));
				if (count(\$get_cats)) {
					foreach (\$get_cats as \$cat) {
						array_push(self::\$cate_array,\$cat->cid);
					}
				}
			}
			
			\$ordering = \$ordering || \$id ? \$ordering : MulanDBUtil::getObjectbySql('select * from #__".$this->cname." where id='.MulanDBUtil::dbQuote(\$id))->ordering;
			\$this->setState('".$this->cname.".ordering', \$ordering || \$id ? \$ordering : 0);
			
			\$wheres = array('published=1','catid in('.implode(',',self::\$cate_array).')');
			\$result = MulanDBUtil::getPreNextPro(1,'#__".$this->cname."','an',\$wheres,\$ordering,\$id,1);
			if (count(\$result)) {
				return \$result;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	public function getStart() {
		jimport('mulan.mldb');
		\$id = \$this->getState('".$this->cname.".id');
		\$ordering = \$this->getState('".$this->cname.".ordering');
		if (\$id) {
			\$cateid = JRequest::getVar('id');
			if (\$cateid && !count(self::\$cate_array)) {
				\$get_cats = MulanDBUtil::getObjectlistBySql('select c1.id as cid from #__categories as c left join #__categories as c1 on c1.lft >= c.lft and c1.rgt <= c.rgt where c.id='.MulanDBUtil::dbQuote(\$cateid));
				if (count(\$get_cats)) {
					foreach (\$get_cats as \$cat) {
						array_push(self::\$cate_array,\$cat->cid);
					}
				}
			}
			
			\$ordering = \$ordering ? \$ordering : MulanDBUtil::getObjectbySql('select * from #__".$this->cname." where id='.MulanDBUtil::dbQuote(\$id))->ordering;
			\$this->setState('".$this->cname.".ordering', \$ordering ? \$ordering : 0);
			
			\$wheres = array('published=1','catid in('.implode(',',self::\$cate_array).')');
			\$sql = MulanDBUtil::getPreNextPro(-1,'#__".$this->cname."','an',\$wheres,\$ordering,\$id,1,false,true,true);
			return MulanDBUtil::getObjectBySql(\$sql)->count;
		} else {
			return null;
		}
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/models/'.$this->cname.'s.php';
		$code = "<?php
/**
 * @version		\$Id: ".$this->cname."s.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * 
 *
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."

 */
class ".$this->comNameUp."Model".$this->comNameUp."s extends JModelList
{


	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct(\$config = array())
	{
		if (empty(\$config['filter_fields'])) {
			\$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
			);
		}
		parent::__construct(\$config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		\$categoryId = \$this->getState('category.id');
		// Create a new query object.
		\$db		= \$this->getDbo();
		\$query	= \$db->getQuery(true);

		// Select required fields from the categories.
		\$query->select(\$this->getState('list.select', 'a.*'));
		\$query->from('`#__".$this->cname."` AS a');
		\$query->where('a.published =  1');
		
		// Filter by category.
		if (\$categoryId) {
			jimport('mulan.mldb');
			\$cat = MulanDBUtil::getObjectBySql('select * from #__categories where published=1 and id='.MulanDBUtil::dbQuote(\$categoryId));
			if (\$cat->id) {
				\$query->select('c.title as ctitle');
				\$query->where('c.lft >= '.\$cat->lft);
				\$query->where('c.rgt <= '.\$cat->rgt);
				\$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
				//Filter by published category
				\$cpublished = \$this->getState('filter.c.published');
				if (is_numeric(\$cpublished)) {
					\$query->where('c.published = '.(int) \$cpublished);
				}
			}
		}

		// Filter by state

		\$state = \$this->getState('filter.state');
		if (is_numeric(\$state)) {
			\$query->where('a.state = '.(int) \$state);
		}

		// Filter by start and end dates.
		\$nullDate = \$db->Quote(\$db->getNullDate());
		\$nowDate = \$db->Quote(JFactory::getDate()->toMySQL());

		// Add the list ordering clause.
		\$query->order(\$db->getEscaped(\$this->getState('list.ordering', 'a.ordering')).' '.\$db->getEscaped(\$this->getState('list.direction', 'ASC')));
		return \$query;
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(\$ordering = null, \$direction = null)
	{
		// Initialise variables.
		\$app		= JFactory::getApplication();
		\$params	= \$app->getParams('com_".$this->cname."');

		// List state information
		\$limit = \$app->getUserStateFromRequest('global.list.limit', 'limit', \$params->get('limit'));
		\$this->setState('list.limit', \$limit);

		\$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		\$this->setState('list.start', \$limitstart);

		\$orderCol	= JRequest::getCmd('filter_order', 'ordering');
		if (!in_array(\$orderCol, \$this->filter_fields)) {
			\$orderCol = 'ordering';
		}
		\$this->setState('list.ordering', \$orderCol);

		\$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper(\$listOrder), array('ASC', 'DESC', ''))) {
			\$listOrder = 'ASC';
		}
		\$this->setState('list.direction', \$listOrder);

		\$cateid = JRequest::getVar('id', 0, '', 'int');
		\$this->setState('category.id', \$cateid);

		// Load the parameters.
		\$this->setState('params', \$params);
	}
	
	public function getCurrentCate()
	{
		\$db		= \$this->getDbo();
		\$query	= \$db->getQuery(true);
		// Select required fields from the categories.
		\$query->select('c.title as ctitle,c.metadesc as metadesc,c.metakey as metakey,c.metadata as metadata');
		\$query->from('`#__categories` AS c');
		\$query->where('c.id = '.\$this->getState('category.id'));
		
		\$list = \$this->_getList(\$query);
		if (count(\$list)) {
			\$ret_item = \$list[0];
			if (is_string(\$ret_item->metadata)) {
				\$ret_item->metadata = json_decode(\$ret_item->metadata);
			}
		}
		return \$ret_item;
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	function fontEndCodeOfViews()
	{
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'/view.html.php';
		$code = "<?php
/**
 * @version		\$Id: view.html.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the ".$this->comNameUp."s component
 *
 * @package		Joomla.Site
 * @subpackage	com_".$this->cname."
 * @since		1.5
 */
class ".$this->comNameUp."View".$this->comNameUp." extends JView
{
	protected \$state;
	protected \$item;
	protected \$pre_item;
	protected \$next_item;
	
	function display(\$tpl = null)
	{
		// Get some data from the models
		\$this->state		= \$this->get('State');
		\$this->item			= \$this->get('Item');
		
		\$this->_prepareDocument();
		
		//\$this->pre_item = \$this->get('PreOne');
		//\$this->next_item = \$this->get('NextOne');
		\$this->start = \$this->get('Start');
		\$this->start = intval(\$this->start/\$this->params->get('limit'))*\$this->params->get('limit');
		
		parent::display(\$tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		\$app	= JFactory::getApplication();
		\$this->params	= \$app->getParams();
		
		\$parentTitle = \$this->params->get('page_title', '');
		\$this->document->setTitle(\$this->item->title.'-'.\$parentTitle .'-'. \$app->getCfg('sitename'));
		
		if (\$this->item->metadesc)
		{
			\$this->document->setDescription(\$this->item->metadesc);
		}
		elseif (!\$this->item->metadesc && \$this->params->get('menu-meta_description'))
		{
			\$this->document->setDescription(\$this->params->get('menu-meta_description'));
		}

		if (\$this->item->metakey)
		{
			\$this->document->setMetadata('keywords', \$this->item->metakey);
		}
		elseif (!\$this->item->metakey && \$this->params->get('menu-meta_keywords'))
		{
			\$this->document->setMetadata('keywords', \$this->params->get('menu-meta_keywords'));
		}

		if (\$this->item->metadata->author) {
			\$this->document->setMetadata('author', \$this->item->metadata->author);
		}
		
		if (\$this->item->metadata->robots) {
			\$this->document->setMetadata('robots', \$this->item->metadata->robots);
		}
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'/tmpl/default.php';
		$code = "<?php
/**
 * @version		\$Id: default.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;
\$itemid = JRequest::getVar('Itemid');
\$cateid = JRequest::getVar('id');
\$start = \$this->start;

\$base = JURI::base();
\$listLink = 'index.php?option=com_".$this->cname."&view=".$this->cname."s&Itemid='.\$itemid.(\$start > 0 ? '&start='.\$start : '&limitstart=0').'&id='.\$cateid;
\$itemLink = 'index.php?option=com_".$this->cname."&view=".$this->cname."&Itemid='.\$itemid.(\$start > 0 ? '&start='.\$start : '&limitstart=0').'&id='.\$cateid.'&pid=';
?>
<div class='".$this->cname."-item-view ".$this->cname."-view'>
详细页ID：<?php echo \$this->item->id?>;<br/>
<a href='<?php echo JRoute::_(\$listLink); ?>'>返回列表</a>
</div>";
		
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'/tmpl/default.xml';
		$code = '<?xml version="1.0" encoding="utf-8"?>
<metadata>
	<layout title="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_TITLE" option="COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_OPTION">
		<help
			key="JHELP_MENUS_MENU_ITEM_'.$this->comNameLag.'_'.$this->comNameLag.'S"
		/>
		<message>
			<![CDATA[COM_'.$this->comNameLag.'_'.$this->comNameLag.'_VIEW_DEFAULT_DESC]]>
		</message>
	</layout>
</metadata>
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'s/view.html.php';
		$code = "<?php
/**
 * @version		\$Id: view.html.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


class ".$this->comNameUp."View".$this->comNameUp."s extends JView
{

	protected \$state;
	protected \$items;
	protected \$pagination;
	protected \$currentCate;
	
	function display(\$tpl = null)
	{
		// Get some data from the models
		\$state		= \$this->get('State');
		\$items		= \$this->get('Items');
		\$pagination	= \$this->get('Pagination');
		\$this->currentCate = \$this->get('CurrentCate');
		// Check for errors.
		if (count(\$errors = \$this->get('Errors'))) {
			JError::raiseError(500, implode(\"\\n\", \$errors));
			return false;
		}
		
		// Prepare the data.
		// Compute the  slug & link url.
		for (\$i = 0, \$n = count(\$items); \$i < \$n; \$i++)
		{
			\$item		= &\$items[\$i];
			\$item->slug	= \$item->alias ? (\$item->id.':'.\$item->alias) : \$item->id;
		}
		
		\$this->assignRef('state',		\$state);
		\$this->assignRef('items',		\$items);
		\$this->assignRef('pagination',	\$pagination);

		\$this->_prepareDocument();

		parent::display(\$tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		\$app		= JFactory::getApplication();
		\$this->params	= \$app->getParams();
		\$menus		= \$app->getMenu();
		\$pathway	= \$app->getPathway();
		\$title 		= null;
		
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and ".$this->cname."s id will match
		\$active	= \$app->getMenu()->getActive();
		if (isset(\$active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			\$this->setLayout(\$active->query['layout']);
		}
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		\$menu = \$menus->getActive();
		
		\$title = \$this->currentCate->ctitle ? \$this->currentCate->ctitle : JText::_('COM_".$this->comNameLag."_DEFAULT_PAGE_TITLE');
		\$parentTitle = \$this->params->get('page_title', \$menu->title);
		\$this->document->setTitle(\$title.'-'.\$parentTitle .'-'. \$app->getCfg('sitename'));
		
		if (\$this->currentCate->metadesc)
		{
			\$this->document->setDescription(\$this->currentCate->metadesc);
		}
		elseif (!\$this->currentCate->metadesc && \$this->params->get('menu-meta_description'))
		{
			\$this->document->setDescription(\$this->params->get('menu-meta_description'));
		}

		if (\$this->currentCate->metakey)
		{
			\$this->document->setMetadata('keywords', \$this->currentCate->metakey);
		}
		elseif (!\$this->currentCate->metakey && \$this->params->get('menu-meta_keywords'))
		{
			\$this->document->setMetadata('keywords', \$this->params->get('menu-meta_keywords'));
		}

		if (\$this->currentCate->metadata->author) {
			\$this->document->setMetadata('author', \$this->currentCate->metadata->author);
		}
		
		if (\$this->currentCate->metadata->robots) {
			\$this->document->setMetadata('robots', \$this->currentCate->metadata->robots);
		}
	}
}
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'s/metadata.xml';
		$code = '<?xml version="1.0" encoding="utf-8"?>
<metadata>
	<view title="'.$this->comNameUp.'s">
		<message><![CDATA[TYPE'.$this->comNameLag.'SDESC]]></message>
	</view>
</metadata>
';
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default.php';
		$code = "<?php
/**
 * @version		\$Id: default.php ".date("Y-m-d H:i:s")."
 * @subpackage	com_".$this->cname."
 * @author		woondroo
 * @email		wengebin@hotmail.com
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
\$itemid = JRequest::getVar('Itemid');
\$id = JRequest::getVar('id');
\$start = JRequest::getVar('limitstart');
\$itemLink = 'index.php?option=com_".$this->cname."&view=".$this->cname."&Itemid='.\$itemid.(\$start > 0 ? '&start='.\$start : '&limitstart=0').'&id='.\$id.'&pid=';
?>
<div class='".$this->cname."-list'>
	<?php
	if (count(\$this->items)) {
		foreach(\$this->items as \$key=>\$o) {
	?>
		<div class='".$this->cname."-item'>
			<a class='title' href='<?php echo JRoute::_(\$itemLink.\$o->id);?>' title='<?php echo \$o->title?>'><?php echo \$o->title?></a>
		</div>
	<?php
		}
	} else {
		echo '<div class=\"no-list\">暂无数据！</div>';
	}
	?>
	<div class='clr'></div>
</div>
<div class='pagination'>
	<?php echo \$this->pagination->getPagesLinks(); ?>
	<div class='clr'></div>
</div>
";
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
		
		$filePath = JPATH_ROOT.'/components/com_'.$this->cname.'/views/'.$this->cname.'s/tmpl/default.xml';
		$code = '<?xml version="1.0" encoding="utf-8"?>
<metadata>
	<layout title="COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_TITLE" option="COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_OPTION">
		<help
			key="JHELP_MENUS_MENU_ITEM_'.$this->comNameLag.'_'.$this->comNameLag.'S"
		/>
		<message>
			<![CDATA[COM_'.$this->comNameLag.'_'.$this->comNameLag.'S_VIEW_DEFAULT_DESC]]>
		</message>
	</layout>

	<!-- Add fields to the request variables for the layout. -->
	<fields name="request">
		<fieldset name="request">
			<field name="id" type="category"
				default="0"
				description="COM_'.$this->comNameLag.'_FIELD_SELECT_CATEGORY_DESC"
				extension="com_'.$this->cname.'"
				label="COM_'.$this->comNameLag.'_FIELD_SELECT_CATEGORY_LABEL"
				required="true"
			/>
		</fieldset>
	</fields>
	
	<fields name="params">
		<fieldset name="basic" label="COM_'.$this->comNameLag.'_'.$this->comNameLag.'_MENU_SETTINGS_LABEL">
			<field name="isopen" type="radio"
				default="0"
				extension="com_'.$this->cname.'"
				label="COM_'.$this->comNameLag.'_FIELD_ISOPEN_LABEL"
				description="COM_'.$this->comNameLag.'_FIELD_ISOPEN_DESC"
				required="true">
				<option
					value="1">JYES</option>
				<option
					value="0">JNO</option>
			</field>
			<field name="parentcid" type="text"
				default="1"
				extension="com_'.$this->cname.'"
				label="COM_'.$this->comNameLag.'_FIELD_PARENTID_LABEL"
				description="COM_'.$this->comNameLag.'_FIELD_PARENTID_DESC"
			/>
		</fieldset>
	</fields>
</metadata>
';
		
		if(!$this->writCode($filePath,$code)){
			//抛出异常
		}
	}
	function fontEndCodeOfLanguage()
	{
	
	}
	
}
