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
require('database/DbHelper.php');
require('database/DBFile.php');
require('xdb_files.php');
require('FileResumer.php');
require('HttpHeader.php');


$head = new HttpHeader();

$uid	 		= $head->param("uid");
$fid	 		= $head->param("id");
$md5			= $head->param("md5");
$perSvr			= $head->param("perSvr");
$lenSvr			= $head->param("lenSvr");
$lenLoc			= $head->param("lenLoc");
$f_pos			= $head->param("blockOffset");
$rangeSize		= $head->param("blockSize");
$rangeIndex		= $head->param("blockIndex");
$complete		= (bool)$head->param("complete");
$pathSvr		= $head->param("pathSvr");
$pathSvr		= PathTool::urldecode_path($pathSvr);
$fpath			= $_FILES['file']['tmp_name'];//

//相关参数不能为空
if (   (strlen($lenLoc)>0) 
	&& (strlen($uid)>0) 
	&& (strlen($fid)>0) 
	&& (strlen($f_pos)>0) 
	&& !empty($pathSvr))
{		
	//保存文件块数据
	$resu = new FileResumer($fpath,$lenLoc,$md5,$f_pos,$pathSvr);
	$resu->Resumer();	
	
	echo "ok";
	//调试时，打开下面的代码，显示文件块MD5。
	//echo "ok".",range_md5:".$resu->m_rangMD5;
}
else
{
	echo "param is null";
	echo "uid:$uid<br/>";
	echo "fid:$fid<br/>";
	echo "md5:$md5<br/>";
	echo "perSvr:$perSvr<br/>";
	echo "lenSvr:$lenSvr<br/>";
	echo "lenLoc:$lenLoc<br/>";
	echo "f_pos:$f_pos<br/>";
	echo "complete:$complete<br/>";
	echo "pathSvr:$pathSvr<br/>";
}
header('Content-Length: ' . ob_get_length());
?>