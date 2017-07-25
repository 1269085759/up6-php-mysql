<?php
header('Content-Type: text/html;charset=utf-8');
require('../../db/inc.php');
require('../../db/database/DbHelper.php');
require('../../db/PathTool.php');
require('../model/DnFileInf.php');
require('../biz/DnFolder.php');
/*
 * 获取文件夹，子文件列表，返回JSON格式
	更新记录：
		2015-05-13 创建
		2016-07-29 更新
*/
$id 	= $_GET["id"];
$cbk	= $_GET["callback"];
$json	= "$cbk({\"value\":null})";

if ( !empty( $id) )
{
	$db = new DnFolder();
	$data = $db->all_file($id);
	$data = urldecode($data);//还原汉字
	$data = urlencode($data);
	$data = str_replace("+", "%20", $data);
	$json = "$cbk({\"value\":\"$data\"})";
}
echo $json;
?>