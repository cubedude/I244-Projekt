<?php 
	if (!defined("ENVIRONMENT")) exit; 
	
	class DB
	{
		public $link;
		public $next_id;
		public $last_query = "";
		public $last_result = "";
		public $result = array();
		public $array_type = MYSQLI_ASSOC;
		public $debug = true;
		
		private static $instance;

		private function __construct() {}
		private function __clone() {}

		public static function get() {
			if (!DB::$instance instanceof self) {
				 DB::$instance = new self();
			}
			return DB::$instance;
		}
		
		public function mysqli_conn($server = "", $username = "", $password = "", $database = "")
		{
			if (function_exists('mysqli_connect')) {
				$this->link = mysqli_connect($server, $username, $password, $database) or die("Can't connect");
			} else {
				$this->link = "";
			}
			if (!$this->link) {
				die("Can't connect");
			}
			mysqli_query($this->link, "SET CHARACTER SET utf8");
			mysqli_query($this->link, "SET character_set_client='utf8'");
			mysqli_query($this->link, "SET character_set_results='utf8'");
			mysqli_query($this->link, "SET collation_connection='utf8_general_ci'");
		}
		public function Update($post, $tabel, $id = "", $is_id = true)
		{
			$tulemid = null;
			$i = null;
			$where = '';
			foreach ($post as $sForm => $value) {
				if (!is_null($value)) {
					$value = preg_replace_callback("/&[^\s]+;/i", array(
						$this,
						"html_entity_dc"
					), $value);
				}
				$i++;
				$tulemid .= (($i == "1") ? "" : ", ") . "`" . addslashes($sForm) . "` = " . ((is_null($value) || $value == "null") ? "NULL" : "'" . $this->escape_string($value) . "'");
			}
			if ($id != "" && $is_id == true) {
				$where = " WHERE id = '" . (INT) $id . "'";
			} else if ($id != "" && $is_id == false) {
				$where = " WHERE " . $id;
			}
			return $this->query("UPDATE " . $tabel . " SET " . $tulemid . $where) or die($this->error());
		}
		public function Insert($post, $tabel)
		{
			$i = 0;
			$cols = array();
			$vals = array();
			foreach ($post as $sForm => $value) {
				if (!is_null($value)) {
					$value = preg_replace_callback("/&[^\s]+;/i", array(
						$this,
						"html_entity_dc"
					), $value);
				}
				$cols[] = $sForm;
				$vals[] = ((is_null($value) || $value == "null") ? "NULL" : "'" . $this->escape_string($value) . "'");
			}
			$return = $this->query("INSERT INTO " . $tabel . " (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ")") or die($this->error(Debug::$depth_10));
			return $return;
		}
		public function Insert_Update($post, $tabel)
		{
			$i = 0;
			$cols = array();
			$vals = array();
			$tulemid = null;
			foreach ($post as $sForm => $value) {
				if (!is_null($value)) {
					$value = preg_replace_callback("/&[^\s]+;/i", array(
						$this,
						"html_entity_dc"
					), $value);
				}
				$cols[] = $sForm;
				$vals[] = ((is_null($value) || $value == "null") ? "NULL" : "'" . $this->escape_string($value) . "'");
				
				$i++;
				$tulemid .= (($i == "1") ? "" : ", ") . "`" . addslashes($sForm) . "` = " . ((is_null($value) || $value == "null") ? "NULL" : "'" . $this->escape_string($value) . "'");
			}
			$return = $this->query("INSERT INTO " . $tabel . " (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ") ON DUPLICATE KEY UPDATE " . $tulemid) or die($this->error(Debug::$depth_10));
			return $return;
		}
		public function Replace_Into($post, $tabel)
		{
			$tulemid = null;
			$i = null;
			foreach ($post as $sForm => $value) {
				if (!is_null($value)) {
					$value = preg_replace_callback("/&[^\s]+;/i", array(
						$this,
						"html_entity_dc"
					), $value);
				}
				$i++;
				$tulemid .= (($i == "1") ? "" : ", ") . "`" . addslashes($sForm) . "` = " . ((is_null($value) || $value == "null") ? "NULL" : "'" . $this->escape_string($value) . "'");
			}
			return $this->query("REPLACE INTO " . $tabel . " SET " . $tulemid) or die($this->error());
		}
		public function Delete($tabel, $id = "", $is_id = true)
		{
			if ($id != "" && $is_id == true) {
				$where = " WHERE id = '" . (INT) $id . "'";
			} else if ($id != "" && $is_id == false) {
				$where = " WHERE " . $id;
			} else if ($id === NULL) {
				return false;
			}
			$this->query("DELETE from " . $tabel . $where) or die($this->error());
		}
		public function errno()
		{
			return mysqli_errno($this->link);
		}
		public function error($level = 2)
		{
			$h = "Error no: " . $this->errno($this->link) . ".<br />";
			$h .= mysqli_error($this->link) . "<br />";
			$h .= "<font color=\"red\">" . $this->last_query . "</font>";
			
			return $h;
		}
		public function transaction($commit = 0)
		{
			if (!$commit) {
				$query = "START TRANSACTION;";
			} else {
				$query = "COMMIT;";
			}
			$this->query($query);
		}
		public function escape($string)
		{
			return $this->escape_string($string);
		}
		public function escape_string($string)
		{
			$replace = is_object(json_decode($string)) || is_array(json_decode($string)) ? false : true;
			$string  = mysqli_real_escape_string($this->link, $string);
			if ($replace) {
				$string = preg_replace('/(\\\)+("|\')/', '\\\${2}', $string);
			}
			return $string;
		}
		public function load_string($string)
		{
			return htmlspecialchars($string);
		}
		public function query($query, $level = 3, $single = false)
		{
			$this->last_query = $query;
			$result = mysqli_query($this->link, $query) or die($this->error($level));
			if (mb_strtolower(mb_substr(trim($query), 0, 6)) == "insert" || mb_strtolower(mb_substr(trim($query), 0, 12)) == "replace into") {
				$this->next_id = mysqli_insert_id($this->link);
			}
			if ($result instanceof mysqli_result) {
				$this->last_result = $result;
				$count = mysqli_num_rows($result);
				if ($count == 0 || $single) {
					return $result;
				}
				$unique = debug_backtrace(false);
				$key = "r" . md5($query . $unique[2]["file"] . $unique[2]["line"]); #row-unique-key
				$this->result[$key] = array(
					"result" => $result,
					"count" => $count
				);
			}
			return $result;
		}
		private function handleResult($result, $single)
		{
			if (is_string($result)) {
				$unique = debug_backtrace(false);
				$key    = "r" . md5($result . $unique[1]["file"] . $unique[1]["line"]); #row-unique-key                    
				if (isset($this->result[$key])) {
					$this->result[$key]["count"]--;
					if ($this->result[$key]["count"] < 1) {
						@mysqli_free_result($this->result[$key]["result"]);
						$this->last_result = false;
						unset($this->result[$key]);
						$result = null;
					} else {
						$result = $this->result[$key]["result"];
					}
				} else {
					$result = $this->query($result, 5, $single);
				}
			}
			return $result;
		}
		public function fetch_array($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				return mysqli_fetch_array($result, $this->array_type);
			}
		}
		public function fetch_row($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				return mysqli_fetch_row($this->handleResult($result, $single));
			}
		}
		public function fetch_assoc($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				return mysqli_fetch_assoc($this->handleResult($result, $single));
			}
		}
		public function data_seek($result, $key)
		{
			return mysqli_data_seek($result, $key);
		}
		public function fetch_object($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				return mysqli_fetch_object($this->handleResult($result, $single));
			}
		}
		public function num_rows($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				return mysqli_num_rows($this->handleResult($result, $single));
			}
		}
		public function fetch_value($result, $single = false)
		{
			$result = $this->handleResult($result, $single);
			if (is_null($result)) {
				return null;
			} else {
				$this->array_type = MYSQLI_NUM;
				$v = mysqli_fetch_row($this->handleResult($result, $single));
				$this->array_type = MYSQLI_ASSOC;
				return $v[0];
			}
		}
		public function close()
		{
			return ((is_resource($this->link)) ? mysqli_close($this->link) : false);
		}
		
		function __destruct()
		{
			$this->close();
		}
		
		function html_entity_dc($match)
		{
			return html_entity_decode($match[0], ENT_QUOTES, "UTF-8");
		}
	}
?>