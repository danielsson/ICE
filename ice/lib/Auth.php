<?php
/**
 * Session management class
 * 
 */
namespace Ice;
use Ice\Models\User;

require_once(__DIR__ . '/../models/User.php');

class Auth {

	public static function init($required_userlevel = 0) {
		global $config;
		session_start();

		if(!isset($_SESSION['userlevel'])) {
			$_SESSION['userlevel'] = 0;
		}

		self::requires($required_userlevel);
	}

	//TODO: Better handling of failure
	public static function requires($required_userlevel = 0) {
		if ($_SESSION['userlevel'] < 1
			&& $required_userlevel > 0) {

			header("Location: " . $config['baseurl'] . $config['sys_folder'] . 'admin/#login');
			die('UserLevel==zero');
		}
		elseif($_SESSION['userlevel'] < $required_userlevel) {
			die('You are not allowed to view this page.');
		}
	}

	public static function login($username, $password) {
		$user = User::byUsername($username);

		if($user === null) {
			return null;
		}

		if($user->passwordEquals($password)) {
			$_SESSION['uid']		= $user->getId();
			$_SESSION['username']	= $user->getUsername();
			$_SESSION['userlevel']	= $user->getUserLevel();

			return $user;
		} else {
			return null;
		}
	}

	public static function logout() {
		session_destroy();
		header('Location: ./');
	}

	public static function sanitize($input) {
		
		if(is_array($input)) {
			foreach($input as $key => $val){
				$input[$key] = self::sanitize($val);
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