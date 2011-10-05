<?php
include('ice/ice-adapter.php');
$icy->load("index", false);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ICE CMS demo.</title>
<link href="style.css" rel="stylesheet" type="text/css" />

<?php $icy->head(); ?>
</head>
<body>
<div class="header">
	<div class="center">
		<?php element('index_h1', 'h1','field'); ?>
		<?php element('index_page2_link', 'a','field', array('href'=>'page2.php', 'class'=>'menu')); ?>
    </div>
</div>
<div class="center">
	<div class="divider" id="scroller"></div>
    <img src="cityscape.jpg" alt="cityscape" />
    <div class="divider"></div>
    <div class="textBody">
    	<?php element('h2_h1', 'h2','field',array('class'=>'h1')); ?>
        <div class="divider"></div>
        <?php element('index_mainbody', 'div','area'); ?>
    </div>
    <div class="sidebar">
    	<?php element('imgtext', 'p', 'field', array('class'=>'imgtext'))?>
        
      	<?php element('about_us_title','h4','field', array('class'=>'f')); ?>
      	
        <?php element('about_us_text','div','area'); ?>
    </div>
    <div class="divider"></div>
    <?php element('mantra', 'div','field', array('class'=>'mantra')); ?>
    <div class="divider"></div>
    <?php
    	element('exp_left','div','area', array('class'=>'exp left', 'id'=>'exp_left'));
		element('exp_right','div','area', array('class'=>'exp right', 'id'=>'exp_right'));
	?>
    <div class="divider"></div>
</div>
<div class="footer">
</div>

<?php if(!$icy->is_editing()) : ?>
	<script type="text/javascript" src="ice/lib/jquery.js"></script>
	<script type="text/javascript" src="main.js"></script>
<?php endif; ?>
</body>
</html>