<?php
class cmp_file extends DnFileInf
{	
	function __construct()
	{
	}
	
	function read($pidRoot,$row)
	{
        $this->idSvr 	= (int)$row["f_id"];//与up6_files.f_id对应，f_down.aspx用到
        $this->nameLoc 	= $row["f_nameLoc"];
        $this->nameLoc	= PathTool::urlencode_path($this->nameLoc);//防止json_encode将汉字编码成unicode
        //$this->nameLoc  = str_replace("+", "%20", $this->nameLoc);
        
        $this->pathLoc 	= $row["f_pathLoc"];//
        $this->pathLoc	= PathTool::urlencode_path($this->pathLoc);//防止json_encode将汉字编码成unicode
        //$this->pathLoc  = str_replace("+", "%20", $this->pathLoc);
        
        $this->lenSvr 	= (int)$row["f_lenSvr"];
        $this->sizeSvr 	= $row["f_sizeLoc"];
        $this->pidRoot 	= $pidRoot;
        $this->fdTask 	= (bool)$row["f_fdTask"];
        $this->fdID 	= (int)$row["f_fdID"];        
	}
}
?>