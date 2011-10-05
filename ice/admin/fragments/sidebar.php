<?php
	define('SYSINIT',true);
	require '../../lib/auth.class.php';
	$Auth->init(1);
	
	if(isset($_POST['clear']) && $_POST['clear'] == "true") {
		foreach(glob('../../cache/*.txt') as $v) {	unlink($v); }
		die("200");
	}
?>

<script type="text/javascript">
	function sidebar() {
		var aside = $('aside');
		aside.html(document.getElementById('sidebarContent').innerHTML);
		var $list = $('.biglist li', aside);
		$list.click(function() {
			var $this = $(this);
			switch($this.index()) {
				case 0:
					ice.fragment.load('pagemanager');
					break;
				case 1:
					ice.fragment.load('pagewizard');
					break;
				case 2:
					ice.fragment.load('usermanager');
					break;
			}
		});
		
		ice.Manager.windowSandbox.append(
			$('<div><img src="resources/no_windows.png" /></div>').css({
				position: "absolute",
				top:150,
				left:(ice.Manager.windowSandbox.width() - 169) / 2})
		);
	}
	
</script>

<script type="text/template" id="sidebarContent">
	
	<ul class="biglist">
		<li><h2>Edit Existing Pages</h2><p>and manage them</p></li>
		<li><h2>Create a New Page</h2><p>based on a template</p></li>
		<li><h2>Manage Users</h2><p>of your system</p></li>
	</ul>
</script>