<?php

namespace Ice\Models;
use Ice\DB;
use \PDO;
use \Exception;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

/**
 * Abstract model superclass 
 * 
 * defines common methods for class creation
 * @uses DB 
 */

abstract class Model {
	protected $id;

	protected $newItem = true;

	public function getId() {
		return $this->id;
	}
	
	public static function byJSON($json) {
		return static::fromArray($json, true);
	}

	/* HELPERS */
	/**
	 * Creates a new instance from query result
	 *
	 * Helper class to create a single instance from a single result
	 * using the fromArray method.
	 *
	 * @param string $sql The sql to use
	 * @param array $params The params to insert into the sql.
	 * @return object|null Return an instance of the current class.
	 */

	protected static function querySingle($sql, $params) {
		$stmt = DB::prepare($sql);
		$stmt -> execute($params);

		$result = $stmt -> fetch(PDO::FETCH_ASSOC);

		if($result === false) {
			return null;
		} else {
			return static::fromArray($result);
		}
	}

	protected static function queryMultiple($sql, $params) {
		$stmt = DB::prepare($sql);

		$stmt -> execute($params);

		if ($stmt->rowCount() == 0) {
			return null;
		} else {
			$models = array();
			while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
				$models[] = static::fromArray($row);
			}
			return $models;
		}
	}

	public static function fromArray($arr, $new = false){
		return null;
	}
}