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
		$sql = "update up6_files set f_lenSvr=f_lenLoc,f_complete=1,f_perSvr='100%' where (f_id=:f_id or f_pidRoot=:f_id)and f_uid=:f_uid;";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindValue(":f_id",$id);
		$cmd->bindValue(":f_uid",$uid);
		$db->ExecuteNonQuery($cmd);		
		
		$sql = "update up6_folders set fd_complete=1 where fd_id=:fd_id and fd_uid=:fd_uid";
		$cmd =& $db->GetCommand($sql);
		$cmd->bindValue(":fd_id",$id);
		$cmd->bindValue(":fd_uid",$uid);
		$db->ExecuteNonQuery($cmd);
	}

	function Remove($fid,$uid)
	{
		$sql = "update up6_files set f_deleted=1 where f_id=:id and f_uid=:uid";		
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindValue(":id",$fid);
		$cmd->bindValue(":uid",$uid);
		$db->ExecuteNonQuery($cmd);
		
		$sql = "update up6_files set f_deleted=1 where f_pidRoot=:f_pidRoot and f_uid=:f_uid";
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_pidRoot",$fid);
		$cmd->bindValue(":f_uid",$uid);
		$db->ExecuteNonQuery($cmd);
		
		$sql = "update up6_folders set fd_delete=1 where fd_id=:fd_id and fd_uid=:fd_uid";		
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":fd_id",$fid);
		$cmd->bindValue(":fd_uid",$uid);
		$db->ExecuteNonQuery($cmd);
	}

	static function Clear()
	{
		$sql = "delete from up6_folders";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$db->ExecuteNonQuery($cmd);
	}
}
?>