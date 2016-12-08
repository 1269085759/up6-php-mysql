<?php
/*
	更新记录：
		2014-08-12 更新
*/
class xdb_files
{
	var $idSvr=0;
	/**
	 * 文件夹ID
	 */
	var $pid=0;
    /**
     * 根级文件夹ID
     */
    var $pidRoot=0;
	/**
	 * 表示当前项是否是一个文件夹项。
	 */
	var $fdTask;
	/**
	 * 与xdb_folders.fd_id对应
	 */
	var $fdID=0;
	/// <summary>
	/// 是否是文件夹中的子文件
	/// </summary>
	var $f_fdChild;
	/**
	 * 用户ID。与第三方系统整合使用。
	 */
	var $uid=0;
	/**
	 * 文件在本地电脑中的名称
	 */
	var $nameLoc="";
	/**
	 * 文件在服务器中的名称。
	 */
	var $nameSvr="";
	/**
	 * 文件在本地电脑中的完整路径。示例：D:\Soft\QQ2012.exe
	 */
	var $pathLoc="";
	/**
	 * 文件在服务器中的完整路径。示例：F:\\ftp\\uer\\md5.exe
	 */
	var $pathSvr="";
	/**
	 * 文件在服务器中的相对路径。示例：/www/web/upload/md5.exe
	 */
	var $pathRel="";
	/**
	 * 文件MD5
	 */
	var $md5="";
	/**
	 * 数字化的文件长度。以字节为单位，示例：120125
	 */
	var $lenLoc=0;
	/**
	 * 格式化的文件尺寸。示例：10.03MB
	 */
	var $sizeLoc="";
	/**
	 * 文件续传位置。
	 */
	var $FilePos=0;
	/**
	 * 已上传大小。以字节为单位
	 */
	var $lenSvr=0;
	/**
	 * 已上传百分比。示例：10%
	 */
	var $perSvr="";
	var $complete=false;
	var $PostedTime;
	var $deleted=false;
	/**
	 * 文件夹JSON信息
	 */
	var $fd_json="";
	
	//提供给文件夹使用的变量
	//文件总数
	var $filesCount=0;
	//已经完成的文件数
	var $filesComplete=0;
	
	function __construct()
	{
		date_default_timezone_set("PRC");//fix(2016-12-06):在部分server中提示警告
		$this->PostedTime = getdate();
	}
}
?>