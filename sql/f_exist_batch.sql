--批量查询文件
--DROP PROCEDURE `f_exist_batch`
CREATE DEFINER=`root`@`localhost` PROCEDURE `f_exist_batch`(
	in _md5s varchar(65532)
)
select 
	 f_id
	,f_uid
	,f_nameLoc
	,f_nameSvr
	,f_pathLoc
	,f_pathSvr
	,f_pathRel
	,f_md5
	,f_lenLoc
	,f_sizeLoc
	,f_pos
	,f_lenSvr
	,f_perSvr
	,f_complete
	,f_time
	,f_deleted
	 from up6_files
	 where find_in_set (f_md5 ,_md5s)
	 group by f_md5
