<?php 
include_once('ice/ice-adapter.php');
$icy->load('index', true, 1); // Yes, Im using the same name. This way i can use elements twice. Like the title.
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Icy CMS demo.</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<?php $icy->head(); ?>
</head>
<body>
<div class="header">
	<div class="center">
		<a href="./">
		<?php element('index_h1', 'h1','field'); ?>
		</a>
		<?php element('index_page2_link', 'a','field', array('href'=>'page2.php', 'class'=>'menu')); ?>
    </div>
</div>
<div class="divider"></div>
<?php  element('bigTextBody2','div','area',array('class'=>'center textBlock')); ?>
<div class="center">

</div>
<div class="footer">
</div>
</body>
</html>