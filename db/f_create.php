<?php
ob_start();
header('Content-type: text/html;charset=utf-8');
/*
	此文件主要功能如下：
		1.在数据库中添加新记录
		2.返回新加记录信息。JSON格式
		3.创建上传目录
	此文件主要在数据库中添加新的记录并返回文件信息
		如果存在则在数据库中添加一条相同记录。返回添加的信息
		如果不存在，则向数据库中添加一条记录。并返回此记录ID
	控件每次计算完文件MD5时都将向信息上传到此文件中
	@更新记录：
		2014-08-12 完成逻辑。
		2017-07-11 优化
*/
require('database/DbHelper.php');
require('database/DBFile.php');
require('database/DBFolder.php');
require('model/FileInf.php');
require('model/FolderInf.php');
require('utils/FileResumer.php');
require('utils/PathTool.php');
require('biz/PathBuilder.php');
require('biz/PathBuilderMd5.php');

$md5 			= $_GET["md5"];
$id 			= $_GET["id"];
$uid 			= $_GET["uid"];
$lenLoc			= $_GET["lenLoc"];//10240
$sizeLoc		= $_GET["sizeLoc"];//10mb
$sizeLoc		= str_replace("+", " ", $sizeLoc);
$callback 		= $_GET["callback"];//jsonp
$pathLoc		= $_GET["pathLoc"];
$pathLoc		= PathTool::urldecode_path($pathLoc);

if(    empty($md5)
	|| strlen($uid)<1
	|| empty($sizeLoc))
{
	echo $callback . "({\"value\":null})";
	die();
}

$ext = PathTool::getExtention($pathLoc);
$fileSvr = new FileInf();
$fileSvr->id = $id;
$fileSvr->fdChild = false;
$fileSvr->fdTask = false;
$fileSvr->nameLoc = PathTool::getName($pathLoc);
$fileSvr->pathLoc = $pathLoc;
$fileSvr->nameSvr = "$md5.$ext";
$fileSvr->lenLoc = intval($lenLoc);
$fileSvr->sizeLoc = $sizeLoc;
$fileSvr->deleted = false;
$fileSvr->md5 = $md5;
$fileSvr->uid = intval($uid);

//生成路径
$pb = new PathMd5Builder();
$fileSvr->pathSvr = $pb->genFile($uid,$fileSvr->md5,$fileSvr->nameLoc);

$db = new DBFile();
$fileExist = new FileInf();

//数据库存在相同文件
if ($db->exist_file($md5, $fileExist))
{
	$fileSvr->pathSvr = $fileExist->pathSvr;
	$fileSvr->perSvr = $fileExist->perSvr;
	$fileSvr->lenSvr = intval($fileExist->lenSvr);
	$fileSvr->complete = (bool)$fileExist->complete;
	$db->Add($fileSvr);
}//数据库不存在相同文件
else
{
	$db->Add($fileSvr);
	
	//创建文件
	$fr = new FileResumer();
	$fr->CreateFile($fileSvr->pathSvr,$fileSvr->lenLoc);
}
//fix:防止json_encode将汉字转换成unicode
$fileSvr->nameLoc = PathTool::urlencode_safe($fileSvr->nameLoc);
$fileSvr->pathLoc = PathTool::urlencode_safe($fileSvr->pathLoc);
$fileSvr->pathSvr = PathTool::urlencode_safe($fileSvr->pathSvr);
	
$json = json_encode($fileSvr);//低版本php中，json_encode会将汉字进行unicode编码
$json = urldecode( $json );//还原汉字

$json = urlencode($json);
$json = str_replace("+","%20",$json);
$json = $callback . "({'value':'$json'})";//返回jsonp格式数据。
echo $json;
header('Content-Length: ' . ob_get_length());
?>