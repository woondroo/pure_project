<?php
defined('JPATH_BASE') or die();
jimport('mulan.logger.logger');
class PaymentLog extends Logger
{
	
	public function __construct()
	{	
		$this->baseFilePath .= strtolower(__CLASS__);
		$this->setPath();
	}
	
	public function warnning($operator,$customInfo)
	{

		$this->buildInfo(false);
		$this->info .= $operator.'	';
		$this->customInfo .= $customInfo;
		$this->insert();
	}
}
?>