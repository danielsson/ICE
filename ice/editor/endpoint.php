<?php
$data = array(
	'status'=>'success',
	'error'=>'none'
);
define('SYSINIT', true);
require_once('../ice-config.php');
require_once('../lib/db.class.php');
require_once('../lib/auth.class.php');
require_once('../lib/image.class.php');

if($_SESSION['userlevel'] < 1) {
	$data['status'] = 'error';
	$data['error'] = 'auth';
	die(json_encode($data));
}
function clean($str) {
	return preg_replace('/[^A-Za-z0-9_]/','',$str);
}

$fieldname = clean($_POST['fieldname']);
$pagename = clean($_POST['pagename']);

if(isset($_POST['text'])){
	$content = $db->escape($_POST['text']);

	if(empty($pagename)) {
		$data['status'] = "error";
		$data['error'] = "empty pagename";
		die(json_encode($data));
	}
} elseif(isset($_POST['url'])) {
	$url = parse_url($_POST['url']);
	$w = intval($_POST['w']);
	$h = intval($_POST['h']);

	$img = new IceImage(realpath('../media/' . basename($url['path'])));
	$img->setCachePath(realpath('../cache/') . '/' . $w . 'x' . $h . basename($url['path']));
	$content = $config['sys_folder'] . 'cache/' . $w . 'x' . $h . basename($url['path']);

	$img->resizeToFit($w,$h);

	$img->cache();
	$data['url'] = $config['baseurl'] . $content;

	$content = $db->escape($content);
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