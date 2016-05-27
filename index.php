<?php
	//Start the session and define enviroment for security reasons
    session_start();
	define("ENVIRONMENT", true);
	
	//Set header just incase
	header("Content-Type: text/html; charset=utf-8");
	
	//Include all the config options to set stuff up
	include_once "config.php";
	
	//Layout HTML
	$html = new Template(TPATH."html.tpl");
	
	//Set CSS and JS
	$html->addCSS(WEBPATH."resources/css/index.css?".rand(0,1000));
	$html->addJS(WEBPATH."resources/js/functions.js");
	$html->addJS(WEBPATH."resources/js/jquery-2.2.4.js");
	
	//Set Content
	$html->set('content',$PAGE->content());
	
	$class = array();
	if(USER::get()->theme()){
		$class[] = "theme_".(int)USER::get()->theme();
	}
	if(USER::get()->card()){
		$class[] = "card_".(int)USER::get()->card();
	}
	//Set variables
	$html->set('title',TITLE.(!empty($PAGE->title)?" | ".$PAGE->title:""));
	
	$html->set('class',join(" ",$class));
	$html->set('css',$html->returnCSS());
	$html->set('js',$html->returnJS());
	
	//RENDER
	echo $html->render();
	exit;
?>
