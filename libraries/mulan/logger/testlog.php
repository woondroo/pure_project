<?php
defined('JPATH_BASE') or die();
jimport('mulan.logger.logger');
class TestLog extends Logger
{
	
	public function __construct()
	{	
		
		$this->baseFilePath .= strtolower(__CLASS__);

		$this->setPath();
	}
	
	public function warnning($customInfo)
	{

		$this->buildInfo(false);
		$this->info .= '';
		$this->customInfo .= $customInfo;
		$this->insert();
	}
}


?>