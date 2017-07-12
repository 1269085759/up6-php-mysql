<?php
/*
	文件夹操作类		
	更新记录：
		2014-08-12 创建
*/
class DBFolder
{
	/// <summary>
	/// 向数据库添加一条记录
	/// </summary>
	/// <param name="inf"></param>
	/// <returns></returns>
	static function Add(&$folder/*FolderInf*/)
	{		
		$sb = "insert into up6_folders(";
		$sb = $sb. "fd_name";
		$sb = $sb. ",fd_pid";
		$sb = $sb. ",fd_uid";
		$sb = $sb. ",fd_length";
		$sb = $sb. ",fd_size";
		$sb = $sb. ",fd_pathLoc";
		$sb = $sb. ",fd_pathSvr";
		$sb = $sb. ",fd_folders";
		$sb = $sb. ",fd_files";

		$sb = $sb. ") values(";
		$sb = $sb. " :fd_name";//"@fd_name";
		$sb = $sb. ",:fd_pid";//",@pid";
		$sb = $sb. ",:fd_uid";//",@uid";
		$sb = $sb. ",:fd_length";//",@length";
		$sb = $sb. ",:fd_size";//",@size";
		$sb = $sb. ",:fd_pathLoc";//",@pathLoc";
		$sb = $sb. ",:fd_pathSvr";//",@pathSvr";
		$sb = $sb. ",:fd_folders";//",@folders";
		$sb = $sb. ",:fd_files";//",@files";
		$sb = $sb. ")";

		$db = new DbHelper();
		$cmd =& $db->GetCommand( $sb );
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":fd_name",$folder->nameLoc);
		$cmd->bindValue(":fd_pid",$folder->pidSvr,PDO::PARAM_INT);
		$cmd->bindValue(":fd_uid",$folder->uid,PDO::PARAM_INT);
		$cmd->bindValue(":fd_length",$folder->lenLoc,PDO::PARAM_INT);
		$cmd->bindParam(":fd_size",$folder->size);
		$cmd->bindParam(":fd_pathLoc",$folder->pathLoc);
		$cmd->bindParam(":fd_pathSvr",$folder->pathSvr);
		$cmd->bindValue(":fd_folders",$folder->foldersCount,PDO::PARAM_INT);
		$cmd->bindValue(":fd_files",$folder->filesCount,PDO::PARAM_INT);

		$db->ExecuteNonQuery($cmd);
		
