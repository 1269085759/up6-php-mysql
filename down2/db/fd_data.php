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
}

$fdArr = json_decode($fdSvr,true);
$fd = new DnFolderInf();
$fd->nameLoc = $fdArr["nameLoc"];
$fd->pathLoc = $fdArr["pathLoc"];
$fd->nameLoc = $fd->nameLoc;
$fd->pathLoc = $fd->pathLoc;
$fd->fileUrl = $fdArr["fileUrl"];
$fd->lenSvr  = (int)$fdArr["lenSvr"];
$fd->sizeSvr = $fdArr["sizeSvr"];
$fd->uid	 = $fdArr["uid"];
if( !empty($fdArr["files"]))
{
	foreach($fdArr["files"] as $row )
	{
		$f = new DnFileInf();
		$f->nameLoc = $row["nameLoc"];
		$f->pathLoc = $row["pathLoc"];
		$f->nameLoc = $f->nameLoc;//防止json_encode将汉字转换为unicode
		$f->pathLoc = $f->pathLoc;//防止json_encode将汉字转换为unicode
		$f->fileUrl = $row["fileUrl"];
		$f->lenSvr  = (int)$row["lenSvr"];//取不到值？
		$f->sizeSvr = $row["sizeSvr"];
		$f->pidRoot = (int)$row["pidRoot"];
		$fd->files[] = $f;
	}
}
$fa = new folder_appender();
$fa->add($fd);

$json = json_encode($fd);
$json = urldecode($json);//还原汉字
$json = urlencode($json);
//UrlEncode会将空格解析成+号
$json = str_replace("+", "%20",$json);
echo $json;
?>