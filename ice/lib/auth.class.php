<?php
class Authentication {
	
	public $error;
	
	public function __construct() {
		session_start();

		if(!isset($_SESSION['userlevel'])) $_SESSION['userlevel'] = 0;
		
		return true;
	}
	public function init($req_userlevel){
		global $config;
		if($_SESSION['userlevel'] < 1 && $req_userlevel > 0) {
			if($_GET['xhr']!="true") {
				header("Location: " . $config['baseurl'] . $config['sys_folder'] . 'admin/#login');
			}
			die('UserLevel==zero');
		}
		elseif($_SESSION['userlevel']<$req_userlevel) {
			die('You are not allowed to view this page.');
		}
	}
	public function loginProcess() {
		global $db;
		if(isset($_POST['username'])) {
			$_POST = $this->sanitize($_POST);
			if(strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1) { die('No password and/or username given'); }
			$db->connect();
			$sql = "SELECT id, username, password, userlevel FROM ice_users WHERE username='".$_POST['username']."' LIMIT 1";
			$result = $db->query($sql);
			
			if(!$result) {if($_GET['xhr']=="true"){die(0);} return; }
			
			while($row = mysql_fetch_array($result))
			  {
			  	
				if($row['password']==md5($_POST['password'])) {
					$_SESSION['uid']=$row['id'];
					$_SESSION['username']=$row['username'];
					$_SESSION['userlevel'] = $row['userlevel'];
					if($_GET['xhr']==true){die('true');}
					if(isset($_POST['nextpage'])) {header('Location: '.urldecode(strip_tags($_POST['nextpage']))); die();}
					continue;
				} else {
					if($_GET['xhr']=="true"){die(0);}
					$this->error = "Wrong username/password";
					continue;
				}
			  }
		if($_GET['xhr']=="true"){die(0);}
			  
		}
		elseif(isset($_GET['logout'])){
			session_destroy();
			header('Location: ./');
		}
	}
	public function sanitize($input) {
		
		if(is_array($input)) {
		  foreach($input as $key => $val){
			  $input[$key] = $this->sanitize($val);
		  }
		  return $input;
		}
		else if(is_string($input)) {
			return filter_var($input, FILTER_SANITIZE_STRING);
		}
		else if(is_int($input)) {
			return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
		}
		else if(is_float($input)) {
			return ilter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
		}
	}
}

$Auth = new Authentication();

?>