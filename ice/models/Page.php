<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once 'IceModel.php';
require_once 'ContentSet.php';

class Page extends IceModel {

	private $id;
	private $name;
	private $tid = 0;
	private $url;

	public function __construct($id, $name, $tid, $url) {
		$this->id = $id;
		$this->name = $name;
		$this->tid = $tid;
		$this->url = $url;
	}

	public function getName() {
		return $this->name;
	}

	public function getId() {
		return $this->id;
	}

	public function getTid() {
		return $this->tid;
	}

	public function getUrl() {
		return $this->url;
	}

	/* FINDERS */
	public static function byId($db, $id) {
		$id = (int) $id;
		$sql = "SELECT id, name, tid, url FROM 'ice_pages' WHERE id='$id' LIMIT 1";
		return static::querySingle($db,$sql);
	}

	public static function byPageName($db, $name) {
		$sql = "SELECT id, name, tid, url FROM 'ice_pages' WHERE name='". $db->escape($name) ."' LIMIT 1";
		return static::querySingle($db,$sql);
	}

	public static function findAll() {
		$sql = "SELECT id, name, tid, url FROM 'ice_pages' WHERE 1";
		return static::queryMultiple($db,$sql);
	}

	/* HELPERS */

	private static function fromArray($arr) {
		return static(
			$arr['id'],
			$arr['name'],
			$arr['tid'],
			$arr['url']
		);
	}

	/* METHODS */

	public function getContentSet($db) {
		return ContentSet::byPageName($db, $this->name);
	}
}