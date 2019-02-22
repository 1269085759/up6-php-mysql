<?php
/*
 * 
	更新记录：
		2016-07-31 创建
		2017-07-25 更新
*/
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
	
	function all_file($id)
	{
		$sql = 'select
				 f_id
				,f_nameLoc
				,f_pathSvr
				,f_pathRel
				,f_lenSvr
				,f_sizeLoc				
				 from up6_files
				 where f_pidRoot=:pidRoot;';
		
		$files = array();
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
		
		$cmd->bindValue(":pidRoot",$id);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{
			$f = new DnFileInf();
			$f->f_id 	= $row["f_id"];
			$f->nameLoc = PathTool::urlencode_path( $row["f_nameLoc"] );//防止汉字被转换成unicode
			$f->pathSvr = PathTool::urlencode_path( $row["f_pathSvr"] );//防止汉字被转换成unicode
			$f->pathRel = PathTool::urlencode_path( $row["f_pathRel"] );//防止汉字被转换成unicode
			$f->lenSvr 	= $row["f_lenSvr"];
			$f->sizeSvr	= $row["f_sizeLoc"];			
		
			$files[] = $f;
		}
		if( count($files) < 1 ) return "";
		return json_encode($files);
	}
}
?>