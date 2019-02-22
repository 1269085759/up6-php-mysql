<?php
class DnFileInf extends FileInf
{
	var $f_id = "";
	var $fileUrl = "";
	var $sizeSvr = "0byte";
	var $perLoc = "0%";
	
	function __construct()
	{
		$this->fdTask = false;
	}
}
?>