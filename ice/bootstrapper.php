<?php
define('SYSINIT', true);
require_once('ice-config.php');
require_once('lib/db.class.php');
$url = parse_url($config['baseurl'] . $_REQUEST['path']);
$url = addslashes($url['path']);
$sql = "SELECT ice_pages.id, ice_files.path FROM ice_pages INNER JOIN ice_files ON ice_pages.tid=ice_files.id WHERE ice_pages.url = '$url' LIMIT 1;";

$db->connect();
$res = $db->query($sql);
if(!$res) {
	if($config['dev_mode']) {echo $db->error();}
	else {echo 'DB error';}
} else {
	while ($row = mysql_fetch_array($res)) {
		define('ICE_PAGE_OVERRIDE', 'dyn_' . $row['id']);
		require($_SERVER['DOCUMENT_ROOT'] . stripslashes($row['path']));
		exit(0);
	}
	if(!defined(ICE_PAGE_OVERRIDE)) {
		require('404.php');
	}
}
?>