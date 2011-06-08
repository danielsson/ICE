<?php
//TODO: Make this less shitty.
define('SYSINIT',true);
require '../ice-config.php';
require '../lib/auth.class.php';
$Auth->init(2);
$images = Array();
foreach(glob('../media/*.*') as $v) {
	$e = explode('/',$v);
	$e[0] = '';
	$v = join('/',$e);
	$images[] =  array($v, $e[count($e)-1]);
}
?>
<h4>Images in media folder</h4>


<?php 
foreach($images as $i) { 
	$url = $config['baseurl'] . str_replace('/','',$config['sys_folder']) . $i[0];
	
	/*
	<div class="imgHolder">
		<img src="<?php echo $url; ?>"></img>
		<span><?php echo $i[1]; ?></span>
	</div>
	*/
	echo "<a class=\"iceImgLink\" onclick=\"iceEdit.saveImage('$url');\">$i[1]</a><br>";
}

?>
<input type="button" onclick="iceEdit.saveImage(prompt('Enter the url','http://www.example.com'));" value="Insert image by url"></input>
<input type="button" onclick="iceEdit.saveImage(null);" value="Cancel" style="top: 510px"></input>