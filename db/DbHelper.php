<?php
/*
	说明：
		此数据库访问类使用PDO，使用前请先正确配置PDO
		
	更新记录：
		2014-08-05 创建
*/
class DbHelper
{
	var $m_host;	//数据库地址
	var $m_dbName;	//数据库名称
	var $m_uname;	//帐号
	var $m_upass;	//密码
	var $m_dbStr;	//数据库连接字符串
	var $m_conCur = null;
	var $m_con_utf8 = null;

	function __construct() 
	{
        $this->m_host 	= "localhost";  //
		$this->m_dbName = "HttpUploader6";
		$this->m_uname	= "root";
		$this->m_upass	= "";
		$this->m_dbStr = "mysql:host=" . $this->m_host . ";dbname=" . $this->m_dbName;		
	}
	
	function &GetCon()
	{
		if(empty($this->m_conCur))
		{
			$con = new PDO($this->m_dbStr,$this->m_uname,$this->m_upass);
			$this->m_conCur = $con;//保存连接			
		}				
		return $this->m_conCur;
	}
	
	function &GetConUtf8()
	{
		//if(empty($this->m_con_utf8))
		//{
			$con = new PDO($this->m_dbStr,$this->m_uname,$this->m_upass
							,array(
						        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET names utf8',
						        //PDO::ATTR_PERSISTENT => true//bug:使用长连接就报错
					    	)
						);
		//}				
		//return $this->m_con_utf8;
		return $con;
	}
	
	function GetConCur()
	{
		return $this->m_conCur;
	}
	
	/**
	 * 自动创建命令对象。返回引用
	 * @param sql
	 * @return
	 */
	function &GetCommand($sql)
	{
		$con =& $this->GetCon();
		//$this->m_conCur = $con;//保存连接
		$stmt = $con->prepare($sql);
		return $stmt;
	}
	
	function prepare($sql)
	{
		$con =& $this->GetCon();
		
		$stmt = $con->prepare($sql);
		return $stmt;		
	}
	
	/*
	 * 每次都重新创建一个statement
	 * */
	function prepare_utf8($sql)
	{
		$con = $this->GetConUtf8();
		$this->m_conCur = $con;//保存当前连接
		
		$stmt = $con->prepare($sql);
		return $stmt;
	}
	
	/**
	 * 执行SQL,自动关闭数据库连接
	 * @param cmd
	 * @return
	 */
	function ExecuteNonQuery(&$cmd)
	{	
		try
		{
			$cmd->execute();
		}
		catch(PDOException $e)
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	function ExecuteGenKey(&$cmd,$key_name)
	{
		$key = null;
		try
		{
			$cmd->execute();
			$key = $this->m_conCur->lastInsertId($key_name);			
		}
		catch(PDOException $e)
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $key;
	}
	
	/**
	 * 执行SQL,自动关闭数据库连接
	 * @param cmd
	 * @return
	 */
	function ExecuteNonQueryTxt($sql)
	{		
		$con = $this->GetCon();
		$con->exec($sql);
	}
	
	function ExecuteNonQueryConTxt($sql)
	{
		try 
		{
			$this->m_conCur->exec($sql);
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	/**
	 * 执行SQL
	 * @param cmd
	 * @return 
	 */
	function Execute(&$cmd)
	{		
		$ret = false;
		try 
		{
			$ret = $cmd->execute();
		}
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
	
	/**
	 * 使用方法：
	 * exeProRet("sp_getName","@a,@b,@c")
	 * 执行SQL,自动关闭数据库连接
	 * @sql 存储过程SQL语句，call fd_lastInsertId(@aid)
	 * 输出参数必须添加@符号
	 * @param 参数 @a,@b,@c
	 * @return 
	 */
	function exeProRet($sql,$param)
	{
		$this->GetCon();
		
		$con = &$this->m_conCur;
		$outVal = null;
		
		try 
		{
			$cmd = $con->prepare("call $sql($param)");
			$cmd->execute();
			$outVal = $con->query("select $param")->fetch(PDO::FETCH_ASSOC);			
		}
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $outVal;
	}
	
	/**
	 * 执行SQL,自动关闭数据库连接
	 * @param cmd
	 * @return
	 */
	function ExecuteScalar(&$cmd)
	{		
		$ret = 0;
		try 
		{
			$cmd->execute();
			$ret = $cmd->fetchColumn();//获取第1列数据
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
	
	/**
	 * 执行SQL,自动关闭数据库连接
	 * @param cmd
	 * @return
	 */
	function ExecuteScalarCmdTxt(&$cmd,$sql)
	{		
		$ret = 0;
		try 
		{
			$count = $cmd->exec(sql);
			$ret = $cmd->fetchColumn();
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
	
	/**
	 * 执行SQL,自动关闭数据库连接
	 * @param cmd
	 * @return
	 */
	function ExecuteScalarTxt($sql)
	{		
		$ret = 0;
		try 
		{
			$cmd =& $this->GetCommand(sql);
			$cmd->execute();
			$ret = $cmd->fetchColumn();
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
	
	/*
		@return array,
	*/
	function ExecuteRow(&$cmd)
	{
		$ret = array();
		try 
		{
			//$cmd =& $this->GetCommand(sql);
			$cmd->execute();
			$ret = $cmd->fetch();
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
	
	/**
	 * 注意：外部必须关闭ResultSet，connection,
	 * ResultSet索引基于1
	 * @param cmd
	 * @return
	 */
	function ExecuteDataSet(&$cmd)
	{
		$ret = null;
		try 
		{
			$cmd->execute();
			$ret = $cmd->fetchAll();
		} 
		catch (PDOException $e) 
		{
			print "Error!:" . $e->getMessage() . "<br/>";
			die();
		}
		return $ret;
	}
}
?>