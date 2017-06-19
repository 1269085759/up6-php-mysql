<?php
require('DbHelper.php');
require('inc.php');
/**
	数据库初始化
	更新记录：
		201-06-19 创建
 */

$rootDir = dirname(dirname(__FILE__));
$sqlDir = "$rootDir\\sql";
$downDir = "$rootDir\\sql.down";
echo "SQL脚本路径：$sqlDir<br/>";
$files = scandir($sqlDir);
$db = new DbHelper();
//清理操作
$sql_clear = array(
		 "DROP PROCEDURE if exists fd_process"
		,"DROP PROCEDURE if exists f_update"
		,"DROP PROCEDURE if exists fd_files_add_batch"
		,"DROP PROCEDURE if exists fd_files_check"
		,"DROP PROCEDURE if exists fd_add_batch"
		,"DROP PROCEDURE if exists fd_update"
		,"DROP PROCEDURE if exists f_process"
		,"DROP PROCEDURE if exists fd_process"
		,"DROP TABLE IF EXISTS up6_files"
		,"DROP TABLE IF EXISTS up6_folders"
		,"DROP TABLE IF EXISTS down_files"
		,"DROP TABLE IF EXISTS down_folders");
$db->exeSqls($sql_clear);

$cmd = $db->GetConUtf8();
for( $i = 0 , $l = count($files);$i<$l;++$i)
{
	$f = "$sqlDir\\$files[$i]";
	if(is_file($f))
	{
		$sql = file_get_contents($f);
		$cmd->exec($sql);
		echo "执行 $f 成功<br/>";
	}	
}

//下载表
$files = scandir($downDir);
for( $i = 0 , $l = count($files);$i<$l;++$i)
{
	$f = "$downDir\\$files[$i]";
	if(is_file($f))
	{
		$sql = file_get_contents($f);
		$cmd->exec($sql);
		echo "执行 $f 成功<br/>";
	}
}
?>
<p>数据库初始化完毕！</p>