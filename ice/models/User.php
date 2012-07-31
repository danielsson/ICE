<?php 

namespace Ice\Models;
use \Bcrypt;
use Ice\DB;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once 'Model.php';
require_once __DIR__ . '/../lib/DB.php';

class User extends Model {

	private $userlevel = 0;
	private $username = "";
	private $passwordhash = "";
	private $keycardhash = NULL;

	public function __construct($i,$ul,$un,$p=null,$c=null, $new=true) {
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
		$this->passwordhash = self::hash($pass);
		return $this->passwordhash;
	}

	public function setKeyCardHash($key) {
		$this->keycardhash = self::hash($key);
	}

	public function getArray(){
		return array(
			'id' => $this->id,
			'username' => $this->username,
			'userlevel' => $this->userlevel);
	}

	/* FINDERS */
	public static function bySession() {
		if(isset($_SESSION['uid']) && $_SESSION['uid'] != 0) {
			return static::byId($_SESSION['uid']);
		} else {
			return NULL;
		}
	}

	public static function byUsername($name) {
		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE username=? LIMIT 1";
		return static::querySingle($sql, array($name));
	}

	public static function byId($id) {
		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE id=? LIMIT 1";

		return static::querySingle($sql, array((int) $id));
	}

	public static function findAll() {

		$sql = "SELECT id, username, password, userlevel, keyCardHash FROM ice_users WHERE 1";
		return static::queryMultiple($sql, null);
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
		require_once(__DIR__ . '/../lib/bcrypt.class.php');
		return Bcrypt::hash($str);
	}

	/* METHODS */
	public function passwordEquals($pass) {
		require_once(__DIR__ . '/../lib/bcrypt.class.php');
		return Bcrypt::verify($pass, $this->passwordhash);
	}

	public function keyCardHashEquals($key) {
		require_once(__DIR__ . '/../lib/bcrypt.class.php');
		return $this->hasKeyCard() and Bcrypt::verify($key,$this->keycardhash);
	}

	public function hasKeyCard() {
		return $this->keycardhash != NULL and !empty($this->keycardhash);
	}

	public function save() {

		$params = array(
			':username' 	=> $this -> username,
			':passwordhash'	=> $this -> passwordhash,
			':userlevel'	=> $this -> userlevel,
			':keycardhash'	=> $this -> keycardhash
		);

		if($this->newItem){
			$sql = "INSERT INTO ice_users (username,password,userlevel,keyCardHash) VALUES 
			(:username, :passwordhash, :userlevel, :keycardhash);";
		} else {
			$sql = "UPDATE ice_users 
			SET username = :username, password = :passwordhash,
				userlevel = :userlevel, keyCardHash = :keycardhash
			WHERE id=:id;";
			$params[':id'] = $this->id;
		}

		$stmt = DB::prepare($sql);
		if($stmt->execute($params)) {
			if($this->newItem) {
				$this->id = DB::lastInsertId();
				$this->newItem = false;
			}
			return $this->id;
		} else {
			//The query had an error
			throw new Exception("SQL error: " . DB::errorInfo() . $sql, 1);
		}
	}

	public function delete() {
		$stmt = DB::prepare("DELETE FROM ice_users WHERE id=? LIMIT 1;");
		if (!$stmt->execute(array(intval($id)))) {
			throw new Exception("SQL error: " . DB::errorInfo() . $sql, 1);
		}
	}
}