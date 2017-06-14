<?php
ob_start();
/*
	控件每次向此文件POST数据
	逻辑：
		1.更新数据库进度
		2.将文件块数据保存到服务器中。
	更新记录：
		2014-04-09 增加文件块验证功能。
*/
require('DbHelper.php');
require('DBFile.php');
require('xdb_files.php');
require('FileResumer.php');

$uid	 		= $_POST["uid"];
$fid	 		= $_POST["idSvr"];
$md5			= $_POST["md5"];
$perSvr			= $_POST["perSvr"];
$lenSvr			= $_POST["lenSvr"];
$lenLoc			= $_POST["lenLoc"];
$f_pos			= $_POST["RangePos"];
$rangeSize		= $_POST["rangeSize"];
$rangeIndex		= $_POST["rangeIndex"];
$complete		= $_POST["complete"];
$fd_idSvr = "";
if(!empty($_POST["fd-idSvr"]) )	$fd_idSvr = $_POST["fd-idSvr"];
$fd_lenSvr = "";
if(!empty($_POST["fd-lenSvr"]) ) $fd_lenSvr = $_POST["fd-lenSvr"];
$fd_perSvr = "";
if(!empty($_POST["fd-perSvr"]) ) $fd_perSvr = $_POST["fd-perSvr"];
$pathSvr		= $_POST["pathSvr"];
$pathSvr		= str_replace("+","%20",$pathSvr);
$pathSvr		= urldecode($pathSvr);//服务器路径，URL编码
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
	$cmp = strcmp($complete,"true") == 0;

	//更新数据表进度信息
	$db = new DBFile();		
	$fd = strlen($fd_idSvr) > 0;
	if($fd) $fd = !empty($fd_lenSvr);
	if($fd) $fd = intval($fd_idSvr) > 0;
	if($fd) $fd = intval($fd_lenSvr)> 0;
	if($fd)
	{		
		$db->fd_fileProcess($uid,$fid,$f_pos,$lenSvr,$perSvr,$fd_idSvr,$fd_lenSvr,$fd_perSvr,$cmp);
	}
	else
	{
		$db->f_process($uid,$fid,$f_pos,$lenSvr,$perSvr,$cmp);
	}

	
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
	echo "fd_idSvr:$fd_idSvr<br/>";
	echo "fd_lenSvr:$fd_lenSvr<br/>";
	echo "fd_perSvr:$fd_perSvr<br/>";
	echo "pathSvr:$pathSvr<br/>";
}
header('Content-Length: ' . ob_get_length());
?>