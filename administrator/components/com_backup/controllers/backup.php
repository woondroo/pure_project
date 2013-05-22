<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * Backup Controller
 */
class BackupControllerBackup extends JControllerForm {
	public function backupDB() {
		$result = 'false';
		
		$config = JFactory::getConfig();
		$result = $this->backup_tables($config->get('host'),$config->get('user'),$config->get('password'),$config->get('db'));
		
		echo $result;
		exit;
	}
	
	public function resumeDB() {
		$result = 'false';
		$resume_backup_id = JRequest::getVar('bid',0,'int');
		if ($resume_backup_id) {
			jimport('mulan.mldb');
			$backup = MulanDBUtil::getObjectBySql('select sqlfile from #__backup where id='.$resume_backup_id);
			if ($backup->sqlfile && file_exists(JPATH_ROOT.$backup->sqlfile)) {
				@ini_set('memory_limit','-1');
				$filesql = file_get_contents(JPATH_ROOT.$backup->sqlfile);
				$sqls = explode(";\n",$filesql);
				
				$config = JFactory::getConfig();
				$link = mysql_connect($config->get('host'),$config->get('user'),$config->get('password'));
				mysql_select_db($config->get('db'), $link);
				foreach ($sqls as $sql) {
//					echo $sql.'------breakline-----<br/><br/>';
					if (trim($sql) == '') continue;
					$execute_result = mysql_query($sql);
					if ($execute_result == false) {
						$result = 'false';
						break;
					} else {
						$result = 'true';
					}
				}
				
				if (!$result) $result = 'true';
			}
		}
//		exit;
		$this->checkBackup();
		echo $result;
		exit;
	}
	
	public function downloadSQL() {
		$backup_id = JRequest::getVar('bid',0,'int');
		if ($backup_id) {
			jimport('mulan.mldb');
			$backup = MulanDBUtil::getObjectBySql('select sqlfile from #__backup where id='.$backup_id);
			$file = JPATH_ROOT.$backup->sqlfile;
			$file_name = substr($backup->sqlfile, strrpos($backup->sqlfile, '/')+1);
			$file_size = filesize($file);
			
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: ".$file_size);
			Header("Content-Disposition: attachment; filename=".$file_name);
			
			$sql_file = fopen($file, "r");
			echo fread($sql_file, $file_size);
			fclose($sql_file);
		} else {
			echo '<div style="width:100%;text-align:center;">Sorry! File not found!</div>';
		}
		exit;
	}
	
	public function checkBackup() {
		$to = JRequest::getVar('to');
		
		$backup_base_dir = '/backup';
		$backup_dir = JPATH_ROOT.$backup_base_dir;
		$backup_files = array();
		$backup_files_clear = array();
		if (is_dir($backup_dir)) {
			$backup_folder = opendir($backup_dir);
			while ($file = readdir($backup_folder)) {
				if (strpos($file,'.sql') > 0) {
					$backup_files[] = '\''.$backup_base_dir.'/'.$file.'\'';
					$backup_files_clear[] = $backup_base_dir.'/'.$file;
				}
			}
		}
		
		if (count($backup_files)) {
			jimport('mulan.mldb');
			$backed = MulanDBUtil::getObjectlistBySql('select sqlfile from #__backup where sqlfile in('.implode(',',$backup_files).')');
			$backed_files = array();
			if (count($backed)) {
				foreach ($backed as $bd) {
					$backed_files[] = $bd->sqlfile;
				}
			}
			foreach ($backup_files_clear as $b) {
				if (!in_array($b, $backed_files)) {
					MulanDBUtil::executeSql('insert into #__backup values(null,\''.$b.'\',\''.date("Y-m-d H:i:s",filectime(JPATH_ROOT.$b)).'\',137,\'\',\'\',\'\',\'\')');
				}
			}
		}
		
		if ($to) $this->setRedirect('index.php?option=com_backup&view=backups');
	}
	
