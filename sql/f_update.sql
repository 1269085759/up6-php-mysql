
--更新文件
DELIMITER $$
CREATE PROCEDURE f_update(		
 in _pid		int
,in _pidRoot	int
,in _fdTask		tinyint
,in _fdID		int
,in _fdChild	tinyint
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
	 f_pid		= _pid
	,f_pidRoot	= _pidRoot
	,f_fdTask	= _fdTask
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
DELIMITER;/*--5.7.9版本MySQL必须加这一句，否则包含多条SQL语句的存储过程无法创建成功*/