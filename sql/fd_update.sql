--更新文件夹
DELIMITER $$
CREATE PROCEDURE fd_update(		
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
DELIMITER;/*--5.7.9版本MySQL必须加这一句，否则包含多条SQL语句的存储过程无法创建成功*/