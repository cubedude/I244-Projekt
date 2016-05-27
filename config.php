<?php 
	if (!defined("ENVIRONMENT")) exit; 

	//WEBPATH AND BASEPATH
	$p1 = explode("/",strrev($_SERVER["SCRIPT_NAME"]));
	$p2 = "";
	for ($i = 1; $p1[$i] != ""; $i++) $p2 .= "/".$p1[$i];

	define("WEBPATH", "http://".$_SERVER["HTTP_HOST"]."/".utf8_encode(strrev($p2)));
	define("BASEPATH", dirname(__FILE__)."/");
	
	include_once BASEPATH."define.php";

	//CREATE DB CONNECTION
	include_once BASEPATH."classes/mysql.php";
	DB::get()->mysqli_conn(DBSERVER, DBUSER, DBPASS, DBDB);
	
	//INITATE USER CLASS
	include_once BASEPATH."classes/user.php";
	
	//INCLUDE FUNCTIONS
	include_once BASEPATH."functions.php";
	
	//INCLUDE TEMPLATE ENGINE
	include_once BASEPATH."classes/template.php";
	define("TPATH",BASEPATH."resources/templates/");
	
	
	//INITATE PAGE CLASS
	include_once BASEPATH."classes/page.php";
	$PAGE = new Page();
	
	
?>