	/**
	 * 备份数据库或者一个表，通过 tables 可指定
	 */
	public function backup_tables($host, $user, $pass, $name, $tables = '*') {
		try {
			$link = mysql_connect($host, $user, $pass);
			mysql_select_db($name, $link);
	
			//get all of the tables
			if ($tables == '*') {
				$tables = array ();
				$result = mysql_query('SHOW TABLES');
				while ($row = mysql_fetch_row($result)) {
					$tables[] = $row[0];
				}
			} else {
				$tables = is_array($tables) ? $tables : explode(',', $tables);
			}
	
			$sql_max_length = 40*1024;
			$return_length = 0;
			$return_buffer = '';
			$is_buffer_end = true;
			$buffer_row = 0;
	
			//cycle through
			foreach ($tables as $table) {
				$result = mysql_query('SELECT * FROM ' . $table);
				$num_fields = mysql_num_fields($result);
	
				$return .= 'DROP TABLE IF EXISTS `' . $table . '`;';
				$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
				$return .= "\n" . $row2[1] . ";\n";
				
				for ($i = 0; $i < $num_fields; $i++) {
					while ($row = mysql_fetch_row($result)) {
						if ($return_length == 0) {
							$is_buffer_end = false;
							$return_buffer = 'INSERT INTO `' . $table . '` VALUES';
						}
						$temp_str = "(";
						for ($j = 0; $j < $num_fields; $j++) {
							$row[$j] = addslashes($row[$j]);
							$row[$j] = ereg_replace("\\\\u", "\\u", $row[$j]);
							$row[$j] = ereg_replace("\r\n", "\\r\\n", $row[$j]);
//							$row[$j] = ereg_replace("'", "''", $row[$j]);
//							$row[$j] = ereg_replace("\\", "\\\\", $row[$j]);
//							$row[$j] = ereg_replace("\r\n", "\\r\\n", $row[$j]);
							if (isset ($row[$j])) {
								$temp_str .= "'" . $row[$j] . "'";
							} else {
								$temp_str .= "''";
							}
							if ($j < ($num_fields -1)) {
								$temp_str .= ",";
							}
						}
						$temp_str .= ")";
						$temp_str_len = strlen($temp_str);
						if ($return_length + $temp_str_len > $sql_max_length) {
							$return_buffer .= ";\n";
							$return .= $return_buffer;
							
							$return_buffer = 'INSERT INTO `' . $table . '` VALUES';
							$return_buffer .= $temp_str;
							$return_length = strlen($return_buffer);
							$is_buffer_end = false;
							$buffer_row = 1;
						} else {
							$return_buffer .= ($buffer_row > 0 ? ",\n" : "").$temp_str;
							$return_length += $temp_str_len+($buffer_row > 0 ? 3 : 0);
							$buffer_row ++;
						}
					}
				}
				if ($is_buffer_end == false) {
					$is_buffer_end = true;
					$return_buffer .= ";\n";
					$return .= $return_buffer;
					
					$return_buffer = '';
					$return_length = 0;
					$buffer_row = 0;
				}
				$return .= "\n\n";
			}
	
			if (!is_dir(JPATH_ROOT.'/backup')) mkdir(JPATH_ROOT.'/backup');
			
			//save file
			$config = JFactory::getConfig();
			$cur_time = new DateTime('now', new DateTimeZone($config->get('offset')));
			$cur_time = $cur_time->format('YmdHis');
			
			$file_name = '/backup/db-backup-' . $cur_time . '-' . (md5(implode(',', $tables))) . '.sql';
			$handle = fopen(JPATH_ROOT.$file_name, 'w+');
			fwrite($handle, $return);
			fclose($handle);
			
			if (!file_exists(JPATH_ROOT.$file_name) || filesize(JPATH_ROOT.$file_name) <= 0) {
				$file_name = 'false';
			}
		} catch (Exception $e) {
			$file_name = 'false';
		}
		
		return $file_name;
	}
}