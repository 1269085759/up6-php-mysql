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
		2016-04-13 从uuid模式创建文件夹
		2016-05-29 优化数据库操作逻辑，将文件，文件夹操作改为批量操作，提高效率。
		2017-04-19 完善对中文的支持。
		2017-07-11 
			取消文件更新操作
			取消文件夹更新操作
			取消ID生成操作

	JSON格式化工具：http://tool.oschina.net/codeformat/json
	POST数据过大导致接收到的参数为空解决方法：http://sishuok.com/forum/posts/list/2048.html
*/
require('database/DbHelper.php');
require('utils/inc.php');
require('database/DBFile.php');
require('database/DBFolder.php');
require('model/FileInf.php');
require('model/FolderInf.php');
require('utils/PathTool.php');
require('utils/FileResumer.php');
require('biz/PathBuilder.php');
require('biz/PathBuilderMd5.php');
require('biz/PathBuilderUuid.php');
require('utils/FdDataWriter.php');

$jsonTxt = $_POST["folder"];
$uid 	 = $_POST["uid"];

$jsonTxt = str_replace("+","%20",$jsonTxt);
//客户端使用的是encodeURIComponent编码，
$jsonTxt = urldecode($jsonTxt);//utf-8解码


//参数为空
if (	empty($jsonTxt)
	||	strlen($uid)<1 )
{
	echo "param is null folder:$jsonTxt,uid:$uid\n";
	die();
}

//解析成数组
$jsonArr = json_decode($jsonTxt,true);


$folders = array();
if( !empty($jsonArr["folders"]) )
{
	$folders = $jsonArr["folders"];
}

$files = array();
if( !empty($jsonArr["files"]) )
{
	$files = $jsonArr["files"];
}

//将$jsonArr赋值给$fdroot
$fdroot 			= new FolderInf();
$fdroot->nameLoc	= PathTool::unicode_decode( $jsonArr["nameLoc"] );
$fdroot->nameSvr	= $fdroot->nameLoc; 
$fdroot->lenLoc 	= $jsonArr["lenLoc"];//fix:php32不支持int64
$fdroot->sizeLoc	= $jsonArr["sizeLoc"];
$fdroot->sizeLoc	= str_replace("+", " ", $fdroot->sizeLoc);
$fdroot->lenSvr		= $jsonArr["lenSvr"];//fix:php32不支持int64
$fdroot->id 		= $jsonArr["id"];
$fdroot->uid 		= intval($uid);
$fdroot->pidRoot	= $fdroot->id;
$fdroot->pathSvr 	= "";
$fdroot->pathLoc 	= PathTool::urldecode_path($jsonArr["pathLoc"] );
if( $fdroot->lenLoc == "0") $fdroot->complete = true;

//创建文件夹
$pb = new PathBuilderUuid();
$fdroot->pathSvr = PathTool::to_utf8( $pb->genFolder($uid, $fdroot) );
$fdroot->pathSvr = str_replace("\\", "/", $fdroot->pathSvr);

$fd_writer = new FdDataWriter();
$fd_writer->add_folder($fdroot);//添加根目录
$fd_writer->add_file($fdroot);//

$fdroot->pathSvr = PathTool::urlencode_safe($fdroot->pathSvr);

$fdroot->complete = false;
//fix(2017-04-19):增加对空文件夹的处理
if( $fdroot->lenLoc == 0 ) $fdroot->complete = true;
$json = json_encode($fdroot);//fix:汉字被编码成了unicode
$json = urldecode( $json );

//将数组转换为JSON
$json = urlencode( $json );
//UrlEncode会将空格解析成+号，
$json = str_replace("+","%20",$json);

echo $json;
header('Content-Length: ' . ob_get_length());
?>