<?php
	define('SYSINIT',true);
	require '../../lib/auth.class.php';
	$Auth->init(1);
	
	if($_POST['clear']=="true") {
		foreach(glob('../../cache/*.txt') as $v) {	unlink($v); }
		die("200");
	}
?>

<script type="text/javascript">
	function dashboard() {
		var dWin = new ice.Window;
		dWin.name = "dashboardWin";
		dWin.title = "Dashboard";
		dWin.width = 440;
		dWin.closeable = false;
		dWin.onOpen = function(win) {
			var $t = win.contentBox;
			$t.find('a').click(function(e) {
				e.preventDefault();
				var g = new ice.Window;
				g.name = "AdvTools";
				g.title = "Toolbox";
				g.setContent($('#advancedTools').html());
				g.width = "auto";
				g.contentBox
					.css({background:'#333', color: '#FFF'})
					.prev()
					.css({background:'#333'});
				g.clearCache = function() {
					$.post('fragments/dashboard.php', {clear: "true"});
					ice.message('Cache cleared', 'info');
				};
				if(ice.Manager.addWindow(g)) {
					ice.Manager.getWindow('AdvTools').element.css({left:0, top:100});
				}
			});
			$('#editPagesBtn', $t).click(function() { ice.fragment.load('pagemanager'); });
			$('#createPageBtn', $t).click(function() { ice.fragment.load('pagewizard'); });
			$('#manageUsersBtn', $t).click(function() { ice.fragment.load('usermanager'); });
		};
		dWin.setContent(document.getElementById('dashboardWindow').innerHTML);
		ice.Manager.addWindow(dWin);
	}
</script>

<script type="text/template" id="dashboardWindow">
<div class="winpadd">
	<div class="block190" style="float:left;">
		<div id="editPagesBtn" class="expBtn rounded6">
			<p class="big">Edit Existing Pages</p>
			<p class="small">and manage them</p>
		</div>
		<div id="createPageBtn" class="expBtn rounded6">
			<p class="big">Create a New Page</p>
			<p class="small">based on a template</p>
		</div>
		<div id="manageUsersBtn" class="expBtn rounded6">
			<p class="big">Manage Users</p>
			<p class="small">of your system</p>
		</div>
		<a href="#" >Advanced Tools</a>
	</div>
	<div class="block190 rounded6" style="float:right;">
	<div style="background: #333; color:#FFF; padding: 10px 0;" class="rounded6">
		<b style="text-align: center; display:block">Recent</b>
		<br />
		<ul class="nicelist">
			<li>Index</li>
			<li>Username changed</li>
		</ul>
	</div>
	</div>
<br style="clear:both; height:0;" />
</div>
</script>

<script type="text/template" id="advancedTools">
	<ul class="nicelist">
		<li onclick="ice.fragment.load('templatemanager')">Add files</li>
		<li onclick="ice.Manager.getWindow($(this).inWindow()).clearCache()">Clear Cache</li>
		<?php if($_SESSION['userlevel'] > 1) { ?>
		<li onclick="ice.fragment.load('and')">Set options</li>
		<?php } if($_SESSION['userlevel'] > 2) { ?>
		<li onclick="ice.fragment.load('and')">Edit Icy Config</li>
		<?php } ?>
	</ul>
	<br />
</script>