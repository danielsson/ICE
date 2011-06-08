<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(2);
	$db->connect();
	if(!empty($_POST['nicename'])) {
		$_POST = $Auth->sanitize($_POST);
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['path'])) {
			die('Incorrect path: File not found.' . $_POST['path']);
			// TODO: THIS IS NOT WORKING
		}
		if(!empty($_POST['nicename']) || !empty($_POST['path']) || !empty($_POST['url'])) {
			$sql = "INSERT INTO ice_files (name, path, url) VALUES ('" . $_POST['nicename'] . "', '" . $_POST['path'] . "', '" . $_POST['url'] . "');";
			$db->query($sql);
			$sql = "SELECT id FROM ice_files WHERE name = '" . $_POST['nicename'] . "' LIMIT 1;";
			$res = $db->query($sql);
			while($row = mysql_fetch_array($res)) {
				$nr = $row['id'];
			}
			$sql = "INSERT INTO ice_pages (name, tid, url) VALUES ('" . $_POST['nicename'] . "', '" . $nr . "', '" . $_POST['url'] . "');";
			$db->query($sql);
			$db->close();
			die();
		}
	}
	if(!empty($_POST['del'])) {
		$_POST = $Auth->sanitize($_POST);
		$sql = "DELETE FROM ice_files WHERE id = '" . intval($_POST['id']) . "'; ";
		$db->query($sql);
		$sql = "DELETE FROM ice_pages WHERE tid = '" . intval($_POST['id']) . "'; ";
		$db->query($sql);
		$db->close();
		die();
	}
?>

<script type="text/javascript">
function templatemanager() {	
	var W = new ice.Window;
	W.name = "TMPLMAN";
	W.title = "Page File Manager";
	W.width = 600;
	W.setContent(document.getElementById('pageFileManager').innerHTML);
	W.contentBox.find('#addFilesBtn').click(function() {
		var AFW = new ice.Window;
		AFW.title = "Add File to Icy";
		AFW.width = 300;
		AFW.name = "AddFileDialog";
		AFW.closeable = false;
		AFW.minimizeable = false;
		AFW.setContent(document.getElementById('addFilesDialog').innerHTML);
		AFW.contentBox.find(':submit').click(function(e) {
			e.preventDefault();
			var $this = $(this);
			if($this.siblings('[value=""]').length > 0) { // Detect empty fields
				ice.message('All fields must be used', 'warning');
				return false;
			}
			var data = $this.parent().serialize();
			$this.siblings().andSelf().attr('disabled', 'disabled');
			$.post('fragments/templatemanager.php', data, function(data) {
				if(data.length > 0) {
					ice.message(data, 'warning');
					$this.siblings().andSelf().removeAttr('disabled');
				} else {
					var d = ice.Manager.getWindow("AddFileDialog").contentBox.find('input');
					ice.message('File has been added', 'info');
					var strS = '<tr><td>#</td><td>' + d.eq(0).val() + '</td><td>' + d.eq(1).val() + '</td><td>' + d.eq(2).val() + '</td></tr>';
					var k = ice.Manager.getWindow("TMPLMAN").element.find('tbody');
					k.html(k.html() + strS);
					ice.Manager.removeWindow('AddFileDialog');
				}
			});
		});
		AFW.contentBox.find('input[type=button]').click(function(){ ice.Manager.removeWindow("AddFileDialog"); });
		ice.Manager.addWindow(AFW);
	});
	W.contentBox.find('#delFilesBtn').click(function() {
		var res = prompt('Please enter the id of the file You want to remove from the system.', "#");
		if(res != null && res !="") {
			$.post("fragments/templatemanager.php", {del: true, id: res});
			alert('Reload the page to see the changes');
		}
			
	});
	ice.Manager.addWindow(W);
}

</script>
<script type="text/template" id="pageFileManager">
<div class="winpadd">
<p>Use this interface to add finished files to the cms. (URL)Instructions</p>
<br />
<table class="rounded6">
<thead>
	<tr>
		<td>ID</td>
		<td>Name</td>
		<td>Path</td>
		<td>URL</td>
	</tr>
</thead>
<tbody>

	<?php 
	$sql = "SELECT * FROM ice_files";
	$res = $db->query($sql);
	if(!$res) {
		echo "No pages";
	} else {
		while ($row = mysql_fetch_array($res)) {
			echo '<tr><td>', $row['id'], '</td><td>', $row['name'], '</td><td>', $row['path'], '</td><td>', $row['url'], '</td>';
		}
	}
	$db->close();
	?>

</tbody>
</table>
<div style="height:20px;"></div>
<div class="block190" style="float:left">
	<div id="delFilesBtn" class="expBtn rounded6" style="margin:0;">
		<p class="big">Remove a File</p>
		<p class="small">from Icy cms</p>
	</div>
</div>
<div class="block190" style="float:right">
	<div id="addFilesBtn" class="expBtn rounded6" style="margin:0;">
		<p class="big">Add a File</p>
		<p class="small">to Icy cms</p>
	</div>
</div>
<div style="clear:both"></div>
</div>
</script>
<script type="text/template" id="addFilesDialog">
<div class="winpadd">
	<p>You will have to put the files on the server yourself</p>
	<br />
	<form>
	<p><b>Name</b></p>
	<input type="text" name="nicename" style="width:90%"/>
	<p><b>Relative File Path. </b><i>(Relative to the document root) I.e. /templates/about.php</i></p>
	<input type="text" name="path" style="width:90%"/>
	<p><b>Relative Url.</b><i> (Relative to the root)I.e. if url to the page is example.com/tmp/a.php, write /tmp/a.php in this box.</i></p>
	<input type="text" name="url" style="width:90%"/>
	<input type="button" value="Abort" style="float:left"/>
	<input type="submit" value="Add file" style="float:right" />
	</form>
	<div style="clear:both"></div>
</div>

</script>