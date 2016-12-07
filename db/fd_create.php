<?php
ob_start();
header('Content-Type: text/html;charset=utf-8');
/*
	业务逻辑：
		1.向数据库添加文件和文件夹信息
		2.将文件和文件夹ID保存到JSON中
		3.将JSON返回给客户端。
	文件夹上传方法中调用。
	客户端上传的文件夹JSON格式：
    [
	     [name:"soft"		    //文件夹名称
	     ,pid:0                //父级ID
	     ,idLoc:0              //文件夹ID，客户端定义
	     ,idSvr:0              //文件夹ID，与数据库中的xdb_folder.fd_id对应。
	     ,length:"102032"      //数字化的文件夹大小，以字节为单位
	     ,size:"10G"           //格式化的文件夹大小
	     ,pathLoc:"d:/soft"   //文件夹在客户端的路径
	     ,pathSvr:"e:/web"    //文件夹在服务端的路径
	     ,foldersCount:0       //子文件夹总数
	     ,filesCount:0         //子文件总数
	     ,filesComplete:0      //已上传完成的子文件总数
	     ,folders:[
	           {name:"img1",pidLoc:0,pidSvr:10,idLoc:1,idSvr:0,pathLoc:"D:/Soft/img1",pathSvr:"E:/Web"}
	          ,{name:"img2",pidLoc:1,pidSvr:10,idLoc:2,idSvr:0,pathLoc:"D:/Soft/image2",pathSvr:"E:/Web"}
	          ,{name:"img3",pidLoc:2,pidSvr:10,idLoc:3,idSvr:0,pathLoc:"D:/Soft/image2/img3",pathSvr:"E:/Web"}
	          ]
	     ,files:[
	           {name:"f1.exe",md5:"857d5430f3355aad40ead12a06168de6",idLoc:0,idSvr:0,pidRoot:0,pidLoc:1,pidSvr:0,length:"100",size:"100KB",pathLoc:"",pathSvr:""}
	          ,{name:"f2.exe",md5:"8b3d850a3979b8f4bae8a0e8d7c1a512",idLoc:0,idSvr:0,pidRoot:0,pidLoc:1,pidSvr:0,length:"100",size:"100KB",pathLoc:"",pathSvr:""}
	          ,{name:"f3.exe",md5:"3bbb5dc01aff53b482820c2838043515",idLoc:0,idSvr:0,pidRoot:0,pidLoc:1,pidSvr:0,length:"100",size:"100KB",pathLoc:"",pathSvr:""}
	          ,{name:"f4.rar",md5:"243c74ae1356b96783f9c356058ed569",idLoc:0,idSvr:0,pidRoot:0,pidLoc:1,pidSvr:0,length:"100",size:"100KB",pathLoc:"",pathSvr:""}
	          ]
	]

	更新记录：
		2014-07-23 创建
		2014-08-05 修复BUG，上传文件夹如果没有子文件夹时报错的问题。
		2014-09-12 完成逻辑。
		2014-09-15 修复设置子文件，子文件夹层级结构错误的问题。
		2016-04-13 以md5模式上传文件夹
		2016-05-29 修复添加文件夹数据错误的问题。

	JSON格式化工具：http://tool.oschina.net/codeformat/json
	POST数据过大导致接收到的参数为空解决方法：http://sishuok.com/forum/posts/list/2048.html
*/
require('DbHelper.php');
require('inc.php');
require('DBFile.php');
require('DbFolder.php');
require('FileInf.php');
require('xdb_files.php');
require('PathTool.php');
require('biz/PathBuilder.php');
require('biz/PathMd5Builder.php');
require('FolderInf.php');
require('FdDataWriter.php');

$jsonTxt = $_POST["folder"];
$uidTxt = $_POST["uid"];

$jsonTxt = str_replace("+","%20",$jsonTxt);
$jsonTxt = urldecode($jsonTxt);//utf-8解码

//参数为空
if (	empty($jsonTxt)
	||	strlen($uidTxt)<1 )
{
	echo "param is null folder:$jsonTxt,uid:$uidTxt\n";
	die();
}

//解析成数组
$jsonArr = json_decode($jsonTxt,true);

$folders = array();
if( !empty($jsonArr["folders"]) )
{
	$folders = $jsonArr["folders"];
	array_remove_value($jsonArr,"folders");
}

$files = array();
if( !empty($jsonArr["files"]) )
{
	$files = $jsonArr["files"];
	array_remove_value($jsonArr,"files");
}

//将$jsonArr赋值给$fdroot
$fdroot 			= new FolderInf();
$fdroot->nameLoc	= $jsonArr["nameLoc"];
$fdroot->lenLoc 	= $jsonArr["lenLoc"];//部分php-32不支持int64
$fdroot->size 		= $jsonArr["size"];
$fdroot->lenSvr		= $jsonArr["lenSvr"];//php-32不支持int64
$fdroot->pidLoc 	= 0;
$fdroot->pidSvr 	= 0;
$fdroot->idLoc 		= (int)$jsonArr["idLoc"];
$fdroot->idSvr 		= (int)$jsonArr["idSvr"];
$fdroot->uid 		= intval($uidTxt);
$fdroot->pathSvr 	= $jsonArr["pathSvr"];
$fdroot->pathLoc 	= $jsonArr["pathLoc"];
$fdroot->filesCount = (int)$jsonArr["filesCount"];//
$fdroot->foldersCount = (int)$jsonArr["foldersCount"];//

