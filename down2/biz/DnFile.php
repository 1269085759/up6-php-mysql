<?php
/*
 * 
	更新记录：
		2016-07-31 创建
		2017-07-25 更新
*/
class DnFile
{
	var $db;//全局数据库连接,共用数据库连接
	
	function __construct()
	{
		$this->db = new DbHelper();
	}

	function Add($inf/*DnFileInf*/)
	{
		$sql  = 'insert into down_files(
				 f_id
				,f_uid
				,f_nameLoc
				,f_pathLoc				
				,f_lenSvr
				,f_sizeSvr
				,f_fdTask
				) values(
				 :f_id
				,:f_uid
				,:f_nameLoc
				,:f_pathLoc				
				,:f_lenSvr
				,:f_sizeSvr
				,:f_fdTask
				)';
		
		$cmd = $this->db->prepare_utf8( $sql );

		$cmd->bindValue(":f_id",$inf->id);
		$cmd->bindValue(":f_uid",$inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":f_nameLoc",$inf->nameLoc);
		$cmd->bindParam(":f_pathLoc",$inf->pathLoc);		
		$cmd->bindValue(":f_lenSvr",$inf->lenSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_sizeSvr",$inf->sizeSvr);
		$cmd->bindParam(":f_fdTask",$inf->fdTask,PDO::PARAM_BOOL);
		$this->db->ExecuteNonQuery($cmd );
	}

	/// <summary>
	/// 删除文件
	/// </summary>
	/// <param name="fid"></param>
	function Delete($fid,$uid)
	{
		$sql = "delete from down_files where f_id=:f_id and f_uid=:f_uid";
		
		$cmd = $this->db->prepare_utf8($sql);

		$cmd->bindParam(":f_id", $fid);
		$cmd->bindParam(":f_uid", $uid);
		$this->db->Execute($cmd);
	}

	/**
	 * 更新文件进度信息
	 * @param fid
	 * @param uid
	 * @param mac
	 * @param lenLoc
	 */
	function updateProcess($fid,$uid,$lenLoc,$perLoc)
	{
		$sql = "update down_files set f_lenLoc=:f_lenLoc,f_perLoc=:f_perLoc where f_id=:f_id and f_uid=:f_uid";
		$db  = new DbHelper();
		$cmd = $db->prepare_utf8($sql);				

		$cmd->bindParam(":f_lenLoc", $lenLoc);
		$cmd->bindParam(":f_perLoc", $perLoc);
		$cmd->bindParam(":f_id", $fid);
		$cmd->bindParam(":f_uid", $uid);
		
		$db->ExecuteNonQuery($cmd);
	}
	
	function clear()
	{
		$this->db->ExecuteNonQueryTxt("truncate table down_files");
	}
	
	function all_complete($uid)
	{
		$sql = 'select 
				 f_id
				,f_fdTask
				,f_nameLoc
				,f_sizeLoc
				,f_lenSvr
				,f_pathSvr
				 from up6_files
				 where f_uid=:f_uid and f_deleted=0 and f_fdChild=0 and f_complete=1 and f_scan=1;';
		
		//取未完成的文件列表
		//$files = array();
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
				
		$cmd->bindValue(":f_uid",$uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{
			$f = new DnFileInf();
			$f->id 		= new_guid();
			$f->f_id 	= $row["f_id"];
			$f->fdTask 	= (bool)$row["f_fdTask"];
			$f->nameLoc = PathTool::urlencode_path( $row["f_nameLoc"] );//防止汉字被转换成unicode
			$f->sizeLoc = $row["f_sizeLoc"];
			$f->sizeSvr	= $row["f_sizeLoc"];
			$f->lenSvr 	= $row["f_lenSvr"];
			$f->pathSvr	= PathTool::urlencode_safe( $row["f_pathSvr"] );//防止汉字被转换成unicode
		
			$files[] = $f;
		}
		if( count($files) < 1 ) return "";
		return json_encode($files);
	}
	
	/**
	 * 加载所有未下载完的文件和文件夹
	 * @param unknown $uid
	 * @return string
	 */
	function all_uncmp($uid)
	{
		$sql = 'select 
				 f_id
				,f_nameLoc
				,f_pathLoc
				,f_perLoc
				,f_sizeSvr				
				,f_fdTask
				 from down_files
				 where f_uid=:f_uid and f_complete=0;';
		
		$files = array();
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
				
		$cmd->bindValue(":f_uid",$uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{
			$f = new DnFileInf();
			$f->id 		= $row["f_id"];
			$f->nameLoc = PathTool::urlencode_path( $row["f_nameLoc"] );//防止汉字被转换成unicode
			$f->pathLoc = PathTool::urlencode_path( $row["f_pathLoc"] );//防止汉字被转换成unicode
			$f->perLoc 	= $row["f_perLoc"];
			$f->sizeSvr	= $row["f_sizeSvr"];			
			$f->fdTask 	= (bool)$row["f_fdTask"];
		
			$files[] = $f;
		}
		if( count($files) < 1 ) return "";
		return json_encode($files);
	}
}
?>