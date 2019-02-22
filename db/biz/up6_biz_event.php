<?php


class up6_biz_event 
{
	static function file_create_same(&$inf/*FileInf*/){}
	static function file_create(&$inf/*FileInf*/){}
	static function file_post_complete($id){}
	static function file_post_block($id, $blockIndex){}
	static function file_post_process($id){}
	static function folder_create(&$fd/*fd_root*/){}
	static function folder_post_complete($id){}
	static function file_del($id, $uid){}
}

?>