<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2014-08-12 创建
*/
class FileInf 
{	
	var $nameLoc = "";			/// 文件名称。示例：QQ2014.exe
	var $nameSvr = "";
	var $pathLoc = "";		/// 文件在客户端中的路径。示例：D:\\Soft\\QQ2013.exe
	var $pathSvr = "";		/// 文件在服务器上面的路径。示例：E:\\Web\\Upload\\QQ2013.exe
	var $pathRel = "";
	var $pidLoc = 0;		/// 客户端父ID(文件夹ID)
	var $pidSvr = 0;		/// 服务端父ID(文件夹在数据库中的ID)
	var $pidRoot = 0;		/// 根级文件夹ID，数据库ID，与xdb_folders.fd_id对应
	var $idLoc = 0;			/// 本地文件ID。
	var $idSvr = 0;			/// 文件在服务器中的ID。
	var $uid = 0;			/// 用户ID
	var $lenLoc = 0;		/// 数字化的长度。以字节为单位，示例：1021021
	var $sizeLoc = "0bytes";	/// 格式化的长度。示例：10G
	var $postPos = 0;	/// 文件上传位置。
	var $perSvr = "0%";/// 上传百分比
	var $lenSvr = 0;	/// 已上传大小
	var $md5 = "";			/// 文件MD5
	var $complete = false;
	
	function __construct()
	{
	}
}
?>