<?php
define('SYSINIT', true);
require '../../lib/auth.class.php';
$Auth -> init(1);

if (isset($_POST['clear']) && $_POST['clear'] == "true") {
	foreach (glob('../../cache/*.txt') as $v) {	unlink($v);
	}
	die("200");
}
?>

<script type="text/javascript">
	function sidepanel() {
		var aside = $('aside');
		aside.html(document.getElementById('sidebarContent').innerHTML);
		var $list = $('.biglist li', aside);
		$list.click(function(e) {
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
				case 3:
					$this.append($('#advancedTools').html());
					$this.unbind(e);
					break;
			}
		});

		ice.Manager.windowSandbox.append($('<div><img src="resources/no_windows.png" /></div>').css({
			position : "absolute",
			top : 150,
			left : (ice.Manager.windowSandbox.width() - 169) / 2
		}));

	}
</script>
<script type="text/template" id="sidebarContent">
	<ul class="biglist">
	<li style="background: url(resources/icons.png) 0 0 no-repeat;">
		<h2>Edit Existing Pages</h2><p>and manage them</p>
	</li><li style="background: url(resources/icons.png) 0 -60px no-repeat;">
		<h2>Create a New Page</h2><p>based on a template</p>
	</li><li style="background: url(resources/icons.png) 0 -120px no-repeat;">
		<h2>Manage Users</h2><p>of your system</p>
	</li><li style="background: url(resources/icons.png) 0 -180px no-repeat;">
			<h2>Advanced tools</h2><p>for advanced users</p>
	</li>
	</ul>
</script>
<script type="text/template" id="advancedTools">
	<br />
	<ul class="nicelist">
		<li onclick="ice.fragment.load('templatemanager')">Add files</li>
		<li onclick="$.post('fragments/sidepanel.php', {clear : 'true'}); ice.message('Cache cleared', 'info');">Clear Cache</li>
		<?php if($_SESSION['userlevel'] > 1) { ?>
		<li onclick="ice.fragment.load('and')">Set options</li>
		<?php } if($_SESSION['userlevel'] > 2) { ?>
		<li onclick="ice.fragment.load('and')">Edit Icy Config</li>
		<?php } ?>
	</ul>
	<br />
</script>