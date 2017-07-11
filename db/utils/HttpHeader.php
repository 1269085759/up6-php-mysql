<?php
/**
 * 用法
 * $head = new HttpHeader();
 * $name = $head->param("f-name");
 * $size = $head->param("f-size");
 * 调试
 * var_dump($head->headers);
 * @author zysoft
 *
 */
class HttpHeader
{
	public $headers;

	//构造函数
	function __construct()
	{
		$this->headers = $this->all();
	}
	
	//析构函数
	function __destruct()
	{
	}

	function param($name)
	{
		foreach($this->headers as $hn => $hv)
		{
			if( strcmp($hn, $name) == 0) return $hv;
		}
		return null;
	}
	
	function all()
	{
		if (!function_exists('getallheaders')) 
		{
			return $this->svrAll();
		}
		else
		{
			return getallheaders();
		}
		
	}	

	function svrAll()
	{
		$headers = array();
		foreach ($_SERVER as  $name  => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
?>