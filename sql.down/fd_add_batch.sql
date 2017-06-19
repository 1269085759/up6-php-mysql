/*批量添加文件和文件夹*/
CREATE PROCEDURE fd_add_batch(
 in fCount int	/*文件总数，由外部+1，表示单独增加一个文件夹*/
,in uid int		/*用户ID*/
)
begin
	declare f_ids text default '0';/*文件ID列表*/
	declare i int;
	set i = 0;
	
	/*批量添加文件*/
	while(i<fCount) do	
		insert into down_files(f_uid) values(uid);	
		set f_ids = concat( f_ids,",",last_insert_id() );
		set i = i + 1;
	end while;
	set f_ids = substring(f_ids,3);/*删除0,*/
	
	select f_ids;
end