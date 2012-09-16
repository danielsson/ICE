<?php

namespace Ice\Models;
use Ice\DB;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once 'Model.php';
require_once 'ContentSet.php';
require_once 'File.php';
require_once __DIR__ . '/../lib/DB.php';

/**
 * Straight-forward class representation of a page.
 *
 */

class Page extends Model
{
    protected $id;
    protected $name;
    protected $tid = 0;
    protected $url;

    public function __construct($id, $name, $tid, $url, $new = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->tid = $tid;
        $this->url = $url;
        $this->newItem = $new;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($n)
    {
        $this->name = $n;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTid()
    {
        return $this->tid;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /* FINDERS */
    public static function byId($id)
    {
        $sql = "SELECT id, name, tid, url FROM ice_pages WHERE id=? LIMIT 1";

        return static::querySingle($sql, array((int) $id));
    }

    public static function byPageName($name)
    {
        $sql = "SELECT id, name, tid, url FROM ice_pages WHERE name=? LIMIT 1";

        return static::querySingle($sql, array($name));
    }

    public static function findAll()
    {
        $sql = "SELECT `id`, `name`, `tid`, `url` FROM ice_pages WHERE 1";

        return static::queryMultiple($sql, null);
    }

    /* HELPERS */

    public static function fromArray($arr,$new=false)
    {
        return new static(
            $arr['id'],
            $arr['name'],
            $arr['tid'],
            $arr['url'],
            $new
        );
    }

    /* METHODS */

    public function getContentSet()
    {
        return ContentSet::byPageName($this->name);
    }

    public function getFile()
    {
        return File::byId($this->tid);
    }

    public function delete()
    {
        $stmt = DB::prepare("DELETE FROM ice_pages WHERE id = ?;");

        return $stmt->execute(array($this->id));
    }

    public function save()
    {
        $params = array(
            ':name'	=> $this->name,
            ':tid'	=> $this->tid,
            ':url'	=> $this->url
        );
        if ($this->newItem) {
            $sql = "INSERT INTO ice_pages (name,tid,url) VALUES
            (:name, :tid, :url);";
        } else {
            $sql = "UPDATE ice_pages SET name = :name, tid = :tid, url = :url
            WHERE id = :id;";
            $params[':id'] = $this->id;
        }

        $stmt = DB::prepare($sql);

        return $stmt->execute($params);
    }
}
