<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	列表出所文件列表，包括未上传完成的，以JSON方式返回给客户端JS。
*/
require('model/xdb_files.php');
require('model/FileInf.php');
require('model/FolderInf.php');
require('database/DbHelper.php');
require('database/DbFile.php');
require('database/DBFolder.php');
require('uncomplete/uc_folder.php');
require('uncomplete/uc_file_child.php');
require('uncomplete/uc_builder.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];

if( strlen($uid) > 0)
{
	$json = DBFile::GetAllUnComplete2($uid );
	if( !empty($json) )
	{
		//echo $json;
		$json = urlencode($json);
		$json = str_replace("+","%20",$json);
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
header('Content-Length: ' . ob_get_length());
?>