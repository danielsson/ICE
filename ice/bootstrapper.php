<?php
define('SYSINIT', true);
require_once('ice-config.php');
require_once('lib/db.class.php');
$url = parse_url($config['baseurl'] . $_REQUEST['path']);
$url = mysql_real_escape_string($url['path']);
$sql = "SELECT ice_pages.name, ice_files.path FROM ice_pages INNER JOIN ice_files ON ice_pages.tid=ice_files.id WHERE ice_pages.url = '$url' LIMIT 1;";

$db->connect();
$res = $db->query($sql);
if(!$res) {
	if($config['dev_mode']) {echo $db->error();}
	else {echo 'DB error';}
} else {
	$row = mysql_fetch_array($res);
	if(!empty($row['name'])) {
		define('ICE_PAGE_OVERRIDE', $row['name']);
		require($_SERVER['DOCUMENT_ROOT'] . $row['path']);
	} else {
		require('404.php');
	}
	
}
?>