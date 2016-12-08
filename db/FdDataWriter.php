<?php
/*
 * 更新记录：
 * 	2016-05-27 更名为FdDataWriter
 *  2016-05-29 将数据库操作改为批量操作，提高性能。
 * */
class FdDataWriter
{
	var $db;
	var $cmd_fd_add = null;
	var $cmd_f_add = null;
	var $cmd_f_exist = null;
	var $cmd_f_update = null;
	var $cmd_fd_update = null;
	var $con_utf8 = null;
	
	function __construct() 
	{
		$this->db = new DbHelper();
		$this->con_utf8 =& $this->db->GetConUtf8();
	}	

	function f_update(&$inf/*FileInf*/)
	{
		if(empty($this->cmd_f_update))
		{
			//bug:prepare中如果有返回值，则再次执行会报错。无论是否取完都无法再次执行。
			//$this->cmd_fd_add =& $this->db->prepare_utf8("call fd_insert(:name,:pid,:uid,:length,:size,:pathLoc,:pathSvr,:folders,:files)");	
			$con = $this->con_utf8;
			$cmd = $con->prepare("call f_update(:pid,:pidRoot,:fdTask,:fdID,:fdChild,:uid,:nameLoc,:nameSvr,:pathLoc,:pathSvr,:md5,:lenLoc,:lenSvr,:perSvr,:sizeLoc,:complete,:id)");
			$this->cmd_f_update = $cmd;
		}	
		$cmd = $this->cmd_f_update;
		$cmd->bindValue(":pid", $inf->pidSvr,PDO::PARAM_INT);
		$cmd->bindValue(":pidRoot", $inf->pidRoot,PDO::PARAM_INT);
		$cmd->bindValue(":fdTask", false,PDO::PARAM_BOOL);
		$cmd->bindValue(":fdID", 0,PDO::PARAM_INT);
		$cmd->bindValue(":fdChild", true,PDO::PARAM_BOOL);//是文件夹中的文件
		$cmd->bindValue(":uid", $inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":nameLoc", $inf->nameLoc,PDO::PARAM_STR);
		$cmd->bindParam(":nameSvr", $inf->nameSvr,PDO::PARAM_STR);
		$cmd->bindParam(":pathLoc", $inf->pathLoc,PDO::PARAM_STR);
		$cmd->bindParam(":pathSvr", $inf->pathSvr,PDO::PARAM_STR);
		$cmd->bindParam(":md5", $inf->md5);
		$cmd->bindValue(":lenLoc", $inf->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":lenSvr", $inf->lenSvr,PDO::PARAM_INT);
		if($inf->lenLoc > 0)
		{
			$cmd->bindParam(":perSvr", $inf->perSvr);	
		}
		else
		{
			$cmd->bindValue(":perSvr", "100%");
		}
		$cmd->bindParam(":sizeLoc", $inf->sizeLoc);
		if($inf->lenLoc > 0)
		{
			$cmd->bindValue(":complete", $inf->complete,PDO::PARAM_BOOL);
		}
		else
		{
			$cmd->bindValue(":complete", true);
		}
		$cmd->bindValue(":id", $inf->idSvr,PDO::PARAM_INT);
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}
	
	/**
	 * 使用公共连接
	 * @param unknown $fd
	 */
	function f_update_fd(&$fd/*FolderInf*/)
	{
		if(empty($this->cmd_f_update))
		{
			//bug:prepare中如果有返回值，则再次执行会报错。无论是否取完都无法再次执行。
			//$this->cmd_fd_add =& $this->db->prepare_utf8("call fd_insert(:name,:pid,:uid,:length,:size,:pathLoc,:pathSvr,:folders,:files)");	
			$con = $this->con_utf8;
			$cmd = $con->prepare("call f_update(:pid,:pidRoot,:fdTask,:fdID,:fdChild,:uid,:nameLoc,:nameSvr,:pathLoc,:pathSvr,:md5,:lenLoc,:lenSvr,:perSvr,:sizeLoc,:complete,:id)");
			$this->cmd_f_update = $cmd;
		}	
		$cmd = $this->cmd_f_update;
		$cmd->bindValue(":pid", 0,PDO::PARAM_INT);
		$cmd->bindValue(":pidRoot", 0,PDO::PARAM_INT);
		$cmd->bindValue(":fdTask", true,PDO::PARAM_BOOL);
		$cmd->bindValue(":fdID", $fd->idSvr,PDO::PARAM_INT);
		$cmd->bindValue(":fdChild", false,PDO::PARAM_BOOL);
		$cmd->bindValue(":uid", $fd->uid,PDO::PARAM_INT);
		$cmd->bindParam(":nameLoc", $fd->nameLoc,PDO::PARAM_STR);
		$cmd->bindParam(":nameSvr", $fd->nameLoc,PDO::PARAM_STR);
		$cmd->bindParam(":pathLoc", $fd->pathLoc,PDO::PARAM_STR);
		$cmd->bindParam(":pathSvr", $fd->pathSvr,PDO::PARAM_STR);
		$cmd->bindValue(":md5", "");
		$cmd->bindValue(":lenLoc", $fd->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":lenSvr", 0,PDO::PARAM_INT);
		$cmd->bindValue(":perSvr", "0%");
		$cmd->bindParam(":sizeLoc", $fd->size);
		$cmd->bindValue(":complete", false,PDO::PARAM_BOOL);
		$cmd->bindValue(":id", $fd->idFile,PDO::PARAM_INT);
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}
	
