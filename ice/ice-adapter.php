<?php

/**********************************************
This is the ICE! cms adapter. Include this file
on all pages with editable content.
**********************************************/
use Ice\DB;
defined('SYSINIT') or define('SYSINIT', true);
require_once('ice-config.php');
require_once('lib/DB.php');

//Output buffer callback
function iceOBcallback($d) {
	global $config;
	chdir(dirname($_SERVER['SCRIPT_FILENAME']));
	$c = explode("/", $_SERVER['PHP_SELF']);
	$cfile = $config['sys_folder']."cache/".crc32(ICE_CURRENT_PAGE).".txt";
	file_put_contents($cfile, $d);
	return $d;
}

class IceCms {
	public $in_editor_mode = false;
	private $currentPage = "";
	private $createStmt = null;
	private $pageContent = array();
	
	public function load($page_name, $cache = 'n', $lifetime = 0) {
		global $config;
		if($cache==='n') { $cache = (boolean) $config['use_cache']; }
		if(defined('ICE_PAGE_OVERRIDE') === true) {
			$this->currentPage = ICE_PAGE_OVERRIDE;
		} else {
			$this->currentPage = $this->sanitize($page_name);
		}
		define('ICE_CURRENT_PAGE', $this->currentPage);
		//Cache
		if($cache==true && $this->in_editor_mode!=true) {
			$cfile = $config['sys_folder']."cache/".crc32(ICE_CURRENT_PAGE).".txt";
			if(file_exists($cfile) && time() - filemtime($cfile) < $lifetime*60) {
				readfile($cfile);
				die();
			} else {
				ob_start("iceOBcallback");	
			}
		}
		$this->loadPageData();
	}

	public function head($include_jquery = true) {
		return 0;
	}
	
	public function e($field_name, $element, $type = "field", $attrs = array()) {  //Inserts an element into the page. Equal to element().
		global $config;
		switch($type) {
			case "field":
				$type = "field"; break;
			case "area":
				$type = "area"; break;
			default:
				echo "Error: Invalid type.";
				return false;
				break;
		}
		$field_name = $this->sanitize($field_name);
		echo '<', $element, ' ';
		if(!empty($attrs)) {
			foreach($attrs as $key => $val) {
				echo "$key=\"$val\" ";
			}
		}
		echo ">";
		if(!isset($this->pageContent[$field_name])) {
			$this->createDBrecord($field_name, $type, "Empty element");
		}
		echo $this->pageContent[$field_name];
		
		echo "</", $element, ">";
	}
	
	public function img($field_name, $width=0, $height=0, $attrs = array()) {
		global $config;

		if($height != 0){
			$attrs['height'] = $height;
		}
		if($width != 0){
			$attrs['width'] = $width;
		}
		$field_name = $this->sanitize($field_name);

		if(!isset($this->pageContent[$field_name])) {
			$this->createDBrecord($field_name, 'img', '//placehold.it/' . $width . "x$height");
		}

		$attrs['src'] = $this->pageContent[$field_name];

		echo '<img ';
		foreach($attrs as $key => $val) {
			echo "$key=\"$val\" ";
		}
		echo '/>';


	}
	public function createDBrecord($field_name, $type, $placeholder) {
		global $config;


		if($config['dev_mode']==false) {
			echo 'Dev-mode off -- Record creation failed';
			return false;
		}

		$params = array(
			':fieldname' => $field_name,
			':placeholder' => $placeholder,
			':cp' => $this->currentPage,
			':type' => $type
		);

		if ($this->createStmt === null) {
			$sql = "INSERT IGNORE INTO {$config['content_table']} (fieldname, content, pagename, fieldtype) VALUES (:fieldname, :placeholder, :cp, :type);";
			$this->createStmt = DB::prepare($sql);
		}
		
		$r = $this->createStmt->execute($params);
		if(!$r) {
			echo 'Database Error ';
		}
		$this->pageContent[$field_name] = $placeholder;
		return true;
	}
	
	public function loadPageData() {
		//Create the array of page fields
		global $config;
		$sql = "SELECT content, fieldname FROM {$config['content_table']} WHERE pagename = ?";
		
		$stmt = DB::prepare($sql);
		

		if($stmt->execute(array($this->currentPage))) {
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$this->pageContent[$row['fieldname']] = $row['content'];
			}
		}
	}
	
	public function is_editing() {
		return $this->in_editor_mode;	
	}
	public function sanitize($str) {
		$reg = '/[^A-Za-z0-9_]/';
		return preg_replace($reg,'',$str);
	}
	
}

if(isset($_POST['edit']) && $_POST['edit']=="true") {
	require_once('editor/editor.php');
	$ice = new Ice\ICECMSEDIT();
	$ice->in_editor_mode = true;
} else {
	$ice = new IceCms();
}

//Shorthand functions below. Enable/disable in config.php

if($config['use_shorthand']===true) {
	function element($field_name, $element, $type = "field", $attrs = array()) {
		global $ice;
		$ice->e($field_name, $element, $type, $attrs);
	}
	function image($field_name, $width=0, $height=0, $attrs = array()) {
		global $ice;
		$ice->img($field_name, $width, $height, $attrs);
	}
}

?>