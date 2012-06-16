<?php

namespace Ice\Models;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

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
	protected static function querySingle($db, $sql){
		$res = $db->query($sql);

		if(!$res) {
			throw new Exception("sql yielded no results: " . $sql . $db->error(), 1);
			return NULL;
		} else {
			$usr = mysql_fetch_array($res);
			
			return static::fromArray($usr);
		}
	}

	protected static function queryMultiple($db, $sql) {
		$res = $db->query($sql);
		if(!$res) {
			return NULL;
		} else {
			$ret = array();
			while($row = mysql_fetch_array($res)) {
				$ret[] = static::fromArray($row);
			}
			return $ret;

		}
	}

	public static function fromArray($arr, $new=false) {
		return NULL;
	}
}