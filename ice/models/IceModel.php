<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

abstract class IceModel {
	private $id;

	private $newItem = true;

	public getId() {
		return $this->id;
	}
	
	public static function byJSON($json) {
		return static::fromArray($json);
	}

	/* HELPERS */
	private static function querySingle($db, $sql){
		$res = $db->query($sql);

		if(!$res) {
			return NULL;
		} else {
			$usr = mysql_fetch_array($res);
			
			return static::fromArray($usr);
		}
	}

	private static function queryMultiple($db, $sql) {
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

	static function fromArray($arr) {
		return NULL;
	}
}