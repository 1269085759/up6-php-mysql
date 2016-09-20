<?php
header('Content-Type: text/html;charset=utf-8');
require('../../db/inc.php');
require('../../db/DbHelper.php');
require('../../db/PathTool.php');
require('../model/DnFileInf.php');
require('../model/DnFolderInf.php');
require('../biz/folder_appender.php');
/*
	
    /// 创建一个文件夹下载任务。
    /// JSON格式：
    /// {
    //	"m_perSvr": "100%",
    //	"nameLoc": "files-1",
    //	"lenLoc": 12244936,
    //	"size": "11.6MB",
    //	"lenSvr": 12244936,
    //	"perSvr": "100%",
    //	"pidLoc": 0,
    //	"pidSvr": 0,
    //	"idLoc": 0,
    //	"idSvr": 1421,
    //	"idFile": 2524,
    //	"uid": 0,
    //	"foldersCount": 0,
    //	"filesCount": 1,
    //	"filesComplete": 0,
    //	"pathLoc": "C: \\\\Users\\\\Administrator\\\\Desktop\\\\test\\\\files-1",
    //	"pathSvr": "",
    //	"pidRoot": 0,
    //	"pathRel": "",
    //	"files": [{
    //		"nameLoc": "360wangpan_setup.exe",
    //		"pathLoc": "C:\\\\Users\\\\Administrator\\\\Desktop\\\\test\\\\files-1\\\\360wangpan_setup.exe",
    //		"pathSvr": "F:\\\\csharp\\\\HttpUploader6\\\\trunk\\\\v1.3-fd\\\\upload\\\\2016\\\\07\\\\24\\\\a03b6d45916dcd6db43d1660ac789f78.exe",
    //		"md5": "a03b6d45916dcd6db43d1660ac789f78",
    //		"pidLoc": 0,
    //		"pidSvr": 1421,
    //		"pidRoot": 1421,
    //		"idLoc": 0,
    //		"idSvr": 2525,
    //		"uid": 0,
    //		"lenLoc": 12244936,
    //		"sizeLoc": "11.6MB",
    //		"lenSvr": 12244936,
    //		"postPos": 0,
    //		"perSvr": "0%",
    //		"pathRel": null,
    //		"complete": false,
    //		"nameSvr": null
    //    }]
    //}
	更新记录：
		2015-05-13 创建
		2016-07-29 更新
*/
$uid 	= $_POST["uid"];
$fdSvr	= $_POST["folder"];
$fdSvr	= str_replace("+", "%20", $fdSvr);//fix(2015-07-31):防止中文名称出现乱码
$fdSvr	= urldecode($fdSvr);//utf-8解码
//$fdSvr	= unTurn($fdSvr);//去转义，使用fd_create.htm调试时需要打开此开关

if ( strlen($uid)<1
	||empty($fdSvr)
	)
{
	echo 0;
	return;
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