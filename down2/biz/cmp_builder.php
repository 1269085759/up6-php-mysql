<?php
class cmp_builder
{
	var $folders = array();/*fdid,files索引*/
	var $files = array();//xdb_files
	
	function __construct()
	{
	}
	
	function read($uid)
	{
		$sql = "";
		$sql.= "select ";
		$sql.= " up6_files.f_id";//0
		$sql.= ",up6_files.f_pid";//1
		$sql.= ",up6_files.f_fdTask";//2
		$sql.= ",up6_files.f_fdID";//3
		$sql.= ",up6_files.f_fdChild";//4
		$sql.= ",up6_files.f_pidRoot";//5
		$sql.= ",up6_files.f_nameLoc";//6
		$sql.= ",up6_files.f_sizeLoc";//6
		$sql.= ",up6_files.f_pathLoc";//7
		$sql.= ",up6_files.f_lenSvr";//12
		$sql.= " from up6_files ";
		//
		$sql.= " where up6_files.f_uid=:f_uid and up6_files.f_deleted=0 and up6_files.f_complete=1";
		
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
        $f = new cmp_file();
        $f->read(0, $row);

        if ($f->fdTask)
        {            
        	if(array_key_exists($f->fdID, $this->folders))
            {            	 
            	$fd_index = (int)$this->folders[$f->fdID];
            	
                $this->files[$fd_index]->nameLoc 	= $f->nameLoc;
                $this->files[$fd_index]->pathLoc 	= $f->pathLoc;
                $this->files[$fd_index]->fileUrl 	= $f->fileUrl;
                $this->files[$fd_index]->lenLoc 	= $f->lenLoc;
                $this->files[$fd_index]->lenSvr 	= $f->lenSvr;
                $this->files[$fd_index]->sizeSvr 	= $f->sizeSvr;
                $this->files[$fd_index]->perLoc 	= $f->perLoc;
                $this->files[$fd_index]->fdTask 	= true;
                $this->files[$fd_index]->fdID 		= $f->fdID;
            }
            else
            {
                $f->files = array();
                $this->folders[$f->fdID] = count($this->files);
                $this->files[] = $f;
            }
        }//根级文件
        else
        {        	
            $this->files[] = $f;
        }
    }
	
	function add_child(&$row,$pidRoot)
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
			$json = urldecode($json);//将汉字还原
			return $json;
		}
		return  null;
	}
}
?>