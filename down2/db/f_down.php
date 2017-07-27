<?php
require('../../db/utils/HttpHeader.php');
require('../../db/utils/PathTool.php');

$head = new HttpHeader();

$id 		 = $head->param("id");
$blockIndex  = $head->param("blockIndex");
$blockOffset = $head->param("blockOffset");
$blockSize 	 = $head->param("blockSize");
$pathSvr 	 = $head->param("pathSvr");
$pathSvr	 = PathTool::urldecode_path($pathSvr);

if ( empty($id) 
	|| empty($blockIndex)
	|| strlen($blockOffset) < 1
	|| empty($blockSize)
	|| empty($pathSvr) ) 
{
	header('HTTP/1.1 500 param null');
	return;
}

header("Cache-Control: public");
header("Content-Type: application/octet-stream");
header("Content-Length: $blockSize");

$readToLen = intval($blockSize);
$readLen = 0;
//windows系统中需要将中文转换为gb2312
$pathSvr = iconv( "UTF-8","GB2312",$pathSvr);
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