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
-- 表的结构 `down_folders`
--

DROP TABLE IF EXISTS `down_folders`;
CREATE TABLE IF NOT EXISTS `down_folders` (
  `fd_id` 		int(11) NOT NULL auto_increment,
  `fd_name` 	varchar(50) default '',
  `fd_uid` 		int(11) default '0',
  `fd_mac` 		varchar(50) default '',
  `fd_pathLoc` 	varchar(255) default '',
  `fd_complete` tinyint(1) default '0',
  `fd_id_old` 	varchar(512) default '',
  `fd_percent` 	varchar(7) default '',
  PRIMARY KEY  (`fd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `down_folders`
--

