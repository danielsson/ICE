<?php
namespace Ice;

final class Database {
	protected $db;
	protected $db_host, $db_username, $db_password, $db_name;
	
	public function __construct() {
		global $config;
		$this->db_host = $config['db_host'];
		$this->db_username = $config['db_username'];
		$this->db_password = $config['db_password'];
		$this->db_name = $config['db_name'];
		unset($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);
	}

	public function connect() {
		$this->db = mysql_pconnect($this->db_host,$this->db_username,$this->db_password);
		mysql_select_db($this->db_name,$this->db);
		return $this;
	}
	public function close() {
		mysql_close($this->db);
		return true;
	}
	public function escape($str) {
		return mysql_real_escape_string($str, $this->db);
	}
	public function query($q){
		return mysql_query($q, $this->db);
	}
	public function error(){
		return mysql_error($this->db);
	}
}
$db = new Database();



?>