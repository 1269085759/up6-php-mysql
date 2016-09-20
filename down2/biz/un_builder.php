<?php
class un_builder
{
	var $folders = array();//id,files_index
	var $files = array();//xdb_files
	
	function __construct()
	{
	}
	
	function read($uid)
	{
		$sql = "";
		$sql.= "select ";
		$sql.= " f_id";//0
		$sql.= ",f_nameLoc";//1
		$sql.= ",f_pathLoc";//2
		$sql.= ",f_perLoc";//3
		$sql.= ",f_lenLoc";//4
		$sql.= ",f_fileUrl";//5
		$sql.= ",f_lenSvr";//6
		$sql.= ",f_sizeSvr";//7
		$sql.= ",f_pidRoot";//8
		$sql.= ",f_fdTask";//9
		$sql.= " from down_files";
		//
		$sql.= " where f_uid=:f_uid and f_complete=0";
		
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sql);
		$cmd->bindParam(":f_uid",$uid);
		
		$ret = $db->ExecuteDataSet($cmd);
	
		foreach($ret as $row)
		{
			$pidRoot = intval($row["f_pidRoot"]);
		
			//是一个子文件
			if ($pidRoot != 0)
			{
				$this->add_child($row, $pidRoot);
			}//是一个文件项
			else
			{
				$this->add_file($row, $uid);
			}
		}		
		
		return $this->to_json();//
	}
	
	function add_file($row, $uid)
    {
        $f = new un_file();
        $f->read(0, $row);

        if ($f->fdTask)
        {            
            if(array_key_exists($f->idSvr, $this->folders))
            {
            	$fd_index = $this->folders[$f->idSvr];
            	
                $this->files[$fd_index]->nameLoc 	= $f->nameLoc;
                $this->files[$fd_index]->pathLoc 	= $f->pathLoc;
                $this->files[$fd_index]->fileUrl 	= $f->fileUrl;
                $this->files[$fd_index]->lenLoc 	= $f->lenLoc;
                $this->files[$fd_index]->lenSvr 	= $f->lenSvr;
                $this->files[$fd_index]->sizeSvr 	= $f->sizeSvr;
                $this->files[$fd_index]->perLoc 	= $f->perLoc;
                $this->files[$fd_index]->fdTask 	= true;
            }
            else
            {
                $f->files = array();
                $this->folders[$f->idSvr] = count($this->files);
                $this->files[] = $f;
            }
        }//根级文件
        else
        {
            $this->files[] = $f;
        }
    }
	
	function add_child($row,$pidRoot)
	{
        $f = new cmp_file();
        $f->read($pidRoot, $row);//

        
        //不存在文件夹
        if (! array_key_exists($pidRoot,$this->folders) )
        {
            $fd = new cmp_file();
            $fd->fdTask = true;
            $fd->idSvr = $pidRoot;
            $fd->files = array();
            $fd->files[] = $f;

            $this->folders[$pidRoot] = count($this->files);
            $this->files[] = $fd;
        }//存在文件夹
        else
        {
        	$fd_index = $this->folders[$pidRoot];
        	$this->files[$fd_index]->files[] = $f;            
        }
	}
	
	function to_json()
	{
		if(count($this->files) > 0)
		{
			$json = json_encode($this->files);
			$json = urldecode($json);//还原汉字
			return $json;			
		}
		return  null;
	}
}
?>