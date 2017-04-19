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
	
	static function to_utf8($str)
	{
		$encode = mb_detect_encoding($str, array('ASCII','GB2312','GBK','UTF-8'));
		if( $encode == "UTF-8" ) return $str;
	
		return iconv($encode, "UTF-8", $str);
	}
	
	static function to_gbk($str)
	{
		$encode = mb_detect_encoding($str, array('ASCII','GB2312','GBK','UTF-8'));
		if( $encode != "UTF-8" ) return $str;
	
		return iconv($encode, "GB2312", $str);
	}
	
	static function unicode_decode($str)
	{
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
				create_function(
						'$matches',
						'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
				),
				$str);
	}
}
?>