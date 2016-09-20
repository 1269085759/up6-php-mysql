<?php
class un_file extends DnFileInf
{	
	function __construct()
	{
	}
	
	function read($pidRoot,$row)
	{
		$this->idSvr 	= (int)$row["f_id"];
		$this->nameLoc 	= $row["f_nameLoc"];
		$this->nameLoc	= PathTool::urlencode_path($this->nameLoc);//防止json_encode将汉字转换为unicode
		$this->pathLoc 	= $row["f_pathLoc"];
		$this->pathLoc	= PathTool::urlencode_path($this->pathLoc);//防止json_encode将汉字转换为unicode
		$this->lenLoc 	= (int)$row["f_lenLoc"];
		$this->perLoc 	= $row["f_perLoc"];
		$this->lenSvr 	= (int)$row["f_lenSvr"];
		$this->sizeSvr 	= $row["f_sizeSvr"];
		$this->fileUrl 	= $row["f_fileUrl"];
		$this->pidRoot 	= (int)$row["f_pidRoot"];
		$this->fdTask 	= (bool)$row["f_fdTask"];		
	}
}
?>