$fd_writer = new FdDataWriter();
//分配文件和文件夹ID数
$ids = $fd_writer->make_ids_batch($fdroot->filesCount+1,$fdroot->foldersCount+1);
$fd_ids = explode(",",$ids["ids_fd"]);
$f_ids  = explode(",",$ids["ids_f"]);

$fdroot->idSvr 	= array_shift($fd_ids);//取一个文件夹ID
$fdroot->idFile = array_shift($f_ids);//取一个文件ID

$fd_writer->fd_update($fdroot);//更新文件夹数据
$fd_writer->f_update_fd($fdroot);//更新文件数据

$tbFolders = array();
$tbFolders[$fdroot->idLoc] = $fdroot;

$arrFolders = array();

//解析文件夹
foreach($folders as $folder)
{
	$fd 			= new FolderInf();
	$fd->nameLoc	= $folder["nameLoc"];
	$fd->idLoc 		= intval($folder["idLoc"]);
	$fd->idSvr 		= intval($folder["idSvr"]);
	$fd->pidRoot 	= 0;//$folder["pidRoot"];
	$fd->pidLoc		= (int)$folder["pidLoc"];
	$fd->pidSvr		= (int)$folder["pidSvr"];
	//$fd->lenLoc		= $folder["length"];
	//$fd->size		= $folder["size"];
	$fd->pathLoc	= $folder["pathLoc"];
	$fd->pathSvr	= $folder["pathSvr"];
	$fd->uid 		= intval($uidTxt);
			
	//查找父级文件夹
	$fdParent = $tbFolders[strval($fd->pidLoc)];		
	
	$fd->pidSvr = $fdParent->idSvr;
	$fd->idSvr = intval( array_shift($fd_ids) );//取一个文件夹ID
	//更新文件夹数据
	$fd_writer->fd_update($fd);
	
	$tbFolders[$fd->idLoc] = $fd;
	array_push($arrFolders,$fd);
}

$f_exist = new xdb_files();
$arrFiles = array();

//服务器已存在的文件
$files_svr = $fd_writer->find_files($files);

//如果文件非常多可能执行超时
set_time_limit(0);

//解析文件
foreach($files as $file)
{
	$fd				= $tbFolders[ intval($file["pidLoc"]) ];			
	$f				= new FileInf();
	$f->nameLoc		= $file["nameLoc"];
	$f->pathLoc		= $file["pathLoc"];
	$f->idLoc		= (int)$file["idLoc"];	
	$f->lenLoc		= (int)$file["lenLoc"];
	$f->sizeLoc		= $file["sizeLoc"];
	//$f->perSvr 	= $file["perSvr"];
	$f->lenSvr		= intval($file["lenSvr"]);
	$f->md5			= $file["md5"];
	$f->uid			= intval($uidTxt);
	$f->pidRoot		= $fdroot->idSvr;
	$f->pidSvr		= $fd->idSvr;
	$f->pidLoc		= $fd->idLoc;
	$f->nameSvr		= $f->md5 . "." . PathTool::getExtention($f->pathLoc);
	//生成文件路径
	$pb				= new PathMd5Builder();	
	$f->pathSvr		= $pb->genFile($f->uid, $f->md5,$f->nameLoc);

	//存在相同文件
	$f_exist = NULL;
	if( strlen($f->md5) > 0 )
	{
		if(array_key_exists($f->md5, $files_svr))
		{
			$f_exist = $files_svr[$f->md5];
		}
	}
	
	if( !empty($f_exist) )
	{
		$f->lenLoc 	= $f_exist["f_lenLoc"];
		$f->lenSvr 	= $f_exist["f_lenSvr"];
		$f->perSvr 	= $f_exist["f_perSvr"];
		$f->pathSvr = $f_exist["f_pathSvr"];
		$f->pathRel = $f_exist["f_pathRel"];
		$f->postPos = $f_exist["f_pos"];
		$f->complete = (bool)intval($f_exist["f_complete"]);
		$f->nameSvr = $f_exist["f_nameSvr"];
	}
	$f->idSvr = intval( array_shift($f_ids) );//取一个文件ID
	$fd_writer->f_update($f);//更新文件数据
	
	//fix:防止json_encode将汉字转换成unicode
	$f->nameLoc		= PathTool::urlencode_safe($f->nameLoc);
	$f->nameSvr		= PathTool::urlencode_safe($f->nameSvr);
	$f->pathLoc		= PathTool::urlencode_safe($f->pathLoc);
	$f->pathSvr		= PathTool::urlencode_safe($f->pathSvr);
	
	array_push($arrFiles,$f);
}

//转换为JSON
$fdroot->folders = $arrFolders;
$fdroot->files = $arrFiles;
$fdroot->complete = false;
$json = json_encode($fdroot);//bug:汉字被编码成了unicode
$json = urldecode( $json );//还原汉字

//将数组转换为JSON
$json = urlencode( $json );
//UrlEncode会将空格解析成+号，
$json = str_replace("+","%20",$json);

echo $json;
header('Content-Length: ' . ob_get_length());
?>