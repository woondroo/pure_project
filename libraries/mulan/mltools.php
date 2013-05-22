<?php
defined('JPATH_BASE') or die();

class MulanToolsUtil extends JObject{
	
	public function __construct(){
		// init content here...
	}
	
	/**
	 * 根据素材板的坐标获取素材板上的(x,y)位置坐标
	 *
	 * @param string $pos 截取的字符
	 * @return array $position x/y坐标的位置
	 */
	static function getMaterialPos($pos) {
		$position = array('x'=>0,'y'=>0,'img'=>'');
		
		if (!$pos) return $position;
		$pos_attrs = explode('-',$pos);
		
		$config = JFactory::getConfig();
		$material_board_size = intval($config->get('material_board_size',0));
		$position['img'] = $config->get('material_board_'.strtoupper($pos_attrs[0]));
		$position['x'] = intval(substr($pos_attrs[1],1)) * $material_board_size;
		$position['y'] = intval(substr($pos_attrs[2],1)) * $material_board_size;
		
		return $position;
	}
}
?>