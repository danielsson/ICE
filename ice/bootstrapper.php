<?php
//TODO: This is a mess

namespace Ice;
use \PDO;

define('SYSINIT', true);

require_once('ice-config.php');
require_once('lib/DB.php');

$url = parse_url($config['baseurl'] . $_REQUEST['path']);
$url = mysql_real_escape_string($url['path']);
$sql = "SELECT ice_pages.name, ice_files.path FROM ice_pages INNER JOIN ice_files ON ice_pages.tid=ice_files.id WHERE ice_pages.url = '$url' LIMIT 1;";

$res = DB::query($sql);
if(!$res) {
	if($config['dev_mode']) {echo DB::textError();}
	else {echo 'DB error';}
} else {
	$row = $res->fetch(PDO::FETCH_ASSOC);
	if(!empty($row['name'])) {
		define('ICE_PAGE_OVERRIDE', $row['name']);
		require($_SERVER['DOCUMENT_ROOT'] . $row['path']);
	} else {
		require('404.php');
	}
	
}
?>