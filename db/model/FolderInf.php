<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2014-08-12 创建
*/
class FolderInf extends FileInf
{
	var $folders = array()	;	//文件夹列表
	var $files = array();		//文件列表
	
	function __construct()
	{
		$this->fdTask = true;
	}
}
?>