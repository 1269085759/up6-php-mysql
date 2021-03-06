<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	只文件根据UID和FID删除数据库中文件项。不真正删除数据，只将设置删除标识
	返回值：
		1 删除成功
		0 删除失败
	更新记录：
		2012-4-2 创建
		2014-09-12 完成逻辑。
*/
require('biz/up6_biz_event.php');
require('database/DbHelper.php');
require('database/DBFile.php');

$uid = $_GET["uid"];
$fid = $_GET["id"];
$cbk = $_GET["callback"];
$ret = $cbk . "(0)";

//参数不为空
if (	strlen($fid) > 0 
	&&	strlen($uid) > 0)
{
	$db = new DBFile();
	$db->Delete(intval($uid),$fid);
	up6_biz_event::file_del($fid, intval($uid));
	$ret = $cbk . "(1)";
}

//返回查询结果
echo $ret;
header('Content-Length: ' . ob_get_length());
?>