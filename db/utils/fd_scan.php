<?php

class fd_scan
{
	var $db;
	var $cmd_fd_add = null;
	var $cmd_f_add = null;
	var $con_utf8 = null;
	
	function __construct() 
	{
		$this->db = new DbHelper();
		$this->con_utf8 =& $this->db->GetConUtf8();
	}
	
	function GetAllFiles($inf,$root)
	{
		$dir = iconv('UTF-8','gbk',$inf->pathSvr);
		$dir_handle=opendir($dir);
		if($dir_handle)
		{
			while(($file=readdir($dir_handle))!==false)
			{
				if($file==='.' || $file==='..')
				{
					continue;
				}
				$tmp=realpath($inf->pathSvr.'/'.$file);
				if(is_dir($tmp))
				{
					$fd = new FileInf();
					$pb = new PathBuilderUuid();
					$fd->id = $pb->guid();
					$fd->pid = $inf->id;
					$fd->pidRoot = $inf->pidRoot;
					$fd->nameSvr = iconv("gbk","utf-8",$file);
					$fd->pathSvr = iconv("gbk","utf-8",realpath(iconv('UTF-8','gbk',$inf->pathSvr) . '/' . $file));
					$fd->pathSvr = str_replace("\\", "/", $fd->pathSvr);
					$fd->pathRel = substr($fd->pathSvr, strlen($root) + 1);
					$fd->perSvr = "100%";
					$fd->complete = true;
					$this->save_folder($fd);
					
					$this->GetAllFiles($fd, $root);
					
				} else
				{
					$fl = new FileInf();
					$pb = new PathBuilderUuid();
					$fl->id = $pb->guid();
					$fl->pid = $inf->id;
					$fl->pidRoot = $inf->pidRoot;
					$fl->nameSvr = iconv("gbk","utf-8",$file);
					$fl->pathSvr = iconv("gbk","utf-8",realpath(iconv('UTF-8','gbk',$inf->pathSvr) . '/' . $file));
					$fl->pathSvr = str_replace("\\","/", $fl->pathSvr);
					$fl->pathRel = substr($fl->pathSvr, strlen($root) + 1);
					$fl->lenSvr = filesize(iconv('UTF-8','gbk',$fl->pathSvr));
					$fl->lenLoc = $fl->lenSvr;
					$fl->perSvr = "100%";
					$fl->complete = true;
					$this->save_file($fl);
				}
			}
			closedir($dir_handle);
		}		
	}
	
	function save_file($inf/*FileInf*/)
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
		$cmd->bindValue(":fdChild", true);//是文件夹中的文件
		$cmd->bindValue(":uid", $inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":nameLoc", $inf->nameLoc);
		$cmd->bindParam(":nameSvr", $inf->nameSvr);
		$cmd->bindParam(":pathLoc", $inf->pathLoc);
		$cmd->bindParam(":pathSvr", $inf->pathSvr);
		$cmd->bindParam(":pathRel", $inf->pathRel);
		$cmd->bindParam(":md5", $inf->md5);
		$cmd->bindValue(":lenLoc", $inf->lenLoc);
		$cmd->bindParam(":sizeLoc", $inf->sizeLoc);
		$cmd->bindValue(":lenSvr", $inf->lenSvr);
		$cmd->bindParam(":perSvr", $inf->perSvr);
		$cmd->bindValue(":complete", $inf->complete,PDO::PARAM_BOOL);
		
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}
	
	function save_folder($inf/*FileInf*/)
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
					,fd_complete
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
					,:complete
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
		$cmd->bindValue(":name", $inf->nameSvr);
		$cmd->bindValue(":pathLoc", $inf->pathLoc);
		$cmd->bindValue(":pathSvr", $inf->pathSvr);
		$cmd->bindValue(":pathRel", $inf->pathRel );
		$cmd->bindValue(":complete", $inf->complete,PDO::PARAM_BOOL);
		
		if(!$cmd->execute())
		{
			print_r($cmd->errorInfo());
		}
	}

	function scan($inf, $root)
	{
		$this->GetAllFiles($inf, $root);
	}	
}
?>