		$folder->idSvr = $db->m_conCur->lastInsertId();//$db->ExecuteScalar("select @@IDENTITY");
		return $folder->idSvr;
	}

	/**
	 * 将文件夹上传状态设为已完成
	 * 更新文件表
	 * 更新文件夹表
	 * @param unknown $id	文件ID
	 * @param unknown $uid
	 */
	function Complete($id,$uid)
	{		
		$sql = "update up6_files set f_lenSvr=f_lenLoc,f_complete=1,f_perSvr='100%' where f_id=:f_id and f_uid=:f_uid;";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindValue(":f_id",$id);
		$cmd->bindValue(":f_uid",$uid);
		$db->ExecuteNonQuery($cmd);		
	}

	function Remove($fid,$uid)
	{
		$sql = "update up6_files set f_delete=1 where f_id=:id";		
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindValue(":id",$fid);		
		$db->ExecuteNonQuery($cmd);
		
		$sql = "update up6_folders set fd_delete=1 where fd_id=:fd_id and fd_uid=:fd_uid;";		
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":fd_id",$fid);
		$cmd->bindParam(":fd_uid",$uid);
		$db->ExecuteNonQuery($cmd);
	}

	static function Clear()
	{
		$sql = "delete from up6_folders";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 根据文件夹ID获取文件夹信息和未上传完的文件列表，转为JSON格式。
	/// 说明：
	/// </summary>
	/// <param name="fid"></param>
	/// <returns></returns>
	function GetFilesUnCompleteJson($fid,&$root)
	{
		$sb = "select ";
		$sb = $sb. "fd_name";
		$sb = $sb. ",fd_length";
		$sb = $sb. ",fd_size";
		$sb = $sb. ",fd_pid";
		$sb = $sb. ",fd_pathLoc";
		$sb = $sb. ",fd_pathSvr";
		$sb = $sb. ",fd_folders";
		$sb = $sb. ",fd_files";
		$sb = $sb. ",fd_filesComplete";
		$sb = $sb. " from up6_folders where fd_id=:fd_id;";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		$cmd->bindParam(":fd_id",$fid);
		$row = $db->ExecuteRow($cmd);		
		if ($row)
		{	
			$root->name 			= $row["fd_name"];
			$root->lenLoc 			= $row["fd_length"];
			$root->size 			= $row["fd_size"];
			$root->pidSvr 			= intval($row["fd_pid"]);
			$root->idSvr 			= $fid;
			$root->pathLoc 		= $row["fd_pathLoc"];
			$root->pathSvr 		= $row["fd_pathSvr"];
			$root->foldersCount 	= intval($row["fd_folders"]);
			$root->filesCount 		= intval($row["fd_files"]);
			$root->filesComplete 	= intval($row["fd_filesComplete"]);
		}

		//单独取已上传长度
		$root->lenSvr = DBFolder::GetLenPosted($fid);

		//取未完成的文件列表
		$files = array();
		DBFile::GetUnCompletesArr($fid,$files);
		$root->files = $files;		
		return json_encode($root);
	}

	/// <summary>
	/// 根据文件夹ID获取文件夹信息和未上传完的文件列表，转为JSON格式。
	/// </summary>
	/// <param name="fid"></param>
	/// <returns></returns>
	function GetFilesUnCompleteArr($fid)
	{
		$sb = "select ";
		$sb = $sb. "fd_name";
		$sb = $sb. ",fd_length";
		$sb = $sb. ",fd_size";
		$sb = $sb. ",fd_pid";
		$sb = $sb. ",fd_pathLoc";
		$sb = $sb. ",fd_pathSvr";
		$sb = $sb. ",fd_folders";
		$sb = $sb. ",fd_files";
		$sb = $sb. ",fd_filesComplete";
		$sb = $sb. " from up6_folders where fd_id=:fd_id;";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		$cmd->bindParam(":fd_id",$fid);
		$row = $db->ExecuteRow($cmd);
		
		$root = new FolderInf();
		if ($row)
		{
			$root->name 			= $row["fd_name"];
			$root->lenLoc 			= $row["fd_length"];
			$root->size 			= $row["fd_size"];
			$root->pidSvr 			= intval($row["fd_pid"]);
			$root->idSvr 			= $fid;
			$root->pathLoc 		= $row["fd_pathLoc"];
			$root->pathSvr 		= $row["fd_pathSvr"];
			$root->foldersCount 	= intval($row["fd_folders"]);
			$root->filesCount 		= intval($row["fd_files"]);
			$root->filesComplete 	= intval($row["fd_filesComplete"]);
		}

		//单独取已上传长度
		$root->lenSvr = DBFolder::GetLenPosted($fid);

		//取文件信息
		$files = array();
		DBFile::GetUnCompletesArr($fid,$files);

		$obj = json_encode($root);
		$obj["files"] = $files;
		return json_encode($obj);
	}

	function GetInf($fid)
	{
		$inf = new FolderInf();
		$this->GetInfRef($inf,$fid);
		return $inf;
	}

	/// <summary>
	/// 根据文件夹ID填充文件夹信息
	/// </summary>
	/// <param name="inf"></param>
	/// <param name="fid"></param>
	function GetInfRef(&$inf,$fid)
	{
		$ret = false;
		$sb = "select ";
		$sb = $sb. "fd_name";
		$sb = $sb. ",fd_length";
		$sb = $sb. ",fd_size";
		$sb = $sb. ",fd_pid";
		$sb = $sb. ",fd_pathLoc";
		$sb = $sb. ",fd_pathSvr";
		$sb = $sb. ",fd_folders";
		$sb = $sb. ",fd_files";
		$sb = $sb. ",fd_filesComplete";
		$sb = $sb. " from up6_folders where fd_id=:fd_id;";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		$cmd->bindParam(":fd_id",$fid);
		$row = $db->ExecuteRow($cmd);
		if ($row)
		{
			$inf->name 			= $row["fd_name"];
			$inf->lenLoc 			= $row["fd_length"];
			$inf->size 			= $row["fd_size"];
			$inf->pidSvr 			= intval($row["fd_pid"]);
			$inf->idSvr 			= $fid;
			$inf->pathLoc 			= $row["fd_pathLoc"];
			$inf->pathSvr 			= $row["fd_pathSvr"];
			$inf->foldersCount 		= intval($row["fd_folders"]);
			$inf->filesCount 		= intval($row["fd_files"]);
			$inf->filesComplete 	= intval($row["fd_filesComplete"]);
			$ret = true;
		}
		return $ret;
	}

	/// <summary>
	/// 获取文件夹已上传大小
	/// 计算所有文件已上传大小。
	/// </summary>
	/// <param name="fidRoot"></param>
	/// <returns></returns>
	function GetLenPosted($fidRoot)
	{
		$sql = "select sum(f_lenSvr) as lenPosted from (select distinct f_md5,f_lenSvr from up6_files where f_pidRoot=:f_pidRoot and LENGTH(f_md5) > 0) a";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_pidRoot",$fidRoot);
		$len = $db->ExecuteScalar($cmd);

		return empty( $len ) ? 0 : intval($len);
	}

        /// <summary>
        /// 子文件上传完毕
        /// </summary>
        /// <param name="fd_idSvr"></param>
        function child_complete($fd_idSvr)
        {
            $sql = "update up6_folders set fd_filesComplete=fd_filesComplete+1 where fd_id=:fd_id";
            $db = new DbHelper();
            $cmd =& $db->GetCommand($sql);
            $cmd->bindParam( ":fd_id", $fd_idSvr);
            $db->ExecuteNonQuery($cmd);
        }
}
?>