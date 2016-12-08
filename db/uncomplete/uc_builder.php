<?php
class uc_builder
{
	var $folders = array();
	var $files = array();//xdb_files
	
	function __construct()
	{
	}
	
	function add_file(&$row,$uid)
	{
		$f = new xdb_files();
        $f->uid       = $uid;
        $f->idSvr     = (int)$row["f_id"];
        $f->fdTask    = (bool)$row["f_fdTask"];//r.getBoolean(3);
        $f->fdID      = (int)$row["f_fdID"];//r.getInt(4);
        $f->nameLoc   = $row["f_nameLoc"];//r.getString(7);
        $f->pathLoc   = $row["f_pathLoc"];//r.getString(8);
        $f->md5       = $row["f_md5"];//r.getString(9).trim();
        $f->lenLoc    = (int)$row["f_lenLoc"];//r.getLong(10);
        $f->sizeLoc   = $row["f_sizeLoc"];//r.getString(11);
        $f->FilePos   = (int)$row["f_pos"];//r.getLong(12);
        $f->lenSvr    = (int)$row["f_lenSvr"];//r.getLong(13);
        $f->perSvr    = $row["f_perSvr"];//r.getString(14);
        $f->complete  = (bool)$row["f_complete"];//r.getBoolean(15);
        $f->pathSvr   = $row["f_pathSvr"];//r.getString(16);//fix(2015-03-19):修复无法续传文件的问题。
        $this->files[] = $f;
	}
	
	function update_folder(&$row,$fd_id)
	{
		$key = strval($fd_id);
   		$fd = null;//
    	if(array_key_exists ($key,$this->folders))
    	{
    		$fd = $this->folders[$key];
    	}
    	else
    	{
    		$fd = new uc_folder();
    	}

        $fdSvr = new FolderInf();
        $fdSvr->filesComplete = (bool)$row["fd_filesComplete"];//r.getInt(25);
        $fdSvr->filesCount = (int)$row["fd_files"];//r.getInt(24);
        $fdSvr->foldersCount = (int)$row["fd_folders"];//r.getInt(23);
        $fdSvr->idFile 	= (int)$row["f_id"];//r.getInt(1);
        $fdSvr->idSvr 	= (int)$row["f_fdID"];//r.getInt(4);
        $fdSvr->lenLoc 	= (int)$row["f_lenLoc"];//r.getLong(10);
        $fdSvr->lenSvr 	= (int)$row["f_lenSvr"];//r.getLong(13);
        $fdSvr->perSvr 	= $row["f_perSvr"];//r.getString(14);
        $fdSvr->pathLoc = $row["fd_pathLoc"];//r.getString(21);
        $fdSvr->pathSvr = $row["fd_pathSvr"];//r.getString(22);
        $fdSvr->size 	= $row["fd_size"];//r.getString(19);
        $fdSvr->name 	= $row["fd_name"];//r.getString(17);


        $fd->m_fdSvr = $fdSvr;
        $this->folders[$key] = $fd;
	}
	
	function add_child(&$row,$pidRoot)
	{		
    	$key = strval($pidRoot);
        
        $fd = null;
        if (array_key_exists($key,$this->folders))
        {
            $fd = $this->folders[$key];
        }
        else 
        {
        	$fd = new uc_folder();
        }
		
        $uf = new uc_file_child();
        $uf->read($pidRoot, $row);
        $fd->m_files[] = $uf;
		
        $this->folders[$key] = $fd;
	}
	function to_json()
	{
		$sz = count($this->files);
		if($sz < 1) return null;		
		 
		foreach ($this->files as $file)		
		{
			if ($file->fdTask)
			{
				$fd = null;
				$fdKey = strval($file->fdID);
				if( array_key_exists($fdKey,$this->folders) )
				{
					$fd = $this->folders[$fdKey];
					$file->perSvr = $fd->m_fdSvr->perSvr;
					$file->fd_json = $fd->getJson();
				}
			}		
		}
		 
		return json_encode($this->files);
	}
}
?>