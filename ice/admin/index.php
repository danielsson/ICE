<?php
	define('SYSINIT', true);
	require_once ('../ice-config.php');
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=encoding">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<title>Administer Icy-cms PRE-ALPHA</title>
<script type="text/javascript" src="../lib/jquery.js"></script>
<script type="text/javascript" src="../lib/jquery_ui_custom.js"></script>
<script type="text/javascript" src="admin.js"></script>
<link href="admin.css" rel="stylesheet" type="text/css" />

</head>
<body>
	<div id="header">
	<div class="center">
		<div id="logo">
			<a href="./">
				<img alt="" src="resources/ice-logo_05.png" />
			</a>
		</div>
		<div id="headerText">
		<?php if($_SESSION['userlevel'] > 0) { ?>
		<a href="#" onclick="ice.logout();"><b>Log out</b></a>
		<?php } else {?>
			Not logged in.
		<?php }?>
		</div>
	</div>
	</div>
	<aside>
		
	</aside>
	<div id="messageField" class="center">
		
	</div>
	<div id="windowSandbox"></div>
	<div id="taskBar" >
		<div id="dragBox">
		<ul>
		</ul>
		</div>
	</div>
<?php if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && $config['use_gcf'] === true) : ?>
<!--[if IE]>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
	<script>
		CFInstall.check({
			mode: "overlay"
		});
	</script>
<![endif]-->
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function() {
	ice.Manager.ready();

<?php if($_SESSION['userlevel'] == 0) {?>
	ice.fragment.load('login');

<?php } else { ?>
	ice.fragment.load('sidebar');
	(function () {
		var c = $('#header .center'), h = $('#header');
		c.css({marginTop:0});
		h.css({height: 48, zIndex:1});
	})()

<?php } ?>
	if($.browser.msie) {
		ice.message("You are running Internet Explorer. Upgrading to a better browser will improve your experience.", 'info');
	}
});
</script>

</body>
</html>