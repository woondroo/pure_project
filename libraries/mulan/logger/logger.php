<?php
defined('JPATH_BASE') or die();
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


class Logger{
	protected $date = null;
	protected $category =null;
	protected $operator = null;
	protected $info = null;
	protected $customInfo = '	|	';
	protected $baseFilePath = 'logs/';
	protected $logFile = null;
	protected $fp = null;
	
	public function __construct()
	{
		
	}
	protected function buildInfo($inner=true)
	{
		$this->info = null;
		$this->customInfo = null;
		$user = &JFactory::getUser();
		$this->date = '['.date('H:i:s').']';
		if($user->username!=null&&$inner)
		{
			$this->operator = $user->username.'-'.$user->id;
			$this->info .= $this->date.'	'.$this->operator.'	';
		}
		else
		{
			$this->info .= $this->date.'	';
		}
	}
	protected function setPath()
	{
		$this->logFile = date('Ymd').'.log';
		$folderDate = date('Ym');
		$this->baseFilePath .= '/'.$folderDate.'/';
		$this->logFile = $this->baseFilePath.$this->logFile;
		JFolder::create($this->baseFilePath);
	}
	protected function insert()
	{

		$this->info .= $this->customInfo;
		$this->writeFile();
	}

	protected  function display()
	{
		
	}

	protected function writeFile()
	{

		$this->fp = fopen($this->logFile,'a');
		if($this->fp)
		{
			$startTime = microtime();
			do{
				$canWrite = flock($this->fp, LOCK_EX);
				if(!$canWrite) usleep(round(rand(0, 100)*1000));
			}
			while ((!$canWrite) && ((microtime()-$startTime)<1000));
			if($canWrite)
			{ 
				fwrite($this->fp,iconv('utf-8','gb2312',$this->info)."\r\n");
				
				flock($this->fp, LOCK_UN);
				if($this->fp){
					fclose($this->fp);
				}
				$this->info = null;
			}
		}
	}
}


?>