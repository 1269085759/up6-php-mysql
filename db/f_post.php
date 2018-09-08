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
require('model/FileInf.php');
require('utils/FileResumer.php');
require('utils/HttpHeader.php');
require('utils/PathTool.php');


$head = new HttpHeader();

$uid	 		= $head->param("uid");
$fid	 		= $head->param("id");
$md5			= $head->param("md5");
$lenSvr			= $head->param("lenSvr");
$lenLoc			= $head->param("lenLoc");
$f_pos			= $head->param("blockOffset");
$blockSize		= $head->param("blockSize");
$blockIndex		= $head->param("blockIndex");
$blockMd5		= $head->param("blockMd5");
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
	$verify = false;
	$msg = "";
	$md5Svr = "";
	
	if(!empty($blockMd5))
	{
		$md5Svr = md5_file($fpath);
	}
	
	//验证大小
	$verify = intval($blockSize) == filesize($fpath);
	if( $verify ) 
	{
		$msg = "block size error sizeSvr:" . filesize($fpath) . " sizeLoc:" . $blockSize;
	}
	
	if( $verify && !empty($blockMd5) )
	{
		$verify = $md5Svr == $blockMd5;
	}
	
	if( $verify )
	{
		//保存文件块数据
		$resu = new FileResumer($fpath,$lenLoc,$md5,$f_pos,$pathSvr);
		if(0 == strcmp($blockIndex,"1")) $resu->CreateFile($pathSvr);
		$resu->Resumer();
		
		$obj = Array('msg'=>'ok', 'md5'=>$md5Svr, 'offset'=>$f_pos);
		$msg = json_encode($obj);
	}
	echo $msg;
}
else
{
	echo "param is null";
	echo "uid:$uid<br/>";
	echo "fid:$fid<br/>";
	echo "md5:$md5<br/>";
	echo "lenSvr:$lenSvr<br/>";
	echo "lenLoc:$lenLoc<br/>";
	echo "f_pos:$f_pos<br/>";
	echo "complete:$complete<br/>";
	echo "pathSvr:$pathSvr<br/>";
}
header('Content-Length: ' . ob_get_length());
?>