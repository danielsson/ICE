<?php
/**
 * Simple magic PDO helper
 * 
 * This is a magic singleton: You can treat it as a PDO instance
 * if you call its members statically. Example:
 * 
 *     $pdo = new PDO( ... );
 *     $stmt = $pdo->prepare( ... ); 
 * 
 * Can be written as:
 * 
 *     $stmt = DB::prepare( ... )
 * 
 * The class connects to the database defined in the global
 * $config variable.
 *
 * @global $config
 */

namespace Ice;
use \PDO;

class DB {
	protected static $instance;

	/**
	 * Simple method to return a PDO instance
	 * @return PDO instance
	 * @global $config
	 */
	public static function instance() {
		global $config;

		if (self::$instance === null) {
			try {
				self::$instance = new PDO(
					$config['db_connection'],
					$config['db_username'],
					$config['db_password']
				);
				self::$instance -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			} catch (PDOException $e) {
				die('Failed to connect to database. Check your settings.');
			}
		}
		
		return self::$instance;
	}

	public static function setDB(PDO $pdo){
		self::$instance = $pdo;
	}

	public static function __callStatic($method, $args) {
		return call_user_func_array(array(self::instance(), $method), $args);
	}
}