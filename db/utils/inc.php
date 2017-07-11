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
?>