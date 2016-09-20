<?php
class DnFileInf
{
	var $idSvr = 0;
	var $uid = 0;
	var $pathLoc = "";
	var $fileUrl = "";
	var $lenLoc = 0;
	var $lenSvr = 0;
	var $sizeSvr = "";
	var $perLoc = "0%";
	var $complete = false;
	var $nameLoc = "";
	var $fdID = 0;//与up6_folder.fd_id对应
	var $fdTask = false;
	var $pidRoot = 0; 
	var $files = null;
	
	function __construct()
	{
	}
}
?>