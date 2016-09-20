<?php
ob_start();
/*
	文件下载页面
	更新记录：
		2012-04-03 创建
*/
require('FileDown.class.php');
require('DbHelper.php');

$fid = $_GET["fid"];

if( strlen($fid) >0)
{
	$db = new DbHelper();
	$inf = $db->GetFileInfByFid($fid);
	//本地文件名称
	$name = urldecode($inf["FileNameLocal"]);
	//服务器文件绝对路径
	$path = urldecode($inf["FilePathRemote"]);
	
	dl_file_resume($path,iconv("UTF-8","GB2312",$name));
}

?>