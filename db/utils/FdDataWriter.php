<?php
/*
 * 更新记录：
 * 	2016-05-27 更名为FdDataWriter
 *  2016-05-29 将数据库操作改为批量操作，提高性能。
 *  2017-07-12 修改逻辑
 * */
class FdDataWriter
{
	var $db;
	var $cmd_fd_add = null;
	var $cmd_f_add = null;
	var $cmd_f_exist = null;
	var $con_utf8 = null;
	
	function __construct() 
	{
		$this->db = new DbHelper();
		$this->con_utf8 =& $this->db->GetConUtf8();
	}
	
	function add_file($inf/*FileInf*/)
	{
		if(empty($this->cmd_f_add))
		{
			//bug:prepare中如果有返回值，则再次执行会报错。无论是否取完都无法再次执行。
			$sql = "
					insert into up6_files(
					 f_id
					,f_pid
					,f_pidRoot
					,f_fdTask
					,f_fdChild
					,f_uid
					,f_nameLoc
					,f_nameSvr
					,f_pathLoc
					,f_pathSvr
					,f_pathRel
					,f_md5
					,f_lenLoc
					,f_sizeLoc
					,f_lenSvr
					,f_perSvr
					,f_complete
					)
					values(
					 :id
					,:pid
					,:pidRoot
					,:fdTask
					,:fdChild
					,:uid
					,:nameLoc
					,:nameSvr
					,:pathLoc
					,:pathSvr
					,:pathRel
					,:md5
					,:lenLoc
					,:sizeLoc
					,:lenSvr
					,:perSvr
					,:complete
					)
					";
			$con = $this->con_utf8;
			$cmd = $con->prepare($sql);
			$this->cmd_f_add = $cmd;
		}
		$cmd = $this->cmd_f_add;
		$cmd->bindParam(":id", $inf->id );
		$cmd->bindValue(":pid", $inf->pid );
		$cmd->bindValue(":pidRoot", $inf->pidRoot );
		$cmd->bindValue(":fdTask", $inf->fdTask,PDO::PARAM_BOOL);		
		$cmd->bindValue(":fdChild", $inf->fdChild,PDO::PARAM_BOOL);//是文件夹中的文件
		$cmd->bindValue(":uid", $inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":nameLoc", $inf->nameLoc);
		$cmd->bindParam(":nameSvr", $inf->nameSvr);
		$cmd->bindParam(":pathLoc", $inf->pathLoc);
		$cmd->bindParam(":pathSvr", $inf->pathSvr);
		$cmd->bindParam(":pathRel", $inf->pathRel);
		$cmd->bindParam(":md5", $inf->md5);
		$cmd->bindValue(":lenLoc", $inf->lenLoc);
		$cmd->bindParam(":sizeLoc", $inf->sizeLoc);
		$cmd->bindValue(":lenSvr", 0);
		$cmd->bindParam(":perSvr", $inf->perSvr);
		$cmd->bindValue(":complete", false,PDO::PARAM_BOOL);
		if($inf->lenLoc ==0 )
		{
			$cmd->bindValue(":lenSvr", $inf->lenLoc);
			$cmd->bindValue(":perSvr", "100%");
			$cmd->bindValue(":complete", true,PDO::PARAM_BOOL);
		}
		
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}
	
	function add_folder($inf/*FileInf*/)
	{

		if(empty($this->cmd_fd_add))
		{
			//bug:prepare中如果有返回值，则再次执行会报错。无论是否取完都无法再次执行。
			$sql = "
					insert into up6_folders(
					 fd_id
					,fd_pid
					,fd_pidRoot
					,fd_uid
					,fd_name
					,fd_pathLoc
					,fd_pathSvr
					,fd_pathRel
					)
					values(
					 :id
					,:pid
					,:pidRoot
					,:uid
					,:name
					,:pathLoc
					,:pathSvr
					,:pathRel
					)
					";
			$con = $this->con_utf8;
			$cmd = $con->prepare($sql);
			$this->cmd_fd_add = $cmd;
		}
		$cmd = $this->cmd_fd_add;
		$cmd->bindParam(":id", $inf->id );
		$cmd->bindValue(":pid", $inf->pid );
		$cmd->bindValue(":pidRoot", $inf->pidRoot);
		$cmd->bindValue(":uid", $inf->uid);//是文件夹中的文件
		$cmd->bindValue(":name", $inf->nameLoc);
		$cmd->bindValue(":pathLoc", $inf->pathLoc);
		$cmd->bindValue(":pathSvr", $inf->pathSvr);
		$cmd->bindValue(":pathRel", $inf->pathRel );
		
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}

	/*
	 * 使用独立连接
	 * $md5s a,b,c,d,e,f,g
	 * */
	function fd_files_check($md5s,$md5Len)
	{
		$con = $this->db->GetConUtf8();
		$cmd = $con->prepare("call fd_files_check(:md5s
				,:md5_len
				,:md5s_len)");	
		//$cmd = &$this->cmd_f_exist;
		$cmd->bindParam(":md5s", $md5s);
		$cmd->bindValue(":md5_len", $md5Len);
		$cmd->bindValue(":md5s_len", strlen($md5s));
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
		$md5Len = 32;//md5长度为32
		foreach($files as $f)
		{
			if(strlen($f["md5"]) > 0)
			{
				$md5Len = strlen($f["md5"]);
				$ids = $ids ."," .$f["md5"];
			}
		}
		
		if($ids == "0")
		{
			return array();
		}
		return $this->fd_files_check( substr($ids,2) ,$md5Len);
	}	
}
?>