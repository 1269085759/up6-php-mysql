<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
		2014-09-12 完成逻辑。
		2014-09-15 修复返回JSONP数据格式错误的问题。
		2016-05-31 优化调用，DBFolder::Complete会自动更新文件表信息，所以在此页面不需要再单独调用DBFile::fd_complete
*/
require('database/DbHelper.php');
require('database/DBFile.php');
require('database/DBFolder.php');

$id   	= $_GET["id"];
$uid	= $_GET["uid"];
$cbk 	= $_GET["callback"];//jsonp
$ret 	= 0;

//参数为空
if (	strlen($uid) > 0
	||	strlen($id_fd) >0  )
{
	$fd = new DBFolder();
	$fd->Complete($id, $uid);
	$ret = 1;
}
echo "$cbk( $ret )";
header('Content-Length: ' . ob_get_length());
?>