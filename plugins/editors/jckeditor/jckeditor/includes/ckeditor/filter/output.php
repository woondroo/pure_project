<?php 

class JCKOutput
{

 	static function cleanString($str)
  	{
	// remove any duplicate whitespace, and ensure all characters are alphanumeric
     $str = preg_replace(array('/\s+/','/[^A-Za-z0-9_\-]/'), array('-',''), $str);
     // lowercase and trim
     $str = trim(strtolower($str));
     return $str;
    }
	
	static function fixId($id)
	{
		return str_replace('description','decsription_id',$id);
	}
}