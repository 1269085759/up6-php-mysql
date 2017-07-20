<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2012-04-03 创建
		2014-08-11 更新数据库操作代码。 
*/
class DBFile
{
	var $db;//全局数据库连接,共用数据库连接
		
	function __construct() 
	{
		$this->db = new DbHelper();
	}
	
	/// <summary>
	/// 根据UID获取文件列表，只列出文件，不列出文件夹，文件夹中的文件
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="tb"></param>
	function GetFilesByUid($f_uid,&$tb)
	{
		$sql = "select * from up6_files where f_uid=:f_uid and f_deleted=0 and f_fdChild=0;";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":f_uid",$f_uid);

		$tb = $db->ExecuteDataTable($cmd);
	}
	
	/**
	 * @param in  $uid
	 * @param out $files xdb_files array
	 * @return string
	 */
	static function GetAllCompleteFiles($f_uid,&$files)
	{
		$sql = "select ";
		$sql = $sql . "f_id";
		$sql = $sql . ",f_fdTask";
		$sql = $sql . ",f_fdID";
		$sql = $sql . ",f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_pathSvr";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_pos";
		$sql = $sql . ",f_lenSvr";
		$sql = $sql . ",f_perSvr";
		$sql = $sql . ",f_complete";
		$sql = $sql . " from up6_files";//联合查询文件夹数据
		$sql = $sql . " where f_uid=:f_uid and f_deleted=0 and f_fdChild=0 and f_complete=1;";//只加载未完成列表

		//取未完成的文件列表
		//$files = array();
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":f_uid",$f_uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{			
			$f = new xdb_files();
			$f->idSvr 			= $row["f_id"];
			$f->f_fdTask 		= $row["f_fdTask"];
			$f->f_fdID 			= $row["f_fdID"];
			$f->nameLoc 		= $row["f_nameLoc"];
			$f->pathLoc 		= $row["f_pathLoc"];
			$f->pathSvr			= $row["f_pathSvr"];
			$f->md5 			= $row["f_md5"];
			$f->lenLoc 			= $row["f_lenLoc"];
			$f->sizeLoc 		= $row["f_sizeLoc"];
			$f->FilePos 		= $row["f_pos"];
			$f->lenSvr 			= $row["f_lenSvr"];
			$f->perSvr 			= $row["f_perSvr"];
			$f->complete 		= $row["f_complete"];
			$f->IsDeleted		= $row["f_deleted"];
			$f->PostedTime		= $row["f_time"];

			array_push($files,$f);
		}
	}

	/// <summary>
	/// 获取所有文件和文件夹列表，不包含子文件夹，包含已上传完的和未上传完的
	/// </summary>
	/// <param name="f_uid"></param>
	/// <returns></returns>
	static function GetAllUnComplete($f_uid)
	{
		$sql = "select 
				 f_id
				,f_fdTask
				,f_nameLoc
				,f_pathLoc
				,f_pathSvr
				,f_md5
				,f_lenLoc
				,f_sizeLoc
				,f_pos
				,f_lenSvr
				,f_perSvr
				,f_complete
				 from up6_files
				 where f_uid=:f_uid and f_deleted=0 and f_fdChild=0 and f_complete=0;";//只加载未完成列表

		//取未完成的文件列表
		$files = array();
		$db = new DbHelper();
		$cmd = $db->GetCommand($sql);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":f_uid",$f_uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{			
			$f = new FileInf();
			$f->uid			= $f_uid;
			$f->id 			= $row["f_id"];
			$f->fdTask 		= (bool)($row["f_fdTask"]);
			$f->nameLoc 	= PathTool::urlencode_safe( $row["f_nameLoc"] );
			$f->pathLoc 	= PathTool::urlencode_safe( $row["f_pathLoc"] );
			$f->pathSvr		= PathTool::urlencode_safe( $row["f_pathSvr"] );
			$f->md5 		= $row["f_md5"];
			$f->lenLoc 		= $row["f_lenLoc"];
			$f->sizeLoc 	= $row["f_sizeLoc"];
			$f->offset 		= $row["f_pos"];
			$f->lenSvr 		= $row["f_lenSvr"];
			$f->perSvr 		= $row["f_perSvr"];
			$f->complete 	= false;
			$f->deleted		= false;
			$f->nameLoc 	= PathTool::urlencode_safe($f->nameLoc);//防止汉字被json_encode转换为unicode
			$f->pathLoc 	= PathTool::urlencode_safe($f->pathLoc);//防止汉字被json_encode转换为unicode

			$files[] = $f;
		}
		return json_encode($files);
	}

	/// <summary>
	/// 获取所有文件和文件夹列表，不包含子文件夹，包含已上传完的和未上传完的
	/// </summary>
	/// <param name="f_uid"></param>
	/// <returns></returns>
	static function GetAll($f_uid)
	{
		$sql = "select ";
		$sql = $sql . "f_id";
		$sql = $sql . ",f_fdTask";
		$sql = $sql . ",f_fdID";
		$sql = $sql . ",f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_pathSvr";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_pos";
		$sql = $sql . ",f_lenSvr";
		$sql = $sql . ",f_perSvr";
		$sql = $sql . ",f_complete";
		$sql = $sql . " from up6_files where f_uid=:f_uid and f_deleted=0 and f_fdChild=0;";

		//取未完成的文件列表
		$files = array();
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":f_uid",$f_uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{			
			$f = new xdb_files();
			$f->uid				= $f_uid;
			$f->idSvr 			= $row["f_id"];
			$f->f_fdTask 		= $row["f_fdTask"];
			$f->f_fdID 			= $row["f_fdID"];
			$f->nameLoc 		= $row["f_nameLoc"];
			$f->pathLoc 		= $row["f_pathLoc"];
			$f->pathSvr			= $row["f_pathSvr"];
			$f->md5 			= $row["f_md5"];
			$f->lenLoc 			= $row["f_lenLoc"];
			$f->sizeLoc 		= $row["f_sizeLoc"];
			$f->FilePos 		= $row["f_pos"];
			$f->lenSvr 			= $row["f_lenSvr"];
			$f->perSvr 			= $row["f_perSvr"];
			$f->complete 		= $row["f_complete"];
			$f->IsDeleted		= $row["f_deleted"];
			$f->PostedTime		= $row["f_time"];

			array_push($files,$f);
		}
		
		//填充文件夹信息
		$arrFiles = array();
		foreach ($files as $file)
		{			
			//是文件夹任务=>取文件夹JSON
			if ($file->f_fdTask)
			{
				$fd = new FolderInf();				
				$file->fd_json = DBFolder::GetFilesUnCompleteJson($file->f_fdID,$fd);
				$file->nameLoc = $fd->name;								
				$pdPer = 0;
				$lenPosted = DBFolder::GetLenPosted($file->f_fdID);
				$fd->lenSvr = $lenPosted;
				$file->lenSvr = $lenPosted;//给客户端使用。
				$len = $fd->lenLoc;
				if ($lenPosted > 0 && $len > 0)
				{
					$pdPer = round(($lenPosted / $len) * 100,2);
				}
				$file->idSvr = $file->f_fdID;//将文件ID改为文件夹的ID，客户端续传文件夹时将会使用这个ID。
				$file->perSvr = $pdPer . "%";
				$file->sizeLoc = $fd->size;
			}
			array_push($arrFiles,$file);
		}
		return json_encode($arrFiles);
	}

	/// <summary>
	/// 根据文件ID获取文件信息
	/// </summary>
	/// <param name="f_id"></param>
	/// <returns></returns>
	function GetFileInfByFid($f_id,&$inf/*xdb_files*/)
	{
		$ret = false;		
		$sb = "select ";
		$sb = $sb . "f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_pathRel";
		$sb = $sb . ",f_md5";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_pos";
		$sb = $sb . ",f_lenSvr";
		$sb = $sb . ",f_perSvr";
		$sb = $sb . ",f_complete";
		$sb = $sb . ",f_time";
		$sb = $sb . ",f_deleted";
		$sb = $sb . " from up6_files where f_id=:f_id limit 0,1";
		
		$db = new DbHelper();
		$cmd = $db->prepare_utf8($sb);
		$cmd->bindParam(":f_id",$f_id);
		$row = $db->ExecuteRow($cmd);

		if ( !empty($row) )
		{
			$inf->idSvr 		= $f_id;
			$inf->uid 			= intval($row["f_uid"]);
			$inf->nameLoc 		= $row["f_nameLoc"];
			$inf->nameSvr 		= $row["f_nameSvr"];
			$inf->pathLoc 		= $row["f_pathLoc"];
			$inf->pathSvr 		= $row["f_pathSvr"];
			$inf->pathRel 		= $row["f_pathRel"];
			$inf->md5 			= $row["f_md5"];
			$inf->lenLoc 		= $row["f_lenLoc"];
			$inf->sizeLoc 		= $row["f_sizeLoc"];
			$inf->FilePos 		= $row["f_pos"];
			$inf->lenSvr 		= $row["f_lenSvr"];
			$inf->perSvr 		= $row["f_perSvr"];
			$inf->complete 		= $row["f_complete"];
			$inf->PostedTime 	= $row["f_time"];
			$inf->IsDeleted 	= $row["f_deleted"];
			$ret = true;
		}
		return $ret;
	}

	/// <summary>
	/// 根据文件MD5获取文件信息
	/// </summary>
	/// <param name="md5"></param>
	/// <param name="inf"></param>
	/// <returns></returns>
	function exist_file($md5, &$inf/*xdb_files*/)
	{
		$ret = false;
		$sb = "select * from (select ";
		$sb = $sb . "f_id";
		$sb = $sb . ",f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_pathRel";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_pos";
		$sb = $sb . ",f_lenSvr";
		$sb = $sb . ",f_perSvr";
		$sb = $sb . ",f_complete";
		$sb = $sb . ",f_time";
		$sb = $sb . ",f_deleted";
		$sb = $sb . " from up6_files";
		$sb = $sb . " where f_md5=:f_md5";
		$sb = $sb . " order by f_lenSvr desc";
		$sb = $sb . " ) tmp limit 1";

		$db = &$this->db;
		
		$cmd = $db->prepare_utf8($sb);
		
		$cmd->bindParam(":f_md5", $md5);
		$row = $db->ExecuteRow($cmd);		
		
		if (!empty($row))
		{
			$inf->idSvr 		= intval($row["f_id"]);
			$inf->uid 			= intval($row["f_uid"]);
			$inf->nameLoc 		= $row["f_nameLoc"];
			$inf->nameSvr 		= $row["f_nameSvr"];
			$inf->pathLoc 		= $row["f_pathLoc"];
			$inf->pathSvr 		= $row["f_pathSvr"];
			$inf->pathRel 		= $row["f_pathRel"];
			$inf->md5 			= $md5;
			$inf->lenLoc 		= intval($row["f_lenLoc"]);
			$inf->sizeLoc 		= $row["f_sizeLoc"];
			$inf->FilePos 		= intval($row["f_pos"]);
			$inf->lenSvr 		= intval($row["f_lenSvr"]);
			$inf->perSvr 		= $row["f_perSvr"];
			$inf->complete 		= (bool)$row["f_complete"];
			$inf->PostedTime 	= $row["f_time"];
			$inf->IsDeleted 	= (bool)$row["f_deleted"];
			$ret = true;
		}
		return $ret;
	}

	/// <summary>
	/// 增加一条数据，并返回新增数据的ID
	/// 在f_create.php中调用
	/// 文件名称，本地路径，远程路径，相对路径都使用原始字符串。
	/// d:\soft\QQ2012.exe
	/// </summary>
	function Add(&$model/*FileInf*/)
	{
		$sb = "insert into up6_files(";
		$sb = $sb . " f_id";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_pos";
		$sb = $sb . ",f_lenSvr";
		$sb = $sb . ",f_perSvr";
		$sb = $sb . ",f_complete";
		$sb = $sb . ",f_time";
		$sb = $sb . ",f_deleted";
		$sb = $sb . ",f_fdChild";
		$sb = $sb . ",f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_pathRel";
		$sb = $sb . ",f_md5";
		$sb = $sb . ",f_lenLoc";
		
		$sb = $sb . ") values (";
		
		$sb = $sb . " :f_id";//"@f_id";
		$sb = $sb . ",:f_sizeLoc";//"@f_sizeLoc";
		$sb = $sb . ",:f_pos";//",@f_pos";
		$sb = $sb . ",:f_lenSvr";//",@f_lenSvr";
		$sb = $sb . ",:f_perSvr";//",@f_perSvr";
		$sb = $sb . ",:f_complete";//",@f_complete";
		$sb = $sb . ",now()";//",@f_time";
		$sb = $sb . ",0";//",@f_deleted";
		$sb = $sb . ",0";//",@f_fdChild";
		$sb = $sb . ",:f_uid";//",@f_uid";
		$sb = $sb . ",:f_nameLoc";//",@f_nameLoc";
		$sb = $sb . ",:f_nameSvr";//",@f_nameSvr";
		$sb = $sb . ",:f_pathLoc";//",@f_pathLoc";
		$sb = $sb . ",:f_pathSvr";//",@f_pathSvr";
		$sb = $sb . ",:f_pathRel";//",@f_pathRel";
		$sb = $sb . ",:f_md5";//",@f_md5";
		$sb = $sb . ",:f_lenLoc";//",@f_lenLoc";
		$sb = $sb . ") ";

		$db = &$this->db;
		$cmd = $db->prepare_utf8( $sb );		
		
		$cmd->bindParam(":f_id",$model->id);
		$cmd->bindParam(":f_sizeLoc",$model->sizeLoc);
		$cmd->bindValue(":f_pos",0);
		$cmd->bindValue(":f_lenSvr",$model->lenSvr);
		$cmd->bindParam(":f_perSvr",$model->perSvr);
		$cmd->bindValue(":f_complete",$model->complete,PDO::PARAM_BOOL);//fix(2016-05-24):必须指名类型，否则无法插入数据
		$cmd->bindValue(":f_uid",$model->uid,PDO::PARAM_INT);
		$cmd->bindParam(":f_nameLoc",$model->nameLoc);
		$cmd->bindParam(":f_nameSvr",$model->nameSvr);
		$cmd->bindParam(":f_pathLoc",$model->pathLoc);
		$cmd->bindParam(":f_pathSvr",$model->pathSvr);
		$cmd->bindParam(":f_pathRel",$model->pathRel);
		$cmd->bindParam(":f_md5",$model->md5);
		$cmd->bindValue(":f_lenLoc",$model->lenLoc);

		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// (已弃用)添加一个文件夹上传任务
	/// </summary>
	/// <param name="inf"></param>
	/// <returns></returns>
	static function AddFD(&$folder/*FolderInf*/)
	{
		$sb = "insert into up6_files(";
		$sb = $sb . " f_nameLoc";
		$sb = $sb . ",f_fdTask";
		$sb = $sb . ",f_fdID";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ") values(";
		$sb = $sb . ":f_nameLoc";
		$sb = $sb . ",1";
		$sb = $sb . ",:f_fdID";
		$sb = $sb . ",:f_lenLoc";
		$sb = $sb . ",:f_sizeLoc";
		$sb = $sb . ",:f_pathLoc";
		$sb = $sb . ");";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":f_nameLoc",$folder->nameLoc);
		$cmd->bindValue(":f_fdID",$folder->idSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_lenLoc",$folder->lenLoc);//
		$cmd->bindParam(":f_sizeLoc",$folder->size);
		$cmd->bindParam(":f_pathLoc",$folder->pathLoc);
		$db->ExecuteNonQuery($cmd);
		
		$id = $db->m_conCur->lastInsertId("f_id");		
		return $id;
	}

	/// <summary>
	/// (已弃用)添加一条文件信息，一船提供给ajax_fd_create.aspx使用。
	/// </summary>
	/// <param name="inf"></param>
	/// <returns></returns>
	function AddXDB(&$inf)
	{
		$sb = "insert into up6_files(";
		$sb = $sb . " f_pid";
		$sb = $sb . ",f_pidRoot";
		$sb = $sb . ",f_fdChild";
		$sb = $sb . ",f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_pathRel";//fix(2015-10-13):
		$sb = $sb . ",f_md5";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ") values(";
		$sb = $sb . " :f_pid";//f_pid
		$sb = $sb . ",:f_pidRoot";//f_pidRoot
		$sb = $sb . ",0";//f_fdChild
		$sb = $sb . ",:f_uid";//f_uid
		$sb = $sb . ",:f_nameLoc";//f_nameLoc
		$sb = $sb . ",:f_nameSvr";//f_nameSvr
		$sb = $sb . ",:f_pathLoc";//f_pathLoc
		$sb = $sb . ",:f_pathSvr";//f_pathSvr
		$sb = $sb . ",:f_pathRel";//f_pathRel
		$sb = $sb . ",:f_md5";//f_md5
		$sb = $sb . ",:f_lenLoc";//f_lenLoc
		$sb = $sb . ",:f_sizeLoc";//f_sizeLoc
		$sb = $sb . ");";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindParam(":f_pid", $inf->pidSvr);
		$cmd->bindParam(":f_pidRoot", $inf->pidRoot);
		$cmd->bindParam(":f_uid", $inf->uid);
		$cmd->bindParam(":f_nameLoc", $inf->nameLoc);
		$cmd->bindParam(":f_nameSvr", $inf->nameSvr);
		$cmd->bindParam(":f_pathLoc", $inf->pathLoc);
		$cmd->bindParam(":f_pathSvr", $inf->pathSvr);
		$cmd->bindParam(":f_pathRel", $inf->pathRel);
		$cmd->bindParam(":f_md5", $inf->md5);
		$cmd->bindParam(":f_lenLoc", $inf->lenLoc);
		$cmd->bindParam(":f_sizeLoc", $inf->sizeLoc);
		$db->ExecuteNonQuery($cmd);

		$f_id = $db->m_conCur->lastInsertId("f_id");//$db->ExecuteScalar("select top 1 f_id from up6_files order by f_id desc");
		return $f_id;
	}

	/// <summary>
	/// (已弃用)添加一条文件信息，一船提供给fd_create.php使用。
	//编码参考：http://my.oschina.net/cart/blog/326787
	/// </summary>
	/// <param name="inf"></param>
	/// <returns></returns>
	static function AddFileInf(&$inf/*FileInf*/)
	{
		$sb = "insert into up6_files(";
		$sb = $sb . " f_pid";
		$sb = $sb . ",f_pidRoot";
		$sb = $sb . ",f_fdChild";
		$sb = $sb . ",f_uid";
		$sb = $sb . ",f_nameLoc";
		$sb = $sb . ",f_nameSvr";
		$sb = $sb . ",f_pathLoc";
		$sb = $sb . ",f_pathSvr";
		$sb = $sb . ",f_md5";
		$sb = $sb . ",f_lenLoc";
		$sb = $sb . ",f_lenSvr";
		$sb = $sb . ",f_perSvr";
		$sb = $sb . ",f_sizeLoc";
		$sb = $sb . ",f_complete";
		
		$sb = $sb . ") values(";
		
		$sb = $sb . " :f_pid";//f_pid
		$sb = $sb . ",:f_pidRoot";//f_pidRoot
		$sb = $sb . ",1";//f_fdChild
		$sb = $sb . ",:f_uid";//f_uid
		$sb = $sb . ",:f_nameLoc";//f_nameLoc
		$sb = $sb . ",:f_nameSvr";//f_nameSvr
		$sb = $sb . ",:f_pathLoc";//f_pathLoc
		$sb = $sb . ",:f_pathSvr";//f_pathSvr
		$sb = $sb . ",:f_md5";//f_md5
		$sb = $sb . ",:f_lenLoc";//f_lenLoc
		$sb = $sb . ",:f_lenSvr";//f_lenSvr
		$sb = $sb . ",:f_perSvr";//f_sizeLoc
		$sb = $sb . ",:f_sizeLoc";//f_sizeLoc
		$sb = $sb . ",:f_complete";//f_sizeLoc
		$sb = $sb . ");";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		
		//设置字符集，防止中文在数据库中出现乱码
		$db->ExecuteNonQueryConTxt("set names utf8");
		
		$cmd->bindValue(":f_pid", $inf->pidSvr,PDO::PARAM_INT);
		$cmd->bindValue(":f_pidRoot", $inf->pidRoot,PDO::PARAM_INT);
		$cmd->bindValue(":f_uid", $inf->uid,PDO::PARAM_INT);		
		$cmd->bindValue(":f_nameLoc", $inf->nameLoc,PDO::PARAM_STR);		
		$cmd->bindValue(":f_nameSvr", $inf->nameSvr,PDO::PARAM_STR);
		$cmd->bindValue(":f_pathLoc", $inf->pathLoc,PDO::PARAM_STR);
		$cmd->bindValue(":f_pathSvr", $inf->pathSvr,PDO::PARAM_STR);
		$cmd->bindParam(":f_md5", $inf->md5);
		$cmd->bindValue(":f_lenLoc", $inf->lenLoc,PDO::PARAM_INT);
		$cmd->bindValue(":f_lenSvr", $inf->lenSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_perSvr", $inf->perSvr);
		$cmd->bindParam(":f_sizeLoc", $inf->sizeLoc);
		$cmd->bindValue(":f_complete", $inf->complete,PDO::PARAM_BOOL);
		$db->ExecuteNonQuery($cmd);

		$f_id = $db->m_conCur->lastInsertId("f_id");//$db->ExecuteScalar("select top 1 f_id from up6_files order by f_id desc");
		return $f_id;
	}

	/// <summary>
	/// 更新文件夹中子文件信息，
	/// f_pathSvr
	/// md5
	/// f_id
	/// </summary>
	/// <param name="inf"></param>
	function UpdateChild(&$inf)
	{		
		$sb = "update up6_files set ";
		$sb = $sb . " f_pathSvr = :f_pathSvr , ";
		$sb = $sb . " f_md5 = :f_md5 ";
		$sb = $sb . " where f_id=:f_id ";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		$cmd->bindParam(":f_pathSvr", $inf->pathSvr);
		$cmd->bindParam(":f_md5", $inf->md5);
		$cmd->bindParam(":f_id", $inf->idSvr);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 根据文件idSvr信息，更新文件数据表中对应项的MD5。
	/// </summary>
	/// <param name="inf"></param>
	function UpdateMD5(&$inf)
	{
		$sb = "update up6_files set ";
		$sb = $sb . " f_md5 = :f_md5 ";
		$sb = $sb . " where f_id=:f_id ";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sb);
		$cmd->bindParam(":f_md5", $inf->md5);
		$cmd->bindParam(":f_id", $inf->idSvr);
		$db->ExecuteNonQuery($cmd);
	}

	//更新文件md5,pathSvr信息
	function update_md5_pathSvr(&$fileSvr/*xdb_files*/)
	{
		$sql = "update up6_files set f_md5=:f_md5,f_pathSvr=:f_pathSvr where f_id=:f_id";

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_md5", $fileSvr->md5);
		$cmd->bindParam(":f_pathSvr", $fileSvr->pathSvr);
		$cmd->bindParam(":f_id", $fileSvr->idSvr);
		$db->ExecuteNonQuery($cmd);
	}

	static function Clear()
	{
		$db = new DbHelper();
		$db->ExecuteNonQueryTxt("TRUNCATE TABLE up6_files;");
		$db->ExecuteNonQueryTxt("TRUNCATE TABLE up6_folders;");
	}

	/// <summary>
	/// 
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id">文件ID</param>
	function Complete($md5)
	{
		$db = new DbHelper();
		$cmd =& $db->GetCommand("update up6_files set f_lenSvr=f_lenLoc,f_perSvr='100%',f_complete=1 where f_md5=:f_md5;");
		$cmd->bindParam(":f_md5",$md5);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id">文件ID</param>
	function fd_complete($idSvr)
	{
		$db = new DbHelper();
		$cmd =& $db->GetCommand("update up6_files set f_perSvr='100%',f_complete=1 where f_id=:f_id;");
		$cmd->bindParam(":f_id",$idSvr);
		$db->ExecuteNonQuery($cmd);
	}
	
    function fd_fileProcess($uid, $f_id, $f_pos, $lenSvr, $perSvr, $fd_idSvr, $fd_lenSvr,$fd_perSvr,$complete)
    {
    	$this->f_process($uid, $f_id, $f_pos, $lenSvr, $perSvr,$complete);
    	$this->fd_process($uid, $fd_idSvr, $fd_lenSvr,$fd_perSvr);
    }
    
    function fd_process($uid,$fd_idSvr,$fd_lenSvr,$perSvr)
    {
        $sql = "call fd_process(:uid,:idSvr,:lenSvr,:per)";
        $db = $this->db;
        $cmd =& $db->GetCommand($sql);     

		$cmd->bindParam(":uid", $uid);
		$cmd->bindParam(":idSvr", $fd_idSvr);
		$cmd->bindParam(":lenSvr", $fd_lenSvr);
		$cmd->bindParam(":per", $perSvr);

		$db->ExecuteNonQuery($cmd);
		return true;
	}

	/// <summary>
	/// 更新上传进度
	/// </summary>
	///<param name="f_uid">用户ID</param>
	///<param name="f_id">文件ID</param>
	///<param name="f_pos">文件位置，大小可能超过2G，所以需要使用long保存</param>
	///<param name="f_lenSvr">已上传长度，文件大小可能超过2G，所以需要使用long保存</param>
	///<param name="f_perSvr">已上传百分比</param>
	function f_process($uid,$id,$offset,$lenSvr,$perSvr)
	{
		$sql = "update up6_files set f_pos=:pos,f_lenSvr=:len,f_perSvr=:per where f_uid=:uid and f_id=:id";		
		$db = &$this->db;
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":pos",$offset);
		$cmd->bindParam(":len",$lenSvr);
		$cmd->bindParam(":per",$perSvr);
		$cmd->bindParam(":uid",$uid);
		$cmd->bindParam(":id",$id);

		$db->ExecuteNonQuery($cmd);
		return true;
	}

	/// <summary>
	/// 上传完成。将所有相同MD5文件进度都设为100%
	/// </summary>
	function UploadComplete($md5)
	{
		$sql = "update up6_files set f_lenSvr=f_lenLoc,f_perSvr='100%',f_complete=1 where f_md5=:f_md5";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		
		$cmd->bindParam(":f_md5", $md5);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 检查相同MD5文件是否有已经上传完的文件
	/// </summary>
	/// <param name="md5"></param>
	function HasCompleteFile($md5)
	{
		//为空
		if (empty($md5)) return false;

		$sql = "select f_id from up6_files where f_complete=1 and f_md5=:f_md5";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);

		$cmd->bindParam(":f_md5", $md5);
		$ret = $db->ExecuteScalar($cmd);

		return empty($ret);
	}

	/// <summary>
	/// 删除一条数据，并不真正删除，只更新删除标识。
	/// </summary>
	/// <param name="f_uid"></param>
	/// <param name="f_id"></param>
	function Delete($f_uid,$f_id)
	{
		$sql = "update up6_files set f_deleted=1 where f_uid=:f_uid and f_id=:f_id";
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);

		$cmd->bindParam(":f_uid", $f_uid);
		$cmd->bindParam(":f_id", $f_id);
		$db->ExecuteNonQuery($cmd);
	}

	/// <summary>
	/// 根据根文件夹ID获取未上传完成的文件列表，并转换成JSON格式。
	/// 说明：
	///		1.此函数会自动对文件路径进行转码
	/// </summary>
	/// <param name="fidRoot"></param>
	/// <returns></returns>
	function GetUnCompletesJson($fidRoot)
	{
		$sql = "select ";
		$sql = $sql . "f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_pidRoot";
		$sql = $sql . ",f_pid";
		$sql = $sql . " from up6_files where f_pidRoot=:f_pidRoot;";		

		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_pidRoot", $fidRoot);
		$list = $db->ExecuteDataSet($cmd);
		
		$arrFiles = array();
		foreach($list as $row)
		{
			$fi = new FileInf();
			$fi->nameLoc = $row["f_nameLoc"];
			$fi->pathLoc = $row["f_pathLoc"];
			$fi->pathLoc = urlencode($fi->pathLoc);
			$fi->pathLoc = str_replace("+","%20",$fi->pathLoc);
			$fi->lenLoc = $row["f_lenLoc"];
			$fi->sizeLoc = $row["f_sizeLoc"];
			$fi->md5 = $row["f_md5"];
			$fi->pidRoot = intval($row["f_pidRoot"]);
			$fi->pidSvr = intval($row["f_pid"]);
			array_push($arrFiles,json_encode($fi));
		}
		return json_encode($arrFiles);
	}

	/// <summary>
	/// 获取未上传完的文件列表
	/// </summary>
	/// <param name="fidRoot"></param>
	/// <param name="files"></param>
	function GetUnCompletesArr($fidRoot,&$files)
	{
		$sql = "select ";
		$sql = $sql . "f_id";
		$sql = $sql . ",f_nameLoc";
		$sql = $sql . ",f_pathLoc";
		$sql = $sql . ",f_pathSvr";
		$sql = $sql . ",f_lenLoc";
		$sql = $sql . ",f_sizeLoc";
		$sql = $sql . ",f_md5";
		$sql = $sql . ",f_pidRoot";
		$sql = $sql . ",f_pid";
		$sql = $sql . ",f_lenSvr";
		$sql = $sql . " from up6_files where f_pidRoot=:f_pidRoot and f_complete=0;";

		$db = new DbHelper();	
		$cmd =& $db->GetCommand($sql);
		$db->ExecuteNonQueryConTxt("set names utf8");
		$cmd->bindParam(":f_pidRoot", $fidRoot);
		
		$list = $db->ExecuteDataSet($cmd);
		foreach($list as $row)
		{
			$fi				= new FileInf();
			$fi->idSvr		= intval($row["f_id"]);
			$fi->nameLoc	= $row["f_nameLoc"];
			$fi->pathLoc	= $row["f_pathLoc"];
			$fi->pathSvr	= $row["f_pathSvr"];
			$fi->lenLoc		= $row["f_lenLoc"];
			$fi->sizeLoc	= $row["f_sizeLoc"];
			$fi->md5		= $row["f_md5"];
			$fi->pidRoot	= intval($row["f_pidRoot"]);
			$fi->pidSvr		= intval($row["f_pid"]);
			$fi->lenSvr 	= $row["f_lenSvr"];
			array_push($files,$fi);
		}
	}
}
?>