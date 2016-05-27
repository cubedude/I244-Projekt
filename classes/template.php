<?php 
	if (!defined("ENVIRONMENT")) exit; 

	class Template 
	{		
		protected $file;
		protected $values = array();
		
		private $css = array();
		private $js = array();
		private $jsOnLoad = "";
	  
		public function __construct($file) 
		{
			if($file){
				$this->get($file);
			}
		}
		
		public function get($file) 
		{
			$this->file = $file;
			$this->values = array();
			$this->css = array();
			$this->js = array();
			$this->jsOnLoad = "";
		}
		
		public function set($vars, $value = "") 
		{
			if(is_array($vars)){
				if(!empty($vars)){
					$this->values = array_merge($vars, $this->values);
				}
			}
			else if(!empty($vars)){
				$this->values[$vars] = $value;
			}
		}
		
		public function addCSS($script = "") 
		{
			if($script){
				$this->css[] = $script;
			}
		}
		
		public function addJS($script = "") 
		{
			if($script){
				$this->js[] = $script;
			}
		}
		
		public function addJSOnLoad($script = "") 
		{
			if($script){
				$this->jsOnLoad .= $script;
			}
		}
		
		public function returnCSS() 
		{
			$h = "";
			foreach($this->css as $key => $script){
				$h .= "<link type=\"text/css\" href=\"".$script."\" rel=\"stylesheet\" />";
			}
			return $h;
		}
		
		public function returnJS() 
		{
			$h = "";
			foreach($this->js as $key => $script){
				$h .= "<script type=\"text/javascript\" src=\"".$script."\"></script>";
			}
			return $h;
		}
		  
		public function render() 
		{
			if(is_file($this->file)){
				if (!file_exists($this->file)) return "";
				$html = file_get_contents($this->file);
			}else{
				$html = $this->file;
			}
		  
			foreach ($this->values as $key => $value) {
				$html = str_replace("{".$key."}", $value, $html);
			}
			
			if(!empty($this->jsOnLoad)){
				$html .= "<script type=\"text/javascript\">
					$(function(){
						".$this->jsOnLoad."
					});
				</script>";
			}
		  
			return $html;
		}
		
	}
?>