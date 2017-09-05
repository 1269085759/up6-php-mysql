<?php
ob_start();
/*
	文件下载页面
	更新记录：
		2012-04-03 创建
*/
require('FileDown.class.php');
require('../database/DbHelper.php');
require('../database/DBFile.php');
require('../model/FileInf.php');

$fid = $_GET["fid"];

if( strlen($fid) >0)
{
	$db = new DBFile();
	$fileSvr = new FileInf();
	if($db->GetFileInfByFid($fid,&$fileSvr) )
	{		
		dl_file_resume($fileSvr->pathSvr,iconv("UTF-8","GB2312",$fileSvr->nameLoc));
	}
}
?>