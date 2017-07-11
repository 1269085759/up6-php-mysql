<?php
class PathUuidBuilder extends PathBuilder
{
	function guid()
	{
		$ret = "";
		if (function_exists('com_create_guid'))
		{
			$ret = com_create_guid();
		}
		else
		{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125);// "}"
			$ret = $uuid;
		}
		$ret = str_replace("{","",$ret);
		$ret = str_replace("}","",$ret);
		$ret = str_replace("-","",$ret);
		$ret = strtolower($ret);
		return $ret;
	}
	
	//d:\\wamp\\www\\up6\\upload\\
	function genFolder($uid,&$fd/*FolderInf*/)
	{
		date_default_timezone_set("PRC");//设置北京时区
		$path = $this->getRoot();
		$path = PathTool::combin($path, date("Y"));
		$path = PathTool::combin($path, date("m"));
		$path = PathTool::combin($path, date("d"));
		$path = PathTool::combin($path,$this->guid());
		$path = PathTool::combin($path,$fd->nameLoc);
		
		//在windows平台需要转换成多字节编码
		$path = iconv("utf-8", "gb2312", $path);
		
		if( !is_dir($path)) mkdir($path,0777,true);
		return realpath($path);//规范化路径
	}
	
	function createFolder($v)
	{
		$path = iconv("utf-8","gb2312",$v);
		if( !is_dir($path)) mkdir($path,0777,true);
		return realpath($path);//规范化路径
	}
}
?>