<?php 
	if (!defined("ENVIRONMENT")) exit; 

	class User
	{
		public $id = 0;
		public $limit = 1;
		private $card = 0;
		private $theme = 0;
		
		private $course = 0;
		private $lastQuestion = 0;
		private $awnserd = array();

		private static $instance;
		private function __clone() {}
		
		private function __construct() {
			if(isset($_GET["logout"])){
				$this->logout();
			}
			
			if(!empty($_SESSION["id"])){
				if($user = DB::get()->fetch_array("SELECT * FROM ".USERS." WHERE id = '".(int)$_SESSION["id"]."'",true)){
					$this->login($user);
				}
			}
			
			if(isset($_REQUEST["card"])){
				$this->card((int)$_REQUEST["card"]);
			}
			
			if(isset($_REQUEST["theme"])){
				$this->theme((int)$_REQUEST["theme"]);
			}
			
			if(isset($_SESSION["course"])){
				$this->course = $_SESSION["course"];
				if(isset($_SESSION["lastQuestion"])) $this->lastQuestion = $_SESSION["lastQuestion"];
				if(isset($_SESSION["awnserd"])) $this->awnserd = $_SESSION["awnserd"];
			}
			
			if(isset($_REQUEST["course"])){
				$this->course((int)$_REQUEST["course"]);
			}
			
			if(isset($_REQUEST["correct"]) && $this->lastQuestion){
				$this->awnsered((int)$this->lastQuestion);
			}
		}
		
		public static function get() {
			if (!User::$instance instanceof self) {
				 User::$instance = new self();
			}
			return User::$instance;
		}
		
		private function login($user){
			$_SESSION["id"] = (int)$user["id"];
			$this->id = (int)$user["id"];
			$this->limit = 2;			
			$this->card = (int)$user["card"];			
			$this->theme = (int)$user["theme"];				
		}
		
		function logout(){
			unset($_SESSION["id"]);
			$this->id = 0;
			$this->limit = 1;		
			header("location.reload()");
		}
		
		function awnsered($question = ""){
			if($question !== ""){
				$this->awnserd[] = (int)$question;
				$_SESSION["awnserd"][] = (int)$question;
				$this->lastQuestion = 0;
				$_SESSION["lastQuestion"] = 0;
			}else{
				return $this->awnserd;
			}
		}
		
		function lastQuestion($lastQuestion = ""){
			if($lastQuestion !== ""){
				$this->lastQuestion = (int)$lastQuestion;
				$_SESSION["lastQuestion"] = (int)$lastQuestion;
			}else{
				return $this->lastQuestion;
			}
		}
		
		function card($card = ""){
			if($card !== ""){
				$this->card = (int)$card;
				if($this->id) DB::get()->Update(array("card"=>(int)$card),USERS,$this->id);
			}else{
				return $this->card;
			}
		}
		
		function theme($theme = ""){
			if($theme !== ""){
				$this->theme = (int)$theme;
				if($this->id) DB::get()->Update(array("theme"=>(int)$theme),USERS,$this->id);
			}else{
				return $this->theme;
			}
		}
		function course($course = ""){
			if($course !== "" && $course != $this->course){
				if($cour = DB::get()->fetch_array("SELECT * FROM ".COURSES." WHERE id = '".(int)$course."' AND (user_id = '".$this->id."' OR public = 1)",true)){
					$this->course = (int)$cour["id"];
					$this->lastQuestion = 0;
					$this->awnserd = array();
					$_SESSION["course"] = (int)$cour["id"];
					$_SESSION["lastQuestion"] = 0;
					$_SESSION["awnserd"] = array();
				}
				else{
					$this->course = 0;
					$this->lastQuestion = 0;
					$this->awnserd = array();
					$_SESSION["course"] = 0;
					$_SESSION["lastQuestion"] = 0;
					$_SESSION["awnserd"] = array();
				}
			}else{
				return $this->course;
			}
		}
		
		function loginValidate($email = "", $pass = ""){
			$errors = array();
			if(empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errors["email"] = true;
			}
			if(empty(trim($pass))){
				$errors["pass"] = true;
			}
			
			if(empty($errors)){
				if($user = DB::get()->fetch_array("SELECT * FROM ".USERS." WHERE email = '".E($email)."'",true)){
					if(password_verify($pass,$user["password"])){
						$this->login($user);
					}else{
						$errors["pass"] = true;
					}					
				}else{
					$errors["email"] = true;
				}
			}
			
			return $errors;
		}
		
		function registerValidate($email = "", $pass = ""){
			$errors = array();
			
			if(empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errors["email"] = true;
			}
			if(empty(trim($pass))){
				$errors["pass"] = true;
			}
			else if(strlen(trim($pass)) <= 3){
				$errors["pass"] = true;
				$errors["other"][] = "<label>Password needs to be longer</label>";
			}
			
			if(!$errors){
				if($user = DB::get()->fetch_array("SELECT * FROM ".USERS." WHERE email = '".E($email)."'",true)){
					$errors["email"] = true;
					$errors["other"][] = "<label>Email allready exists.</label>";
				}else{
					$SQL["email"] = $email;
					$SQL["password"] = password_hash(trim($pass),PASSWORD_DEFAULT);
					$SQL["card"] = $this->card;
					$SQL["theme"] = $this->theme;
					$SQL["time"] = time();
					DB::get()->Insert($SQL,USERS);
					$SQL["id"] = DB::get()->next_id;
					$this->login($SQL);
				}
			}
			
			return $errors;
		}
		
	}
?>