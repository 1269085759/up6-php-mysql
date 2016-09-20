<?php 
require('../../db/DbHelper.php');
require('../../db/PathTool.php');
require('DnFile.php');
require('../model/DnFileInf.php');

$uid 		= $_GET["uid"];
$nameLoc 	= $_GET["nameCustom"];
$pathLoc 	= $_GET["pathLoc"];
$pathSvr 	= $_GET["fileUrl"];
$lenSvr 	= $_GET["lenSvr"];
$sizeSvr 	= $_GET["sizeSvr"];
$cbk 		= $_GET["callback"];
$pathLoc	= str_replace("+","%20",$pathLoc);
$nameLoc	= str_replace("+","%20",$nameLoc);
$pathLoc	= urldecode($pathLoc);
$nameLoc	= urldecode($nameLoc);


if (  strlen($uid) < 1
	||empty($pathLoc)
	||empty($pathSvr)
	||empty($lenSvr))
{
	echo cbk . "({\"value\":null})";
	die();
}

$inf = new DnFileInf();
$inf->uid = intval($uid);
$inf->nameLoc = $nameLoc;
$inf->pathLoc = $pathLoc;
$inf->fileUrl = $pathSvr;
$inf->lenSvr = intval($lenSvr);
$inf->sizeSvr = $sizeSvr;

$db = new DnFile();
$inf->idSvr = (int)$db->Add($inf);

//防止jsonencode将汉字转换为unicode
$inf->nameLoc = PathTool::urlencode_safe($inf->nameLoc);
$inf->pathLoc = PathTool::urlencode_safe($inf->pathLoc);
$json = json_encode($inf);
$json = urldecode($json);//还原汉字
$json = urlencode($json);
$json = "$cbk({\"value\":\"".$json."\"})";//返回jsonp格式数据。
echo $json;
?>