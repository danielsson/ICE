<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

namespace Ice\Models;

class ContentSet {
	private $content;
	private $delta = array();

	public function __construct($arr) {
		$this->content = $arr;
	}

	public function get($key) {
		return $content[$key];
	}

	public function set($key,$val) {
		// Readonly for now
		return NULL;
		$content[$key] = $val;
		$delta[$key] = $val;
	}

	/* FINDERS */
	public static function byPageName($db, $name) {
		global $config;
		$sql = "SELECT content, fieldname FROM ". $config['content_table'] ." WHERE pagename = '$name'";
		$res = $db->query($sql);
		$ret = array();

		if($res) {
			while($row = mysql_fetch_array($res)) {
				$ret[$row['fieldname']] = $row['content'];
			}
		} else {
			return NULL;
		}
		return new static($ret);
	}

}