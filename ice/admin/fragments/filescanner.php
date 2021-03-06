<?php
namespace Ice;
use Ice\Models\File as IceFile;

define('SYSINIT',true);

require_once '../../ice-config.php';
require_once '../../lib/DB.php';
require_once '../../lib/Auth.php';
require_once '../../lib/FileScanner.php';
require_once '../../models/File.php';

Auth::init(3);

if(isset($_POST['files'])) {
	$paths = $_POST['files'];
	
	foreach($paths as $i => $path64) {
		$path = Auth::sanitize(base64_decode($path64));
		$url = str_replace('\\', '/', $path); //Fix for windows

		$name = Auth::sanitize($_POST[$path64]); //The name for the text boxes are simply the encoded path

		$file = new IceFile(0, $name, $path, $url);
		$file->save();
	}
	die('{"status":"ok"}');
}

?>

<script type="text/javascript">

function filescanner() {
	var W = new ice.Window();
	W.name = 'filescanner';
	W.title = 'Scan for files';
	W.width = 600;

	W.onOpen = function(win) {
		$(':submit', win.contentBox).click(function(e){
			e.preventDefault();
			var W = ice.Manager.getWindow('filescanner'),
				postdata = $('form', W.contentBox).serialize();
			W.loadingOn();

			$.post('fragments/filescanner.php', postdata, function(response, code) {
				var W = ice.Manager.getWindow('filescanner');
				W.loadingOff();
				$('form', W.contentBox).slideUp();
				$('p.enlight', W.contentBox).text('The files were added successfully.');
				
				ice.publish('ice:template/new');
				ice.publish('ice:page/new');
			});


		});
	};

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
			<td>Name</td>
			<td>Path</td>
			<td>URL</td>
		</tr>
	</thead>
	<tbody>
		<?php
		//Find all php files in app root folder,
		//where the string ice-adapter.php exist
		//in the first five lines.
		$scanner = new FileScanner("/.php$/");
		$scanner->get_file_paths(realpath('../../../'));
		$scanner->filter_files("/ice-adapter.php/", 5);
		$scanner->make_paths_relative_to_doc_root();

		//Get a list of all existing paths
		$all_files = IceFile::find();
		if ($all_files !== null) {
			$existing_paths = array_map(
				function($file) {return $file->getPath();},
				$all_files);

			//Remove already existing entries
			$scanner->pathlist = array_diff(
				$scanner->pathlist,
				$existing_paths);
		}
		if (count($scanner->pathlist)) {
			foreach($scanner->pathlist as $path) {
				$enc = base64_encode($path);
				echo '<tr>';
				echo '<td><input type="checkbox" name="files[]" value="' . $enc . '" /></td>';
				echo '<td><input type="text" name="'. $enc .'" placeholder="Name this template" /></td>';
				echo "<td>$path</td>";
				echo "<td>$path</td>";
				echo '</tr>';
			}
		} else {
			echo '<tr><td>Nothing found</td></tr>';
		}
		?>
	</tbody>
</table>
<input type="submit" value="Add files" style="float:right"/>
</form>
</script>
