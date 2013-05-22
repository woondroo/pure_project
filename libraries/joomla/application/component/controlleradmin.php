<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
jimport('joomla.application.component.controller');
jimport('mulan.mldb');
/**
 * Base class for a Joomla Administrator Controller
 *
 * Controller (controllers are where you put all the actual code) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.1
 */
class JControllerAdmin extends JController
{
	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $option;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $text_prefix;

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $view_list;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('unpublish',	'publish');	// value = 0
		$this->registerTask('archive',		'publish');	// value = 2
		$this->registerTask('trash',		'publish');	// value = -2
		$this->registerTask('report',		'publish');	// value = -3
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');

		// Guess the option as com_NameOfController.
		if (empty($this->option)) {
			$this->option = 'com_'.strtolower($this->getName());
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the list view as the suffix, eg: OptionControllerSuffix.
		if (empty($this->view_list)) {
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
			}
			$this->view_list = strtolower($r[2]);
		}
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}

	/**
	 * Display is not supported by this controller.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining.
	 * @since   11.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		return $this;
	}

	/**
	 * Method to publish a list of taxa
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$session	= JFactory::getSession();
		$registry	= $session->get('registry');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('publish' => 1, 'unpublish' => 0, 'archive'=> 2, 'trash' => -2, 'report'=>-3);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
			else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_PUBLISHED';
				}
				else if ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_UNPUBLISHED';
				}
				else if ($value == 2) {
					$ntext = $this->text_prefix.'_N_ITEMS_ARCHIVED';
				}
				else {
					$ntext = $this->text_prefix.'_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = JRequest::getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$extensionURL, false));
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function reorder()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', null, 'post', 'array');
		$inc	= ($this->getTask() == 'orderup') ? -1 : +1;

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		if ($return === false) {
			// Reorder failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
			return false;
		} else {
			// Reorder succeeded.
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message);
			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks	= JRequest::getVar('cid',	null,	'post',	'array');
		$order	= JRequest::getVar('order',	null,	'post',	'array');
		
		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);
		
//var_dump($pks);
//echo "</br>";
//var_dump($order);
//exit;
		// Get the model
		$model = $this->getModel();
//var_dump($model);exit;
		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
			return false;
		} else
		{
			// Reorder succeeded.
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function checkin()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', null, 'post', 'array');

		$model = $this->getModel();
		$return = $model->checkin($ids);
		if ($return === false) {
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
			return false;
		} else {
			// Checkin succeeded.
			$message =  JText::plural($this->text_prefix.'_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message);
			return true;
		}
	}
	
	/*
		作者：omese
		日期：2011-12-02
		topthis 置顶功能
	*/
	public function topthis()
	{

		$cid = JRequest::getVar('cid');
		$cids = array();
		if(!is_array($cid))
		{
			$cids[] = $cid;
		}
		else $cids = $cid;
		$cids = array_reverse($cids);
		foreach( $cids as $id)
		{
			$option = JRequest::getVar('option');
			$tableName = substr($option,4);
			
			$sql = 'UPDATE #__'.$tableName.' SET ordering=1 WHERE id='.$id;
			if(!MulanDBUtil::executeSql($sql))
			{
				$this->setRedirect('index.php?option='.$option,'执行置顶操作失败！');
			}
			$sql = 'SELECT id,ordering FROM #__'.$tableName.' ORDER BY ordering ASC';
			$itemList = MulanDBUtil::getObjectListBySql($sql);
			$pks	= array();
			$order	= array();
			foreach($itemList as $index=>$o)
			{
				array_push($pks,$o->id);
				if($o->id==$id)
				{
					array_push($order,0);
				}
				else
				{
					array_push($order,$o->ordering);
				}
			}
			JArrayHelper::toInteger($pks);
			JArrayHelper::toInteger($order);
			
			// Get the model
			$model = $this->getModel();
			
			// Save the ordering
			$return = $model->saveorder($pks, $order);
		}
		$this->setRedirect('index.php?option='.$option);
	}
	
	/*
		作者：omese
		日期：2012-02-07
		saveordering 保存当前排序
	*/
	public function saveordering()
	{
		$ids = JRequest::getVar('ids');
		$option = JRequest::getVar('option');
		$tableName = substr($option,4);
		$ids = explode(',',$ids);
		$returnURl = 'index.php?option='.$option;
		$catid = JRequest::getVar("filter_category_id");
		if($catid)
		{
			$returnURL .= '&filter_category_id='.$catid;
		}
		foreach( $ids as $index=>$o)
		{
			if($o>0)
			{
				$sql = 'UPDATE #__'.$tableName.' SET ordering='.($index+1).' WHERE id='.$o;
				if(!MulanDBUtil::executeSql($sql))
				{
					//$this->setRedirect('index.php?option='.$option,'更新失败');
				}
			}
		}
		$this->setRedirect($returnURl,'更新成功');
	}
	
	/**
	 * 
	 * 2012-02-24 wengebin Add!
	 * 
	 * saveOrderByOrder 根据当前组件的排序方式重新排列ordering
	 * 
	 */
	public function saveOrderByOrder()
	{
		$option = JRequest::getVar('option');
		$orderCol = JRequest::getVar('orderfield');
		$orderDirn = JRequest::getVar('orderby');
		
		$tableName = substr($option,4);
		$ids = explode(',',$ids);
		$returnURl = 'index.php?option='.$option;
		$catid = JRequest::getVar("filter_category_id");
		if($catid) {
			$returnURl .= '&filter_category_id='.$catid;
		}
		
		$order_sql = 'id desc';
		if ($orderCol) {
			$order_sql = $orderCol.' '.($orderDirn ? $orderDirn : 'asc');
		}
		if (strpos($orderCol,'order') > -1) {
			$this->setRedirect($returnURl,'保存排序成功');
			return;
		}
		MulanDBUtil::executeSql('set @rownum=0');
		$sql = 'UPDATE #__'.$tableName.' as a SET a.ordering=(@rownum:=@rownum+1) order by '.$order_sql;
		if(!MulanDBUtil::executeSql($sql)) {
			$this->setRedirect($returnURl,'保存排序失败:'.$sql);
		} else {
			$this->setRedirect($returnURl,'保存排序成功');
		}
	}
	
	/**
	 * 
	 * 2012-04-26 wengebin Add!
	 *
	 * expexcel 方法用于导出选中的数据成 Excel 表格并下载
	 * 
	 */
	public function expexcel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to explore from the request.
		$cids = JRequest::getVar('cid', array(), '', 'array');
		
		$option = JRequest::getVar('option');
		$returnURl = 'index.php?option='.$option;
		$catid = JRequest::getVar("filter_category_id");
		if($catid) {
			$returnURl .= '&filter_category_id='.$catid;
		}

		$option = JRequest::getVar('option');
		$tableName = substr($option,4);
		
		$fields = MulanDBUtil::getObjectlistBySql('desc #__'.$tableName);
		
		$app = JFactory::getApplication();
		$cols = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';
		$cols = explode(' ',$cols);
		$no_explore = array('alias','metadesc','metakey','metadata','access','language','params','published',
							'lft','rgt','ordering','password','usertype','asset_id','title_alias','fulltext','state',
							'sectionid','mask','created_by_alias','checked_out','checked_out_time','urls','attribs',
							'parentid','featured','xreference','archived','approved','date','sid');
		if (count($fields)) {
			ob_end_clean();
			jimport('mulan.mlstring');
			jimport('mulan.excel.PHPExcel');
			
			$sitename = $app->getCfg('sitename');
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()
						->setCreator($sitename)
						->setLastModifiedBy($sitename)
						->setTitle(JText::_(strtoupper($option)).' - Office 2007 XLSX 文档')
						->setSubject(JText::_(strtoupper($option)).' - 导出文档')
						->setDescription(ucfirst($tableName).' document for Office 2007 XLSX, generated using PHP classes.')
						->setKeywords(JText::_(strtoupper($option)).' office 2007 openxml php')
						->setCategory(JText::_(strtoupper($option)).' - 导出文档');
			
			$objPHPExcel->setActiveSheetIndex(0);
			$current_col = 0;
			$num_cols = 0;
			$num = 0;
			foreach ($fields as $field) {
				$current_col = $num % 26;
				$num_cols = floor($num / 26);
				if (in_array($field->Field, $no_explore)) continue;
				$num++;
				
				$objActSheet = $objPHPExcel->getActiveSheet();
				$title = $field->Field == 'id' ? 'ID' : JText::_(strtoupper($option).'_'.strtoupper($tableName).'_FIELD_'.strtoupper($field->Field).'_LABEL');
				$get_col = '';
				for ($i = 1; $i <= $num_cols; $i++) {
					$get_col .= $cols[$i];
				}
				$get_col .= $cols[$current_col];
				$width = 15;
				if (strpos($field->Type,'varchar') > -1 || strpos($field->Type,'datetime') > -1 || strpos($field->Field,'time') > -1) {
					$width = 20;
				} else if (strpos($field->Field,'email') > -1) {
					$width = 30;
				} else if (strpos($field->Type,'text') > -1) {
					$width = 50;
				}
				
				$objActSheet->getColumnDimension($get_col)->setWidth($width);
				$get_col .= '1';
				
				$objActSheet->setCellValue($get_col, $title);
				$objActSheet->getStyle($get_col)->applyFromArray(
						array('fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color'		=> array('rgb' => '969696')
						), 'font' => array(
			                'bold'		=> true,
			                'color'		=> array('rgb' => '000000')
			            ), 'borders' => array(
							'bottom'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array(
									'rgb' => 'DADCDD'
								)
							),
							'top'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array(
									'rgb' => 'DADCDD'
								)
							),
							'left'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array(
									'rgb' => 'DADCDD'
								)
							),
							'right'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array(
									'rgb' => 'DADCDD'
								)
							)
						)
		            )
				);
				$objActSheet->getStyle($get_col)->getAlignment()->applyFromArray(
					array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
					)
				);
			}
			
			$explores = MulanDBUtil::getObjectlistBySql('select * from #__'.$tableName.' where id in('.implode(',',$cids).')');
			if (count($explores)) {
				foreach ($explores as $key=>$exp) {
					$current_col = 0;
					$num_cols = 0;
					$num = 0;
					foreach ($fields as $field) {
						$current_col = $num % 26;
						$num_cols = floor($num / 26);
						if (in_array($field->Field, $no_explore)) continue;
						$num++;
						
						$objActSheet = $objPHPExcel->getActiveSheet();
						$get_col = '';
						for ($i = 1; $i <= $num_cols; $i++) {
							$get_col .= $cols[$i];
						}
						$get_col .= $cols[$current_col].($key+2);
						$get_field = $field->Field;
						
						$val = strip_tags($exp->$get_field);
						if (strpos('sex', $field->Field) > -1) {
							$val = $val == 1 ? '先生' : '小姐';
						}
						if (strpos('text', $field->Type) > -1) {
							$val = MulanStringUtil::substr_zh(preg_replace('/[\s\v]+/','',$val), 40, '...');
						}
						$objActSheet->setCellValue($get_col, $val);
						if ($key % 2 != 0) {
							$objActSheet->getStyle($get_col)->applyFromArray(
									array('fill' => array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('rgb' => 'F0F0F0')
									), 'borders' => array(
										'bottom'     => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN,
											'color' => array(
												'rgb' => 'DADCDD'
											)
										),
										'top'     => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN,
											'color' => array(
												'rgb' => 'DADCDD'
											)
										),
										'left'     => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN,
											'color' => array(
												'rgb' => 'DADCDD'
											)
										),
										'right'     => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN,
											'color' => array(
												'rgb' => 'DADCDD'
											)
										)
									)
					            )
							);
						}
						$objActSheet->getStyle($get_col)->getAlignment()->applyFromArray(
							array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
							)
						);
					}
				}
			}
			
			$objPHPExcel->getActiveSheet()->setTitle(JText::_(strtoupper($option)));
			$objPHPExcel->setActiveSheetIndex(0);
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.JText::_(strtoupper($option)).' - 导出文档.xlsx"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		} else {
			$this->setRedirect($returnURl,'导出失败');
		}
	}
}
