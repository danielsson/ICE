<?php
namespace Ice;

define('SYSINIT',true);

require_once '../../ice-config.php';
require_once '../../lib/auth.class.php';
require_once '../../lib/scanner.php';

if(isset($_POST['files'])) {
	print_r($_POST['files']);
	die();
}

?>

<script type="text/javascript">

function filescanner() {
	var W = new ice.Window();
	W.name = 'filescanner';
	W.title = 'Scan for files';
	W.width = 600;

	W.setContent(document.getElementById('filescannertemplate').innerHTML);
	ice.Manager.addWindow(W);
}
</script>

<script type="text/x-template" id="filescannertemplate">

<p class="enlight">These files are suspected to be templates.</p>
<form method="post" action="fragments/filescanner.php">
<table>
	<thead>
		<tr>
			<td>Add</td>
			<td>Path</td>
			<td>URL</td>
		</tr>
	</thead>
	<tbody>
		<?php

		$scanner = new FileScanner("/.php$/");
		$scanner->get_file_paths(realpath('../../../'));
		$scanner->filter_files("/ice-adapter.php/", 5);
		$scanner->make_paths_relative_to_doc_root();

		foreach($scanner->pathlist as $path) {
			echo '<tr>';
			echo '<td><input type="checkbox" name="files[]" value="' . base64_encode($path) . '" /></td>';
			echo "<td>$path</td>";
			echo "<td>$path</td>";
			echo '</tr>';
		}

		?>
	</tbody>
</table>
<input type="submit" value="Add files" style="float:right"/>
</form>
</script>
