-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 05 月 30 日 06:51
-- 服务器版本: 5.7.9
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: 'httpuploader6'
--

-- --------------------------------------------------------

--
-- 表的结构 'up6_files'
--

CREATE TABLE IF NOT EXISTS up6_files (
  f_id int(11) NOT NULL AUTO_INCREMENT,
  f_pid int(11) DEFAULT '0',
  f_pidRoot int(11) DEFAULT '0',
  f_fdTask tinyint(1) DEFAULT '0',
  f_fdID int(11) DEFAULT '0',
  f_fdChild tinyint(1) DEFAULT '0',
  f_uid int(11) DEFAULT '0',
  f_nameLoc varchar(255) DEFAULT '',
  f_nameSvr varchar(255) DEFAULT '',
  f_pathLoc varchar(255) DEFAULT '',
  f_pathSvr varchar(255) DEFAULT '',
  f_pathRel varchar(255) DEFAULT '',
  f_md5 varchar(40) DEFAULT '',
  f_lenLoc bigint(19) DEFAULT '0',
  f_sizeLoc varchar(10) DEFAULT '0',
  f_pos bigint(19) DEFAULT '0',
  f_lenSvr bigint(19) DEFAULT '0',
  f_perSvr varchar(7) DEFAULT '0%',
  f_complete tinyint(1) DEFAULT '0',
  f_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  f_deleted tinyint(1) DEFAULT '0',
  PRIMARY KEY (f_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 'up6_folders'
--

CREATE TABLE IF NOT EXISTS up6_folders (
  fd_id int(11) NOT NULL AUTO_INCREMENT,
  fd_name varchar(50) DEFAULT '',
  fd_pid int(11) DEFAULT '0',
  fd_uid int(11) DEFAULT '0',
  fd_length bigint(19) DEFAULT '0',
  fd_size varchar(50) DEFAULT '0',
  fd_pathLoc varchar(255) DEFAULT '',
  fd_pathSvr varchar(255) DEFAULT '',
  fd_folders int(11) DEFAULT '0',
  fd_files int(11) DEFAULT '0',
  fd_filesComplete int(11) DEFAULT '0',
  fd_complete tinyint(1) DEFAULT '0',
  fd_delete tinyint(1) DEFAULT '0',
  fd_json varchar(20000) DEFAULT '',
  timeUpload timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fd_pidRoot int(11) DEFAULT '0',
  fd_pathRel varchar(255) DEFAULT '',
  PRIMARY KEY (fd_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Procedures
--
DELIMITER $$
--
CREATE DEFINER=root@localhost PROCEDURE fd_files_add_batch(
 in fCount int	
,in fdCount int)
begin
	declare ids_f text default '0';
	declare ids_fd text default '0';
	declare i int;
	set i = 0;
	
	
	while(i<fdCount) do	
		insert into up6_folders(fd_pid) values(0);	
		set ids_fd = concat( ids_fd,",",last_insert_id() );
		set i = i + 1;
	end while;
	set ids_fd = substring(ids_fd,3);
	
	
	set i = 0;
	while(i<fCount) do	
		insert into up6_files(f_pid) values(0);	
		set ids_f = concat( ids_f,",",last_insert_id() );
		set i = i + 1;
	end while;	
	set ids_f = substring(ids_f,3);
	
	select ids_f,ids_fd;
end$$

CREATE DEFINER=root@localhost PROCEDURE fd_process(in uidSvr int,in fd_idSvr int,in fd_lenSvr bigint(19),in perSvr varchar(6))
update up6_files set f_lenSvr=fd_lenSvr ,f_perSvr=perSvr  where f_uid=uidSvr and f_id=fd_idSvr$$

CREATE DEFINER=root@localhost PROCEDURE fd_update(		
 in _name			varchar(50)
,in _pid			int
,in _uid			int
,in _length			bigint
,in _size			varchar(50)
,in _pathLoc		varchar(255)
,in _pathSvr		varchar(255)
,in _folders		int
,in _files			int
,in _filesComplete	int
,in _complete		tinyint
,in _delete			tinyint
,in _pidRoot		int
,in _pathRel		varchar(255)
,in _id				int
)
begin
	update up6_folders set
	 fd_name			= _name
	,fd_pid				= _pid
	,fd_uid				= _uid
	,fd_length			= _length
	,fd_size			= _size
	,fd_pathLoc			= _pathLoc
	,fd_pathSvr			= _pathSvr
	,fd_folders			= _folders
	,fd_files			= _files
	,fd_filesComplete	= _filesComplete
	,fd_complete		= _complete
	,fd_delete			= _delete
	,fd_pidRoot			= _pidRoot
	,fd_pathRel			= _pathRel
	where 
	fd_id = _id;		
end$$

CREATE DEFINER=root@localhost PROCEDURE f_exist(
in _md5 varchar(40)
)
select 
	 f_id
	,f_uid
	,f_nameLoc
	,f_nameSvr
	,f_pathLoc
	,f_pathSvr
	,f_pathRel
	,f_lenLoc
	,f_sizeLoc
	,f_pos
	,f_lenSvr
	,f_perSvr
	,f_complete
	,f_time
	,f_deleted
	 from up6_files
	 where f_md5 = _md5
	 order by f_lenSvr desc limit 1$$

CREATE DEFINER=root@localhost PROCEDURE f_exist_batch(
	in _md5s varchar(1000)
)
select 
	 f_id
	,f_uid
	,f_nameLoc
	,f_nameSvr
	,f_pathLoc
	,f_pathSvr
	,f_pathRel
	,f_lenLoc
	,f_sizeLoc
	,f_pos
	,f_lenSvr
	,f_perSvr
	,f_complete
	,f_time
	,f_deleted
,f_md5
	 from up6_files
	 where find_in_set (f_md5,_md5s )$$

CREATE DEFINER=root@localhost PROCEDURE f_process(in posSvr bigint(19),in lenSvr bigint(19),in perSvr varchar(6),in uidSvr int,in fidSvr int,in complete tinyint)
update up6_files set f_pos=posSvr,f_lenSvr=lenSvr,f_perSvr=perSvr,f_complete=complete where f_uid=uidSvr and f_id=fidSvr$$

CREATE DEFINER=root@localhost PROCEDURE f_update(		
 in _pid		int
,in _pidRoot	int
,in _fdTask 	tinyint
,in _fdID		int
,in _fdChild 	tinyint
,in _uid		int
,in _nameLoc	varchar(255)
,in _nameSvr	varchar(255)
,in _pathLoc	varchar(255)
,in _pathSvr	varchar(255)
,in _md5		varchar(40)
,in _lenLoc		bigint
,in _lenSvr		bigint
,in _perSvr		varchar(7)
,in _sizeLoc	varchar(10)
,in _complete	tinyint
,in _id			int
)
begin
	update up6_files set
	 f_pid		=_pid
	,f_pidRoot	= _pidRoot
	,f_fdTask 	= _fdTask
	,f_fdID		= _fdID
	,f_fdChild	= _fdChild
	,f_uid		= _uid
	,f_nameLoc	= _nameLoc
	,f_nameSvr	= _nameSvr
	,f_pathLoc	= _pathLoc
	,f_pathSvr	= _pathSvr
	,f_md5		= _md5
	,f_lenLoc	= _lenLoc
	,f_lenSvr	= _lenSvr
	,f_perSvr	= _perSvr
	,f_sizeLoc	= _sizeLoc
	,f_complete	= _complete
	where f_id = _id;		
end$$

--
DELIMITER ;
--
