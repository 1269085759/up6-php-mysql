<?php
//删除数组中的一个元素
function array_remove_value(&$arr, $var)
{
	foreach ($arr as $key => $value) 
	{
		if (is_array($value)) 
		{
			array_remove_value($arr[$key], $var);
		} 
		else 
		{
			$value = trim($value);
			if ($value == $var) 
			{
				unset($arr[$key]);
			} 
			else 
			{
				$arr[$key] = $value;
			}
		}
	}
}

function array_get(&$arr,$key)
{
	foreach($arr as $an => $av)
	{
		if($an == $key)
		{
			return $av;
		}
	}
	return null;
}

//去转义
function unTurn($val) {
	if(is_array($val)) {
		$val = array_map('unTurn', $val);
	}else {
		$val = stripslashes($val);
	}
	return $val;
}

function new_guid()
{
	$ret = "";
	if (function_exists('com_create_guid'))
	{
		$ret = com_create_guid();
	}
	else
	{
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12)
		.chr(125);// "}"
		$ret = $uuid;
	}
	$ret = str_replace("{","",$ret);
	$ret = str_replace("}","",$ret);
	$ret = str_replace("-","",$ret);
	$ret = strtolower($ret);
	return $ret;
}
?>