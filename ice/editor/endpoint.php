<?php
$data = array(
	'status'=>'success',
	'error'=>'none'
);
define('SYSINIT', true);
require_once('../ice-config.php');
require_once('../lib/db.class.php');
require_once('../lib/auth.class.php');
if($_SESSION['userlevel'] < 1) {
	$data['status'] = 'error';
	$data['error'] = "auth";
	die(json_encode($data));
}
function clean($str) {
	return preg_replace('/[^A-Za-z0-9_]/','',$str);
}
$fieldname = clean($_POST['fieldname']);
$pagename = clean($_POST['pagename']);
$content = $db->escape($_POST['text']);

if(empty($pagename)) {
	$data['status'] = "error";
	$data['error'] = "empty pagename";
	die(json_encode($data));
}


$sql = 'UPDATE '. $config['content_table'] ." SET content = '$content' WHERE fieldname = '$fieldname' and pagename = '$pagename';"; 
$db->connect();
$res = $db->query($sql);
if(!$res) {
	$data['status'] = 'error';
	if($config['dev_mode']==true) {
		$data['error'] = $db->error() . "::" . $sql;
	} else {
		$data['error'] = "DB Error: no data was saved";
	}
}

echo json_encode($data);

// Clear the cache
foreach(glob('../cache/*.txt') as $v) {	unlink($v); }
$db->close();
?>