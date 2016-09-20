<?php
require('../../db/DbHelper.php');
require('../../db/xdb_files.php');
require('../../db/DBFile.php');
require('../../db/PathTool.php');

$fid = $_GET["fid"];
if ( strlen($fid) < 1) die();

$inf = new xdb_files();
$db = new DBFile();

//数据库无记录
if(!$db->GetFileInfByFid($fid,&$inf) ) die("data not found");

//文件不存在
if( !is_file($inf->pathSvr)){ die("File not found");}

$len = intval(filesize($inf->pathSvr) );
$filename = $inf->nameLoc;
$ctype = "application/octet-stream";

header("Cache-Control: public");
header("Content-Type: $ctype");
if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
	# workaround for IE filename bug with multiple periods / multiple dots in filename
	# that adds square brackets to filename - eg. setup.abc.exe becomes setup[1].abc.exe
	$iefilename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);
	header("Content-Disposition: attachment; filename=\"$iefilename\"");
} 
else 
{
	header("Content-Disposition: attachment; filename=\"$filename\"");
}

$range_begin = 0;
//续传标记
if( isset($_SERVER['HTTP_RANGE']) ) 
{
	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);//$range = 9422090-10371208
	list($range_begin,$range_end) = explode("-", $range);
	//if yes, download missing part
	//str_replace($range, "-", $range);
	$size2 = $len-1;
	$new_length = $len-intval($range_begin);
	header("HTTP/1.1 206 Partial Content");
	header("Content-Length: $new_length");
	header("Content-Range: bytes $range_begin-$len/$new_length");
} 
else 
{
	$size2 = $len-1;
	header("Content-Range: bytes 0-$size2/$len");
	header("Content-Length: $len");
}
$fp = fopen($inf->pathSvr,"rb");
fseek($fp, $range_begin);
while(!feof($fp)){
	//reset time limit for big files
	set_time_limit(0);
	print(fread($fp,1024*8));
	flush();
	ob_flush();
}
fclose($fp);
?>