<?php 
require('../../db/database/DbHelper.php');
require('../../db/utils/PathTool.php');
require('../../db/model/FileInf.php');
require('../model/DnFileInf.php');
require('../biz/DnFile.php');

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