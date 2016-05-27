<?php 
	if (!defined("ENVIRONMENT")) exit; 
	
	//PAGE
	define("TITLE", "FlashCards");
	
	
	//DATABASE
	//not including DB login for security reasons
	include_once "db.php"
	
	//TABLES
	define("DBPRE", "dev__");
	
	define("PAGES", DBPRE."pages");
	define("USERS", DBPRE."users");
	define("COURSES", DBPRE."courses");
	define("COURSES_CARDS", DBPRE."courses_cards");
	
?>