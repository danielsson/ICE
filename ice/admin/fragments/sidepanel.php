<?php
namespace Ice;

define('SYSINIT', true);
require '../../lib/Auth.php';
Auth::init(1);

?>

<script>
	function sidepanel() {
		var $aside = $('aside');
		$aside.html(document.getElementById("sidepaneltemplate").innerHTML);
		
		$aside.children().eq(0).children().click(function() {
			var $this = $(this), index = $this.index();
			switch(index) {
				case 0: // Edit
					ice.fragment.load('pagemanager');
					break;
				case 1: // create
					ice.fragment.load('pagewizard');
					break;
				case 2: // Media
					ice.fragment.load('mediamanager');
					break;
				case 3: // User
					ice.fragment.load('usermanager');
					break;
				case 4: //advanced
					$aside.children().eq(1).fadeToggle();
					break;
			}
		})
	}
</script>

<script type="text/x-template" id="sidepaneltemplate">
	<ul class="big_grid" id="main_menu">
		<li style="width: 194px"></li>
		<li style="background-position: 0 -98px" ></li>
		<li style="background-position: -98px -98px" class="end"></li>
		<li style="background-position: 0 -196px" ></li>
		<li style="background-position: -98px -196px" class="end"></li>
	</ul>
	<table style="display:none; background:#EEF">
		<tbody>
			<tr>
				<td><a onclick="ice.fragment.load('testing')"><b>Start testing</b></a></td>
			</tr>
			<tr>
				<td><a onclick="ice.fragment.load('templatemanager')"><b>Add files</b></a></td>
			</tr>
		</tbody>
	</table>
</script>