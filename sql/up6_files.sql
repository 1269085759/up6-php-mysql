-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 05 月 26 日 07:53
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `httpuploader6`
--

-- --------------------------------------------------------

--
-- 表的结构 `up6_files`
--

DROP TABLE IF EXISTS `up6_files`;
CREATE TABLE IF NOT EXISTS `up6_files` (
  `f_id` 				int(11) NOT NULL auto_increment,
  `f_pid` 				int(11) default '0',		/*父级文件夹ID*/
  `f_pidRoot` 			int(11) default '0',		/*根级文件夹ID*/
  `f_fdTask` 			tinyint(1) default '0',		/*是否是一条文件夹信息*/
  `f_fdID` 				int(11) default '0',		/*与文件夹表(up6_folders.fd_id)对应*/
  `f_fdChild` 			tinyint(1) default '0',		/*是否是文件夹中的文件*/
  `f_uid` 				int(11) default '0',
  `f_nameLoc` 			varchar(255) default '',	/*文件在本地的名称（原始文件名称）*/
  `f_nameSvr` 			varchar(255) default '',	/*文件在服务器的名称*/
  `f_pathLoc` 			varchar(255) default '',	/*文件在本地的路径*/
  `f_pathSvr` 			varchar(255) default '',	/*文件在远程服务器中的位置*/
  `f_pathRel` 			varchar(255) default '',
  `f_md5` 				varchar(40) default '',	/*文件MD5*/
  `f_lenLoc` 			bigint(19) default '0',		/*文件大小*/
  `f_sizeLoc` 			varchar(10) default '0',	/*文件大小（格式化的）*/
  `f_pos` 				bigint(19) default '0',		/*续传位置*/
  `f_lenSvr` 			bigint(19) default '0',		/*已上传大小*/
  `f_perSvr` 			varchar(7) default '0%',	/*已上传百分比*/
  `f_complete` 			tinyint(1) default '0',		/*是否已上传完毕*/
  `f_time` 				timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `f_deleted` 			tinyint(1) default '0',
  PRIMARY KEY  (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `up6_files`
--


--
-- 存储过程
--

--更新文件进度
CREATE DEFINER=`root`@`localhost` PROCEDURE `f_process`(in posSvr bigint(19),in lenSvr bigint(19),in perSvr varchar(6),in uidSvr int,in fidSvr int,in complete tinyint)
update up6_files set f_pos=posSvr,f_lenSvr=lenSvr,f_perSvr=perSvr,f_complete=complete where f_uid=uidSvr and f_id=fidSvr

--更新文件夹进度
DROP PROCEDURE `fd_process`//
CREATE DEFINER=`root`@`localhost` PROCEDURE `fd_process`(in uidSvr int,in fd_idSvr int,in fd_lenSvr bigint(19),in perSvr varchar(6))
update up6_files set f_lenSvr=fd_lenSvr ,f_perSvr=perSvr  where f_uid=uidSvr and f_id=fd_idSvr

--查看所有存储过程
--show procedure status;
--查看存储过程的创建代码
--show create procedure f_process;




