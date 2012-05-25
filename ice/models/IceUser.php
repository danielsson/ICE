<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once 'IceModel.php';

class IceUser extends IceModel {

	private $userlevel = 0;
	private $username = "";
	private $passwordhash = "";
	private $keycardhash = NULL;

	public function __construct($i,$ul,$un,$p,$c=NULL, $new=true) {
		$this->id = $i;
		$this->userlevel = $ul;
		$this->username = $un;
		$this->passwordhash = $p;
		$this->keycardhash = $c;
		$this->newItem = $new;
	}

	/* GET & SET */
	public function getId() {return $this->id;}

	public function getUserLevel() {return $this->userlevel;}
	public function setUserLevel($l) {$this->userlevel = $l;}

	public function getUsername() {return $this->username;}
	public function setUsername($n) {$this->username = $n;}

	public function setPassword($pass) {
		$this->passwordhash = md5($pass);
		return $this->passwordhash;
	}

	public function setKeyCardHash($hash) {
		$this->keycardhash = $hash;
	}

	public function getArray(){
		return array(
			'id' => $this->id,
			'username' => $this->username,
			'userlevel' => $this->userlevel);
	}

	/* FINDERS */
	public static function bySession($db) {
		if(isset($_SESSION['uid']) && $_SESSION['uid'] != 0) {
			return static::byId($db,$_SESSION['uid']);
		} else {
			return NULL;
		}
	}

	public static function byUsername($db, $name) {
		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE username='". $db->escape($name) ."' LIMIT 1";
		return static::querySingle($db, $sql);
	}

	public static function findAll($db) {

		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE 1";
		return static::queryMultiple($db, $sql);
	}

	public static function byId($db,$id) {
		$id = (int) $id;
		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE id='". $id ."' LIMIT 1";

		return static::querySingle($db, $sql);
	}

	public static function fromArray($arr, $new=false){
		return new static(
				$arr['id'],
				$arr['userlevel'],
				$arr['username'],
				$arr['password'],
				$arr['keyCardHash'],
				$new
			);
	}
	public static function hash($str) {
		//Highly temporary
		return md5($str);
	}

	/* METHODS */
	public function passwordEquals($pass) {
		return ($this->passwordhash == self::hash($pass));
	}

	public function keyCardHashEquals($key) {
		return $this->hasKeyCard() and $this->keycardhash === self::hash($key);
	}

	public function hasKeyCard() {
		return $this->keycardhash != NULL and !empty($this->keycardhash);
	}

	public function save($db) {
		if($this->newItem){
			$sql = "INSERT INTO ice_users (username,password,userlevel,keyCardHash) VALUES 
			('{$this->username}','{$this->passwordhash}','{$this->userlevel}','{$this->keycardhash}');";
		} else {
			$sql = "UPDATE ice_users 
			SET username = '{$this->username}', password = '{$this->passwordhash}', userlevel = '{$this->userlevel}', keyCardHash = '{$this->keycardhash}'
			WHERE id='{$this->id}';";
		}

		$db->query($sql);
		
		if($db->error()) {
			throw new Exception("SQL error: " . $db->error() . $sql, 1);
		}
		
		if($this->newItem) {
			$this->id = mysql_insert_id();
			$this->newItem = false;
		}
			
		return $this->id;
	}
}