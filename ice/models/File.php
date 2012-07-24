<?php

namespace Ice\Models;
use Ice\DB;

require_once 'Model.php';
require_once 'Page.php';
require_once __DIR__ . '/../lib/DB.php';

class File extends Model {
	private $name;
	private $path;
	private $url;

	public function __construct($id, $name, $path, $url, $new = true) {
		$this->id = $id;
		$this->name = $name;
		$this->path = $path;
		$this->url = $url;

		$this->newItem = $new;
	}

	/* GET & SET */
	public function getName() {return $this->name;}
	public function setName($n) {$this->name = $n;}

	public function getPath() {return $this->path;}
	public function setPath($p) {$this->path = $p;}

	public function getUrl() {return $this->url;}
	public function setUrl($u) {$this->url = $u;}

	/* FINDERS */
	public static function byId($id) {
		$sql = "SELECT * FROM ice_files WHERE id = ?";
		return static::querySingle($sql, array((int) $id));
	}
	
	public static function byName($name) {
		$sql = "SELECT * FROM ice_files WHERE name = ?";
		return static::querySingle($sql, array($name));
	}

	public static function findAll() {
		$sql = "SELECT * FROM ice_files";
		return static::queryMultiple($sql, null);
	}

	public static function fromArray($arr, $new = false) {
		return new self(
			$arr['id'],
			$arr['name'],
			$arr['path'],
			$arr['url'],
			$new);
	}

	public function save() {
		$params = array(
			":name" => $this->name,
			":path" => $this->path,
			":url"	=> $this->url
		);
		if($this->newItem) {
			$sql = "INSERT INTO ice_files(name,path,url) VALUES
			(:name,:path,:url)";
		} else {
			$sql = "UPDATE ice_files
			SET name = :name, path = :path, url = :url
			WHERE id = :id";
			$params[':id'] = $this->id;
		}

		$stmt = DB::prepare($sql);
		if($stmt->execute($params)) {
			if ($this->newItem) {
				$this->id = DB::lastInsertId();
				$this->newItem = false;
			}
		} else {
			throw new Exception("SQL error: " . DB::errorInfo() . $sql, 1);
		}

		//Create the static record
		$firstpage = new Page(0, $this->name, $this->id, $this->url);
		$firstpage->save();
	}

	public function delete() {
		return DB::exec(sprintf('DELETE FROM ice_files WHERE id = %d LIMIT 1', $this->id));
	}
}