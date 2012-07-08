<?php
/**
 * Simple PDO helper
 *
 */

namespace Ice;

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
					$config['DB_CONNECTION'],
					$config['DB_USER'],
					$config['DB_PASS']
				);
				self::$instance -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			} catch (PDOException $e) {
				die('Failed to connect to database. Check your settings.');
			}
		}
		
		return self::$instance;
	}

	public static function __callStatic($method, $args) {
		return call_user_func_array(array(self::instance(), $method), $args);
	}
}