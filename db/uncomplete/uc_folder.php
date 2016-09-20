<?php
class uc_folder
{
	var $m_fdSvr;//FolderInf
	var $m_files = array();//uc_file_child
	
	function __construct(){}
	
	function getJson()
	{
		$this->m_fdSvr->files = $this->m_files;
		$obj = json_encode($this->m_fdSvr);
		return $obj;		
	}
}
?>