<?php
header('Content-Type: text/html;charset=utf-8');
require('../../utils/inc.php');
require('../../db/database/DbHelper.php');
require('../../db/PathTool.php');
require('../model/DnFileInf.php');
require('../biz/DnFile.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid) > 0)
{
	$db = new DnFile();
	$json = $db->all_complete($uid);
	
	if(!empty($json))
	{
		$json = urldecode($json);//还原汉字
		$json = urlencode($json);
		$json = str_replace("+", "%20", $json);
		echo "$cbk({\"value\":\"$json\"})";
		return;
	}
}
echo $cbk . "({\"value\":null})";
?>