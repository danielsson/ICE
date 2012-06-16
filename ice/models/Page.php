<?php defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

namespace Ice\Models;

require_once 'Model.php';
require_once 'ContentSet.php';

class Page extends Model {

	protected $id;
	protected $name;
	protected $tid = 0;
	protected $url;

	public function __construct($id, $name, $tid, $url, $new = true) {
		$this->id = $id;
		$this->name = $name;
		$this->tid = $tid;
		$this->url = $url;
		$this->newItem = $new;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($n) {
		$this->name = $n;
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
		$sql = "SELECT id, name, tid, url FROM ice_pages WHERE id='$id' LIMIT 1";
		return static::querySingle($db,$sql);
	}

	public static function byPageName($db, $name) {
		$sql = "SELECT id, name, tid, url FROM ice_pages WHERE name='". $db->escape($name) ."' LIMIT 1";
		return static::querySingle($db,$sql);
	}

	public static function findAll($db) {
		$sql = "SELECT `id`, `name`, `tid`, `url` FROM ice_pages WHERE 1";
		return static::queryMultiple($db,$sql);
	}

	/* HELPERS */

	public static function fromArray($arr,$new=false) {
		return new static(
			$arr['id'],
			$arr['name'],
			$arr['tid'],
			$arr['url'],
			$new
		);
	}

	/* METHODS */

	public function getContentSet($db) {
		return ContentSet::byPageName($db, $this->name);
	}

	public function delete($db) {
		return $db->query("DELETE FROM ice_pages WHERE id = '" . $this->id . "';");
	}

	public function save($db) {
		if($this->newItem){
			$sql = "INSERT INTO ice_pages (name,tid,url) VALUES 
			('{$this->name}','{$this->tid}','{$this->url}');";
		} else {
			$sql = "UPDATE ice_pages SET name = '{$this->name}', tid = '{$this->tid}', url = '{$this->url}'
			WHERE id='{$this->id}';";
		}

		$db->query($sql);
	}
}