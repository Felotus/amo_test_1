<?php
class DataFilter{
	static function clear($value = ''){
		$value = trim($value);
	    $value = htmlspecialchars($value);  
	    return $value;
	}
}
