<?php
class folder_appender
{
	function __construct()
	{
	}

	function add($fd/*DnFolderInf*/)
	{
		$sql = "call fd_add_batch(:fCount,:uid);";
		$db = new DbHelper();
		$cmd = $db->GetCommand($sql);
		$cmd->bindValue(":fCount",count($fd->files)+1,PDO::PARAM_INT);
		$cmd->bindValue(":uid",$fd->uid,PDO::PARAM_INT);
			
		if($cmd->execute())
		{
			$row = $cmd->fetch(PDO::FETCH_ASSOC);
			$f_ids = $row["f_ids"];
		}
		else
		{
			print_r($cmd->errorInfo());
			die();
		}
		
		//批量更新文件
		$sql  = "update down_files set ";
		$sql .= " f_nameLoc=:f_nameLoc";
		$sql .= ",f_pathLoc=:f_pathLoc";
		$sql .= ",f_fileUrl=:f_fileUrl";
		$sql .= ",f_lenSvr=:f_lenSvr";
		$sql .= ",f_sizeSvr=:f_sizeSvr";
		$sql .= ",f_pidRoot=:f_pidRoot";
		$sql .= ",f_fdTask=:f_fdTask";
		$sql .= " where f_id=:f_id";
		
		$cmd = $db->prepare_utf8($sql);
		$cmd->bindParam(":f_nameLoc", $fd->nameLoc);
		$cmd->bindParam(":f_pathLoc", $fd->pathLoc);
		$cmd->bindParam(":f_fileUrl", $fd->fileUrl);
		$cmd->bindValue(":f_lenSvr", $fd->lenSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_sizeSvr", $fd->sizeSvr);
		$cmd->bindValue(":f_pidRoot", $fd->pidRoot,PDO::PARAM_INT);
		$cmd->bindValue(":f_fdTask", $fd->fdTask,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_id", $fd->idSvr,PDO::PARAM_INT);
		
		$ids = explode(",", $f_ids);
					
		//更新文件夹
		$fd->idSvr = intval( $ids[0]);
		$fd->fdTask = true;
		$this->update_file($cmd, $fd);
		//对路径和名称进行编码，防止json_encode将汉字编码成unicode
		$fd->nameLoc = PathTool::urlencode_path($fd->nameLoc);
		$fd->pathLoc = PathTool::urlencode_path($fd->pathLoc);

		//更新文件
		for($i = 1,$f_index=0 , $l = count($ids);$i< $l;++$i,++$f_index)
		{			
			$fd->files[$f_index]->idSvr = (int)($ids[$i]);
			$fd->files[$f_index]->pidRoot = (int)$fd->idSvr;

			$this->update_file($cmd, $fd->files[$f_index]);
			//对路径进行编码，防止json_encode将汉字编码成unicode
			$fd->files[$f_index]->pathLoc = PathTool::urlencode_path($fd->files[$f_index]->pathLoc);
			$fd->files[$f_index]->nameLoc = PathTool::urlencode_path($fd->files[$f_index]->nameLoc);
		}            
	}

	function update_file($cmd,$f/*DnFileInf*/)
	{		
		$cmd->bindParam(":f_nameLoc", $f->nameLoc);
		$cmd->bindParam(":f_pathLoc", $f->pathLoc);
		$cmd->bindParam(":f_fileUrl", $f->fileUrl);
		$cmd->bindValue(":f_lenSvr", $f->lenSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_sizeSvr", $f->sizeSvr);
		$cmd->bindValue(":f_pidRoot", $f->pidRoot,PDO::PARAM_INT);
		$cmd->bindValue(":f_fdTask", $f->fdTask,PDO::PARAM_BOOL);
		$cmd->bindValue(":f_id", $f->idSvr,PDO::PARAM_INT);
		$cmd->execute();
	}
}
?>