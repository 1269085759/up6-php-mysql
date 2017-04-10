<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
require('inc.php');
require('DbHelper.php');
require('DBFile.php');
require('DbFolder.php');
require('FileInf.php');
require('FolderInf.php');
require('FdDataWriter.php');
/*
	此文件只负责将数据表中文件上传进度更新为100%
		向数据库添加新记录在 ajax_create_fid.php 文件中处理
	如果服务器不存在此文件，则添加一条记录，百分比为100%
	如果服务器已存在相同文件，则将文件上传百分比更新为100%
*/
//$txt = "MyEclipse8.5\\u6c49\\u5316.doc";
$nid = 0;
$fd_writer = new FdDataWriter();

	$fd 			= new FolderInf();
	
	echo date('y-m-d h:i:s',time());
	echo "<br/>";
	$inf = new FileInf();
	
	//$ids = $fd_writer->fd_files_add_batch(100,100);//批量添加数据
	//var_dump($ids["ids_f"]);
	
	$ret = $fd_writer->fd_files_check("15736b6273683faf0eb6733beb03e2e1,689e7e4c2c2cc3588f9e73ac82c2423b");
	var_dump($ret);
	echo "<br/>";
	
	var_dump($ret["b5fde3c2b489aac848e472d5ceddebac"])."<br/>";
	var_dump( empty($ret["a"] ) )."<br/>";
	echo "<br/>";
	
	for($i = 0 ;$i<100;++$i)
	{
		//$fd_writer->addFolder2($fd);
		//$fd_writer->addFolder2($fd);
		//$fd_writer->addFolder2($fd);
		//$fd_writer->f_update($inf);
	}
	echo date('y-m-d h:i:s',time());
	



header('Content-Length: ' . ob_get_length());
?>