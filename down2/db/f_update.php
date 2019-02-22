<?php 
require('../../db/database/DbHelper.php');
require('../model/DnFileInf.php');
require('../biz/DnFile.php');

$fid 	= $_GET["id"];
$uid 	= $_GET["uid"];
$lenLoc	= $_GET["lenLoc"];
$per	= $_GET["perLoc"];
$cbk 	= $_GET["callback"];//jsonp

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
echo "$cbk({\"value\":1})";
?>