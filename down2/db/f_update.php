<?php 
require('../../db/database/DbHelper.php');
require('../model/DnFileInf.php');
require('DnFile.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

$fid 	= $_GET["idSvr"];
$uid 	= $_GET["uid"];
$lenLoc	= $_GET["lenLoc"];
$per	= $_GET["perLoc"];
$cbk 	= $_GET["callback"];//jsonp
//
$file_id	= $_GET["file_id"];
$file_lenLoc = $_GET["file_lenLoc"];
$file_per	= $_GET["file_per"];

if ( strlen($uid) < 1
	||empty($fid)
	||empty($cbk)
	||empty($lenLoc) )
{
	echo $cbk . "({\"value\":0})";
	return;
}

$db = new DnFile();
$db->updateProcess($fid,$uid,$lenLoc,$per);
//更新子文件
if (!empty($file_id) && !empty($file_lenLoc))
{
    $db->updateProcess($file_id, $uid, $file_lenLoc, $file_per);
}
echo $cbk . "({\"value\":1})";
?>