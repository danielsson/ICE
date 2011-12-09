<?php 
	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/auth.class.php';
	require_once '../../lib/image.class.php';
	$Auth->init(2);
	
	
	if(isset($_GET['thumb'])) {
		$path = realpath('../../media/' . $_GET['thumb']);
		$tpath = '../../cache/thumb_' . $_GET['thumb'];
		if(!file_exists($tpath)) {
			$thumb = new IceImage($path);
			$thumb->setCachePath($tpath);
			$thumb->resizeWidth(150);
			
			
			header("Content-Type: image/jpeg");
		
			$thumb->outputAndCache();
		} else {
			header("Content-Type: image/jpeg");
			readfile($tpath);
		}
		die();
	} 
?>

<script type="text/javascript">
	function mediamanager() {
		ice.fragment.addCss('mediamanager.css');
		var W = new ice.Window();
		W.name = "MedMAN";
		W.title = "Media Manager";
		
		W.width = 671;
		
		W.onContentChange = function(W) {
			//EventBindings
		};
		
		W.setContent(document.getElementById('mediaManager').innerHTML);
		ice.Manager.addWindow(W);
		
	}
	
</script>

<script type="text/template" id="mediaManager">
<div class="mediamanager">
	<div class="mediaList rounded6">
		<ul>
		<?php
			$images = IceImage::getImagePaths('../../media/*.*');
			foreach ($images as $key => $value) {
				echo "<li><img src=\"http://localhost/ice/ice/admin/fragments/mediamanager.php?thumb=$value[1]\"></li>";
			}
		?>
			<div style="clear: both"/>

		</ul>
	</div>
</div>
	
</script>