<?php
require('../../db/DbHelper.php');
require('../../db/PathTool.php');
require('../model/DnFileInf.php');
require('../biz/cmp_file.php');
require('../biz/un_file.php');
require('../biz/un_builder.php');

$uid = $_GET["uid"];
$cbk = $_GET["callback"];//jsonp

if ( strlen($uid)>0 )
{
	$fd = new un_builder();
	$json = $fd->read($uid);
	
	if( !empty($json) )
	{
		$json = urlencode($json);
		$json = str_replace("+","%20",$json);//
		$json = "$cbk({\"value\":\"$json\"})";
		echo $json;
		return;
	}
}

echo $cbk . "({\"value\":null})";
?>