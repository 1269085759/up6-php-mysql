<?php 
require('../../db/database/DbHelper.php');
require('../../db/PathTool.php');
require('DnFile.php');
require('../model/DnFileInf.php');

$id 		= $_GET["id"];
$uid 		= $_GET["uid"];
$fdTask 	= $_GET["fdTask"];
$nameLoc 	= $_GET["nameLoc"];
$pathLoc 	= $_GET["pathLoc"];
$lenSvr 	= $_GET["lenSvr"];
$sizeSvr 	= $_GET["sizeSvr"];
$cbk 		= $_GET["callback"];
$pathLoc	= PathTool::urldecode_path($pathLoc);
$nameLoc	= PathTool::urldecode_path($nameLoc);

if (  strlen($uid) < 1
	||empty($pathLoc)	
	||empty($lenSvr))
{
	echo cbk . "({\"value\":null})";
	die();
}

$inf = new DnFileInf();
$inf->id = $id;
$inf->uid = intval($uid);
$inf->nameLoc = $nameLoc;
$inf->pathLoc = $pathLoc;
$inf->lenSvr = intval($lenSvr);
$inf->sizeSvr = $sizeSvr;
$inf->fdTask = $fdTask == "1";

$db = new DnFile();
$db->Add($inf);

//防止jsonencode将汉字转换为unicode
$inf->nameLoc = PathTool::urlencode_safe($inf->nameLoc);
$inf->pathLoc = PathTool::urlencode_safe($inf->pathLoc);
$json = json_encode($inf);
$json = urldecode($json);//还原汉字
$json = urlencode($json);
$json = "$cbk({\"value\":\"".$json."\"})";//返回jsonp格式数据。
echo $json;
?>