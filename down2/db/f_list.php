<?php
require('../../db/database/DbHelper.php');
require('../../db/utils/PathTool.php');
require('../../db/model/FileInf.php');
require('../model/DnFileInf.php');
require('../biz/DnFile.php');

$uid 	= $_GET["uid"];
$cbk 	= $_GET["callback"];//jsonp
$json 	= "$cbk({\"value\":null})";

if ( strlen($uid)>0 )
{
	$db = new DnFile();
	$json = $db->all_uncmp( $uid);	
	
	if( !empty($json) )
	{
		$json = urldecode($json);//还原汉字
		$json = urlencode($json);
		$json = str_replace("+","%20",$json);//
		$json = "$cbk({\"value\":\"$json\"})";
		echo $json;
		return;
	}
}

echo $json;
?>