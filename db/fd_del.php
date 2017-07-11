<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
		2014-09-12 完成逻辑
*/
require('database/DbHelper.php');
require('database/DBFile.php');
require('database/DBFolder.php');
require('UploaderCfg.php');

$fid = $_GET["fid"];
$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp
$ret = 0;

//参数不为空
if (	strlen($fid) > 0
	||	strlen($uid) > 0 )
{
	DBFolder::Remove($fid,$uid);
	$ret = 1;
}
echo "$cbk($ret)";
header('Content-Length: ' . ob_get_length());
?>