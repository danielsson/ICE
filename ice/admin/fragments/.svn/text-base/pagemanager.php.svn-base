<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(1);
	$db->connect();
	if($_POST['del'] == "true") {
		if($_SESSION['userlevel'] < 2) {
			die('You are not allowed to perform this action');
		}
		$db->query("DELETE FROM ice_pages WHERE id = '" . intval($_POST['id']) . "';");
		$pagename = 'dyn_' . intval($_POST['id']);
		$db->query("DELETE FROM ice_content WHERE pagename = '$pagename';");
		die('true');
	} elseif ($_POST['rename'] == "true") {
		if($_SESSION['userlevel'] < 2) {
			die('You are not allowed to perform this action');
		}
		$name = mysql_real_escape_string($_POST['name']);
		$id = intval($_POST['id']);
		$db->query("UPDATE ice_pages SET name = '$name' WHERE id = '$id';");
		die('true');
	}
?>
<script type="text/javascript">
	function pagemanager() {
		ice.fragment.addCss('pagemanager.css');
		var W = new ice.Window;
		W.name = "IcyPM";
		W.title = "Manage pages";
		W.width = 800;
		W.beforeClose = function(win) {
			$('#pageManagerMenu').attr('current', 'none').fadeOut();
		};
		W.setContent(document.getElementById('pageManager1').innerHTML);
		
		$('.pageBtn', W.contentBox).bind("contextmenu", function() {
			var $this = $(this), off = $this.offset(), pm = $('#pageManagerMenu');
			pm
				.attr({
					'data-url': $this.attr('data-page-trac'),
					'data-id': $this.attr('data-page-id')
					})
				.css({left: off.left - ((pm.width() - 75) / 2), top: off.top, display:'none'})
				.fadeIn()
				.children('b')
				.text($this.children().text());
			return false; //Prevent the usual context menu from opening
		}).click(function() {
			$('#pageManagerMenu')
				.attr('data-current', $(this).attr('data-page-trac'))
				.find('li:eq(0)')
				.trigger('click');
		});
		
		$('#pageManagerMenu li').click(function() {
			var $this = $(this), current = $this.parent().parent().attr('data-current'),
			id = $this.parent().parent().attr('data-id');
			
			switch($this.attr('data-cmd')) {
				case 'editN':
					var dm = $('<form>').attr({
						id: "formPostShiv",
						action: current,
						method: "post",
						style: "display:none;",
						target: "_blank"
					});
					$('<input type="text" name="edit" value="true">').appendTo(dm);
					dm.appendTo('body');
					document.getElementById('formPostShiv').submit();
					$('#formPostShiv').remove();
					$('#pageManagerMenu').attr('current', 'none').fadeOut();
					break;
				case 'edit':
					ice.fragment.load('browser',{}, {url: current, postEdit: true});
					$('#pageManagerMenu').attr('current', 'none').fadeOut();
					break;
				case 'rename':
					r = prompt('Please enter the new name');
					if(r != null && r !="") {
						$.post('fragments/pagemanager.php', {rename:true, id: id, name: r}, function(data) {
							if(data=="true") {
								$w = ice.Manager.getWindow('IcyPM');
								$('.pageBtn[data-page-id="' + id + '"] span').text(r);
								ice.message('Name changed', 'info');
							} else {
								ice.message(data, 'warning');
							}
						});
					}
					$('#pageManagerMenu').attr('current', 'none').fadeOut();
					break;
				case 'del':
						
					if(confirm('This action will delete the page and all accociated data. Continue?')) {
						$.post('fragments/pagemanager.php', {del:true, id: id}, function(data) {
							if(data == 'true') {
								$w = ice.Manager.getWindow('IcyPM');
								$('.pageBtn[data-page-id="' + id + '"]').remove();
							} else {
								ice.message(data, 'warning');
							}
						});
					}
					$('#pageManagerMenu').attr('current', 'none').fadeOut();
					break;
				case 'close':
					$('#pageManagerMenu').attr('current', 'none').fadeOut();
					break;
			}
		});
		
		ice.Manager.addWindow(W);
	}
</script>

<script type="text/template" id="pageManager1">
<div class="pagemanager">
	<div class="toolbar">
		Click on a page to edit, right click for options.
		<a href="#" style="float:right;" onclick="ice.fragment.load('pagewizard');">Create new page</a>
	</div>
<br />
	<div style="clear:both;"></div>
	<div class="pagesList rounded6">
		<?php
			$sql = "SELECT name, url, id FROM ice_pages";
			$res = $db->query($sql);
			if($res) {
				while($row = mysql_fetch_array($res)) {
					echo '<div class="pageBtn" data-page-trac="', $row['url'], '" data-page-id="',$row['id'] , '" ><span>', stripslashes($row['name']), '</span></div>';
				}
			} else {
				echo $db->error();
			}
			$db->close();
		?>
		<div style="clear:both;"></div>
	</div>
</div>
</script>
<div id="pageManagerMenu" class="shadow rounded6" style="display: none;">
	<br />
	<b></b>
	<br />
	<ul class="nicelist">
		<li data-cmd="editN">Edit</li>
		<li data-cmd="edit">|_ in a window</li>
		<li data-cmd="rename">Rename</li>
		<li data-cmd="del">Delete</li>
		<li data-cmd="close">Close this</li>
	</ul>
	<br />
</div>
