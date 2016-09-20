<?php
/*
 * 
	更新记录：
		2016-07-31 创建
*/
require('../../db/inc.php');
require('../../db/DbHelper.php');
class DnFolder
{	
	var $db;//全局数据库连接,共用数据库连接
	
	function __construct()
	{ 
		$this->db = new DbHelper();
	}

	function Clear()
	{
		$this->db->ExecuteNonQueryTxt("truncate table down_files");
	}
}
?>