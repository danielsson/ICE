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
		require_once(__DIR__ . '/../models/User.php');

		if(isset($_POST['username'])) {
			$_POST = $this->sanitize($_POST);
			if(strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1) { die('No password and/or username given'); }
			$db->connect();

			$user = Ice\Models\User::byUsername($db, $_POST['username']);
			
			if($user == null) {if($_GET['xhr']=="true"){die(0);} return; }
			
			if($user->passwordEquals($_POST['password'])) {
				$_SESSION['uid']		= $user->getId();
				$_SESSION['username']	= $user->getUsername();
				$_SESSION['userlevel']	= $user->getUserLevel();

				if($_GET['xhr']==true){die('true');}
				if(isset($_POST['nextpage'])) {header('Location: '.urldecode(strip_tags($_POST['nextpage']))); die();}
			} else {
				if($_GET['xhr']=="true"){die(0);}
				$this->error = "Wrong username/password";
			}
		} elseif(isset($_GET['logout'])) {
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
			return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
		}
	}
}

$Auth = new Authentication();

?>