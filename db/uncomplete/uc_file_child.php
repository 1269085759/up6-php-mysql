<?php
class uc_file_child extends FileInf
{
	function __construct()
	{
	}

	function read($pidRoot,$row)
	{
		$this->idSvr 	= (int)$row["f_id"];
		$this->nameLoc 	= $row["f_nameLoc"];
		$this->pathLoc 	= $row["f_pathLoc"];
		$this->pathSvr 	= $row["f_pathSvr"];
		$this->lenLoc 	= (int)$row["f_lenLoc"];
		$this->lenSvr 	= (int)$row["f_lenSvr"];
		$this->perSvr 	= $row["f_perSvr"];
		$this->sizeLoc 	= $row["f_sizeLoc"];
		$this->md5 		= $row["f_md5"];
		$this->pidRoot 	= $pidRoot;
		$this->pidSvr 	= (int)$row["f_pid"];
	}
}
?>