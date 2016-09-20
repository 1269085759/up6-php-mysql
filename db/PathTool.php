<?php
class PathTool
{
	static function getName($path)
	{		
		$arr = explode("\\",$path);
		$name= $arr[count($arr) - 1];
		return $name;
	}
	
	static function getExtention($path)
	{		
		$parts = pathinfo( $path);
		$ext = $parts["extension"];//ext,jpg,gif,exe
		//$ext = strtolower($ext);
		return $ext;
	}
	
	static function urlencode_safe($txt)
	{
		$txt = str_replace("\\", "/", $txt);//urlencode会将\\解析成\ 
		$txt = urlencode($txt);
		return str_replace("+","%20",$txt);
	}
	
	static function urlencode_path($txt)
	{
		$txt = str_replace("\\", "/", $txt);//urlencode会将\\解析成\
		$txt = str_replace("/", "\\\\", $txt); 
		$txt = urlencode($txt);
		$txt = str_replace("+","%20",$txt);
		return $txt;		
	}
	
	static function combin($p1,$p2)
	{
		$str_len = strlen($p1);//总长度
		//以/结尾
		if($str_len-1 == strrpos($p1, "/") )
		{
			return $p1 . $p2;			
		}
		return $p1 . "/" . $p2;
	}
}
?>