	/*
	 * 测试：插入200条数据大约需要6秒
	 * 使用独立连接
	 * 文件ID列表：$ret["ids_f"]
	 * 文件夹ID列表：$ret["ids_fd"]
	 * */
	function make_ids_batch($files,$folders)
	{
		$con = $this->db->GetConUtf8();
		$cmd = $con->prepare("call fd_files_add_batch(:files,:folders);");
		$cmd->bindValue(":files", $files,PDO::PARAM_INT);
		$cmd->bindValue(":folders", $folders,PDO::PARAM_INT);
		if($cmd->execute())
		{
			$ret = $cmd->fetch(PDO::FETCH_ASSOC);
			return $ret;
		}
		else
		{
			print_r($cmd->errorInfo());
		}
	}
	
	/**
	 * 更新单个文件夹数据
	 * 使用共用数据库连接
	 * @param unknown $fd
	 */
	function fd_update(&$fd/*FolderInf*/)
	{
		if( empty($this->cmd_fd_update) )
		{
			//bug:prepare中如果有返回值，则再次执行会报错。无论是否取完都无法再次执行。
			//$this->cmd_fd_add =& $this->db->prepare_utf8("call fd_insert(:name,:pid,:uid,:length,:size,:pathLoc,:pathSvr,:folders,:files)");
			$con = $this->con_utf8;
			$cmd = $con->prepare("call fd_update(
					 :name
					,:pid
					,:uid
					,:length
					,:size
					,:pathLoc
					,:pathSvr
					,:folders
					,:files
					,:filesComplete
					,:complete
					,:delete
					,:pidRoot
					,:pathRel
					,:id)");
			$this->cmd_fd_update = $cmd;
		}
		
		$cmd = $this->cmd_fd_update;		
		
		$cmd->bindValue(":name", $fd->nameLoc,PDO::PARAM_STR);
		$cmd->bindValue(":pid", $fd->pidSvr,PDO::PARAM_INT);
		$cmd->bindValue(":uid", $fd->uid,PDO::PARAM_INT);
		$cmd->bindValue(":length", $fd->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":size", $fd->size,PDO::PARAM_STR);
		$cmd->bindValue(":pathLoc", $fd->pathLoc,PDO::PARAM_STR);
		$cmd->bindValue(":pathSvr", $fd->pathSvr,PDO::PARAM_STR);
		$cmd->bindValue(":folders", $fd->foldersCount,PDO::PARAM_INT);
		$cmd->bindValue(":files", $fd->filesCount,PDO::PARAM_INT);
		$cmd->bindValue(":filesComplete", $fd->filesComplete,PDO::PARAM_INT);
		$cmd->bindValue(":complete", $fd->lenSvr,PDO::PARAM_BOOL);
		$cmd->bindValue(":delete", false,PDO::PARAM_BOOL);
		$cmd->bindValue(":pidRoot", $fd->pidRoot,PDO::PARAM_INT);
		$cmd->bindValue(":pathRel", $fd->pathRel,PDO::PARAM_STR);
		$cmd->bindValue(":id", $fd->idSvr,PDO::PARAM_INT);
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
		
	}
	
	/*
	 * 批量更新文件夹数据
	 * */
	function fd_update_batch(&$folders/*FolderInf*/)
	{		
		foreach ($folders as $fd)
		{
			$this->fd_update($fd);
		}
	}

	/*
	 * 使用独立连接
	 * $md5s a,b,c,d,e,f,g
	 * */
	function f_exist_batch($md5s)
	{
		$con = $this->db->GetConUtf8();
		$cmd = $con->prepare("call f_exist_batch(:md5s)");	
		//$cmd = &$this->cmd_f_exist;
		$cmd->bindParam(":md5s", $md5s);
		$cmd->execute();
		$rows = $cmd->fetchAll(PDO::FETCH_ASSOC);
		$files = array();
		foreach ($rows as $f)
		{
			$files[$f["f_md5"]] = $f;
		}
		return $files;
	}
	
	/**
	 * 根据MD5批量查询数据
	 * @param unknown $files
	 */
	function find_files(&$files/**/)
	{
		$ids = "0";
		foreach($files as $f)
		{
			if(strlen($f["md5"]) > 0)
			{
				$ids = $ids ."," .$f["md5"];
			}
		}
		
		if($ids == "0")
		{
			return $files;	
		}
		return $this->f_exist_batch( substr($ids,2) );
	}	
}
?>