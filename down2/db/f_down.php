<?php
require('../../utils/HttpHeader.php');
require('../../db/PathTool.php');

$head = new HttpHeader();

$id 		 = $head->param("id");
$blockIndex  = $head->param("blockIndex");
$blockOffset = $head->param("blockOffset");
$blockSize 	 = $head->param("blockSize");
$pathSvr 	 = $head->param("pathSvr");
$pathSvr	 = PathTool::urldecode_path($pathSvr);

if ( empty($id) 
	|| empty($blockIndex)
	|| empty($blockOffset)
	|| empty($blockSize)
	|| empty($pathSvr) ) 
{
	die();
}

header("Cache-Control: public");
header("Content-Type: application/octet-stream");
header("Content-Length: $blockSize");

$readToLen = intval($blockSize);
$readLen = 0;
$file = fopen($pathSvr,"rb");
fseek($file,$blockOffset);
while( $readToLen > 1)
{
	set_time_limit(0);
	$len = min(1048576,$readToLen);
	print( fread($file,$len ));
	$readToLen -= $len;
	flush();
	ob_flush();
}
fclose($file);
?>