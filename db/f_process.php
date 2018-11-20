<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
		2017-07-11 
			简化文件块逻辑，
			取消进度更新操作
*/
require('biz/up6_biz_event.php');
require('database/DbHelper.php');
require('database/DBFile.php');

$id	 		= $_GET["id"];
$uid	 	= $_GET["uid"];
$offset		= $_GET["offset"];
$lenSvr		= $_GET["lenSvr"];
$perSvr		= $_GET["perSvr"];
$perSvr		= str_replace("+", " ", $perSvr);
$cbk		= $_GET["callback"];

$json = "$cbk({'state':0})";

//相关参数不能为空
if (   (strlen($id)>0) 
	&& (strlen($lenSvr)>0) 
	&& (strlen($perSvr)>0) 
	)
{	
	$db = new DBFile();
	$db->f_process($uid, $id, $offset, $lenSvr, $perSvr);
	up6_biz_event::file_post_process($id);
	$json = "$cbk({\"state\":1})";	
}
echo $json;
header('Content-Length: ' . ob_get_length());
?>