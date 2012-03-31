<?php
/**********************************************
This is the ICE! cms adapter. Include this file
on all pages with editable content.
**********************************************/
define('SYSINIT', true);
require_once('ice-config.php');
require_once('lib/db.class.php');

//Globals
$pageContent = array();

//Output buffer callback
function iceOBcallback($d) {
	global $config;
	chdir(dirname($_SERVER['SCRIPT_FILENAME']));
	$c = explode("/", $_SERVER['PHP_SELF']);
	$cfile = $config['sys_folder']."cache/".crc32(ICE_CURRENT_PAGE).".txt";
	file_put_contents($cfile, $d);
	return $d;
}

class ICECMS {
	public $in_editor_mode = false;
	private $currentPage = "";
	
	public function load($page_name, $cache = 'n', $lifetime = 0) {
		global $config, $db, $pageContent;
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
				echo file_get_contents($cfile);
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
		global $config, $pageContent;
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
		if(is_array($attrs) && count($attrs) > 0) {
			foreach($attrs as $key => $val) {
				echo "$key=\"$val\" ";
			}
		}
		echo ">";
		if(!isset($pageContent[$field_name])) {
			$this->createDBrecord($field_name, $type);
		} else {
			echo $pageContent[$field_name];
		}
		echo "</", $element, ">";
	}
	
	public function img($field_name, $height=0, $width=0, $attrs = array()) {
		
	}
	public function createDBrecord($field_name, $type) {
		global $config, $db;
		$cp = $this->currentPage;
		if($config['dev_mode']==false) {
			echo 'Dev-mode off -- Record creation failed';
			return false;
		}
		$sql = 'INSERT IGNORE INTO ' . $config['content_table'] . " (fieldname, content, pagename, fieldtype) VALUES ('$field_name', 'Empty element', '$cp', '$type');";
		$db->connect();
		$r = $db->query($sql);
		if(!$r) {
			echo 'Database Error';
		}
		echo "Empty element";
		return true;
	}
	
	public function loadPageData() {
		//Create the array of page fields
		global $db, $config, $pageContent;
		$sql = "SELECT content, fieldname FROM ". $config['content_table'] ." WHERE pagename = '$this->currentPage'";
		$db->connect();
		$res = $db->query($sql);
		
		if($res) {
			while($row = mysql_fetch_array($res)) {
				$pageContent[$row['fieldname']] = $row['content'];
			}
		}
		$db->close();
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
	$ice = new ICECMSEDIT();
	$ice->in_editor_mode = true;
} else {
	$ice = new ICECMS();
}

//Shorthand functions below. Enable/disable in config.php

if($config['use_shorthand']===true) {
	function element($field_name, $element, $type = "field", $attrs = array()) {
		global $ice;
		$ice->e($field_name, $element, $type, $attrs);
	}
}

?>