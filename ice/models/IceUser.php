<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once 'IceModel.php';

class IceUser extends IceModel {
	private $id = 0;
	private $userlevel = 0;
	private $username = "";
	private $passwordhash = "";

	public function __construct($i,$ul,$un,$p, $new=true) {
		$this->id = $i;
		$this->userlevel = $ul;
		$this->username = $un;
		$this->passwordhash = $p;
		$this->newItem = $new;
	}

	/* GET & SET */

	public function getUserLevel() {
		return $this->userlevel;
	}

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setPassword($pass) {
		$this->passwordhash = md5($pass);
		return $this->passwordhash;
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
		$sql = "SELECT id, username, password, userlevel FROM ice_users WHERE username='". $db->escape($name) ."' LIMIT 1";
		return static::querySingle($db, $sql);
	}

	public static function findAll($db) {

		$sql = "SELECT id, username, password, userlevel FROM ice_users WHERE 1";
		return static::queryMultiple($db, $sql);
	}

	public static function byId($db,$id) {
		$id = (int) $id;
		$sql = "SELECT id, username, password, userlevel FROM ice_users WHERE id='". $id ."' LIMIT 1";

		return static::querySingle($db, $sql);
	}

	public static function fromArray($arr, $new=false){
		return new static(
				$arr['id'],
				$arr['userlevel'],
				$arr['username'],
				$arr['password'],
				$new
			);
	}

	/* METHODS */
	public function comparePassword($pass) {
		return ($this->passwordhash == md5($pass));
	}

	public function save($db) {
		if($this->newItem){
			$sql = "INSERT INTO ice_users (username,password,userlevel) VALUES 
			({$this->username}','{$this->passwordhash}','{$this->userlevel}');";
		} else {
			$sql = "UPDATE ice_users SET username = '{$this->username}', password = '{$this->passwordhash}', userlevel = '{$this->userlevel}'
			WHERE id='{$this->id}';";
		}

		$db->query($sql);
	}

}