<?php 
require('../../db/DbHelper.php');
require('../../db/PathTool.php');
require('../DnFile.php');
require('../model/DnFileInf.php');

$fid = $_GET["id"];
$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid)<1 ||	empty($fid)	)
{
	echo $cbk . "({\"value\":null})";
	die();
}
$file = new DnFile();
$file->Delete($fid, $uid);
echo $cbk . "({\"value\":1})";
?>