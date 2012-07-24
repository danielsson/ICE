<?php
$data = array(
	'status'=>'success',
	'error'=>'none'
);
define('SYSINIT', true);
require_once('../ice-config.php');
require_once('../lib/DB.php');
require_once('../lib/Auth.php');
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
	$content = DB::quote($_POST['text']);

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
	
	if($img->getHeight() != $h || $img->getWidth() != $w) {
		$img->setCachePath(realpath('../cache/') . '/' . $w . 'x' . $h . basename($url['path']));
		$content = $config['sys_folder'] . 'cache/' . $w . 'x' . $h . basename($url['path']);

		$img->resizeToFit($w,$h);

		$img->cache();
	} else {
		$content = $config['sys_folder'] . 'media/' . basename($url['path']);
	}
	$data['url'] = $config['baseurl'] . $content;

	$content = DB::quote($content);
}

	$sql = 'UPDATE '. $config['content_table'] ." SET content = '$content' WHERE fieldname = '$fieldname' and pagename = '$pagename';"; 

	if(!$res = DB::query($sql)) {
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
?>