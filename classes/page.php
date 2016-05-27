<?php 
	if (!defined("ENVIRONMENT")) exit; 

	class Page
	{
		public $id;
		public $rid;
		public $domain;
		public $title;
		/*
		
		function __construct()
		{
			$raw_url = explode("?",$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
			$urls = explode("/",$raw_url[0]);
			$this->domain = array_shift($urls);
			
			if(is_array($urls) && !empty($urls))
			{
				$vurl = array();
				foreach($urls as $pointer => $url){
					if($page = DB::get()->fetch_array("SELECT id, title FROM ".PAGES." WHERE url = '".E($url)."' AND (access = 0 OR access = '".(int)USER::get()->limit."') AND gid = '".(!empty($vurl)?(int)end($vurl):0)."'")){
						$vurl[] = $page["id"];
						$this->title = $page["title"];
					}
				}
				$this->rid = $vurl;
				$this->id = array_reverse($vurl);
			}        
		}
			
		public function content(){
			if(empty($this->id)){
				$main = new Template(TPATH."page.tpl");
				
				$main->set('side',$this->sideMenu());
				$main->set('main',$this->mainMenu());
				$main->set('extra','');
				
				
				$h = $main->render();
			}
			else{
				if($page = DB::get()->fetch_array("SELECT id, title, act FROM ".PAGES." WHERE id = '".(int)end($this->id)."'")){
					$h = "";
				
				}				
			}
			
			return $h; 
		}
		*/
		
		public function content(){
			$main = new Template(TPATH."page.tpl");
			
			if(USER::get()->course()){
				$max = (int)DB::get()->fetch_value("SELECT count(*) FROM ".COURSES_CARDS." WHERE gid = '".(int)USER::get()->course()."'",true);
				$awnsered = (int)count(USER::get()->awnsered());
				if($max <= $awnsered){
					USER::get()->course(0);
				}else{
					$main->set('side',$this->scoreboard());
					$main->set('main',$this->question());
					$main->set('class',"question");
					$main->set('extra',T(TPATH."extras.tpl"));
					
					$main->addJSOnLoad("
						$('.question > div > .front').click(function(){
							$(this).parent().addClass('flipped');
							console.log('(╯°□°）╯︵ ┻━┻');
						});
					");
				}
			}
			
			if(!USER::get()->course()){
				$main->set('side',$this->sideMenu());
				$main->set('main',$this->mainMenu());
				$main->set('extra',"");
			}
						
			return $main->render(); 
		}
		public function question(){
			$h = new Template(TPATH."question.tpl");
			
			if($ques = DB::get()->fetch_array("SELECT * FROM ".COURSES_CARDS." WHERE gid = '".USER::get()->course()."' ".(!empty(USER::get()->awnsered())?"AND id NOT IN ('".join("','",USER::get()->awnsered())."')":"")." ORDER BY RAND() LIMIT 1",true)){
				USER::get()->lastQuestion($ques["id"]);
				$h->set('question',L($ques["question"]));
				$h->set('awnser',L($ques["awnser"]));
			}
					
			return $h->render();
		}
		
		public function mainMenu(){
			$h = new Template(TPATH."mainmenu.tpl");
			$courses = "";
			while($course = DB::get()->fetch_array("SELECT * FROM ".COURSES." WHERE user_id = '".(int)USER::get()->id."' OR public = 1")){
				$courses .= "<li><a href=\"?course=".(int)$course["id"]."\">".L($course["title"])."</a></li>";
			}
			
			$h->set('courses',$courses);
			
			return $h->render();
		}
		
		public function sideMenu(){
			$h = "";				
			if(!User::get()->id){
				$email = (!empty($_POST["email"])?$_POST["email"]:"");
				$pass = (!empty($_POST["pass"])?$_POST["pass"]:"");
				$errors = array();
				
				if(isset($_POST["login"])){
					$errors = USER::get()->loginValidate($email,$pass);
				}
				elseif(isset($_POST["register"])){
					$errors = USER::get()->registerValidate($email,$pass);
				}
								
				$login = new Template(TPATH."login.tpl");
				$login->set(array(
					'email' => L($email),
					'eclass' => (!empty($errors["email"])?"error":""),
					'pclass' => (!empty($errors["pass"])?"error":""),
					'error' => (!empty($errors["other"])?join("",$errors["other"]):""),
				));
				$h = $login->render();
			}
			
			if(User::get()->id){
				$profile = new Template(TPATH."profile.tpl");
				
				$cards = "";
				for($i = 0; $i < 4; $i++){
					$cards .= "<a href=\"?card=".$i."\" class=\"card icon card_".$i." ".(USER::get()->card() == $i?"active":"")."\">&nbsp;</a>";
				}
				$themes = "";
				for($i = 0; $i < 4; $i++){
					$themes .= "<a href=\"?theme=".$i."\" class=\"icon theme_".$i." ".(USER::get()->theme() == $i?"active":"")."\">&nbsp;</a>";
				}
				
				$profile->set(array(
					'cardback' => $cards,
					'background' => $themes,
				));
				$h = $profile->render();
			}	
			
			return $h;
		}
		
		public function scoreboard(){
			$h = new Template(TPATH."score.tpl");
			
			if($ques = DB::get()->fetch_array("SELECT * FROM ".COURSES." WHERE id = '".USER::get()->course()."'",true)){
				$h->set('course',L($ques["title"]));
				$h->set('max',(int)DB::get()->fetch_value("SELECT count(*) FROM ".COURSES_CARDS." WHERE gid = '".(int)$ques["id"]."'",true));
				$h->set('awnser',(int)count(USER::get()->awnsered()));
			}
					
			return $h->render();
		}
		
	}
?>