<?php 
	if (!defined("ENVIRONMENT")) { exit; }

	function L($str)
	{
		return DB::get()->load_string($str);
	}
	
	function E($str)
	{
		return DB::get()->escape_string($str);
	}
	
	function T($file,$var = array())
	{
		$t = new Template($file);
		$t->set($var);
		return $t->render();
	}
?>