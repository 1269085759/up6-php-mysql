-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 05 月 26 日 07:54
-- 服务器版本: 5.0.45
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `httpuploader6`
--

-- --------------------------------------------------------

--
-- 表的结构 `down_files`
--

DROP TABLE IF EXISTS `down_files`;
/*--drop table down_files*/
CREATE TABLE down_files
(
 f_id      		int(11) NOT NULL auto_increment    
,f_uid        	int(11) 	DEFAULT '0' 
,f_mac        	varchar(50) DEFAULT  '' 
,f_nameLoc		varchar(255)DEFAULT ''
,f_pathLoc      varchar(255)DEFAULT '' 	
,f_fileUrl      varchar(255)DEFAULT '' 	
,f_perLoc    	varchar(6) 	DEFAULT '0' 
,f_lenLoc    	bigint(19) 	DEFAULT '0' 
,f_lenSvr		bigint(19) DEFAULT '0'
,f_sizeSvr      varchar(10) DEFAULT '0' 
,f_complete		tinyint(1)	DEFAULT '0'	
,f_pidRoot		int(11) 	DEFAULT '0'	
,f_fdTask		tinyint(1) 	DEFAULT '0'	
,PRIMARY KEY  (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- 导出表中的数据 `down_files`
--

