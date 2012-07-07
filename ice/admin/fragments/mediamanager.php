<?php 
	namespace Ice;
	use \IceImage;
	
	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/Auth.php';
	require_once '../../lib/image.class.php';
	Auth::init(2);
	
	
	if(isset($_GET['thumb'])) {
		$name = Auth::sanitize($_GET['thumb']);
		$path = realpath('../../media/' . $name);
		$tpath = '../../cache/thumb_' . $name;
		if(!file_exists($tpath)) {
			$thumb = new IceImage($path);
			$thumb->setCachePath($tpath);
			$thumb->resizeToFit(150, 112);

			header("Content-Type: image/jpeg");
		
			$thumb->outputAndCache();
		} else {
			header("Content-Type: image/jpeg");
			readfile($tpath);
		}
		die();
	} 
	if(isset($_POST['del'])) {
		$name = Auth::sanitize($_POST['del']);
		$path = realpath('../../media/' . $name);
		if(file_exists($path)) {
			unlink($path);
		}
		die();
	}
	if(!empty($_FILES)) {
		require_once "../../lib/image.class.php";
		$targetFile =  realpath('../../media/') . DIRECTORY_SEPARATOR . basename($_FILES['userfile']['name']);

		if (IceImage::isAllowedType($targetFile)) {
			if(move_uploaded_file($_FILES['userfile']['tmp_name'],$targetFile)) {
				header("Image created", true, 201);
				echo json_encode(array('status' => 201, "error" => NULL));
			} else {
				header("Moving the file failed", true, 500);
				echo json_encode(array('status' => 500, "error" => "Failed to move the file. Try changing its name."));
			}
		} else {
			header("Filetype not allowed", true, 415);
			echo json_encode(array('status' => 415, "error" => "Filetype not allowed"));
		}
		die();
		
	}
	
if(!isset($_POST['refresh'])) :
?>

<script type="text/javascript">
	function mediamanager() {
		ice.fragment.addCss('mediamanager.css');
		var W = new ice.Window();
		W.name = "MedMAN";
		W.title = 'Media Manager - <a href="#" id="newImage">Upload</a>';
		W.icon = "../../editor/res/image_edit.png";
		W.loader.css("display","block");
		W.width = 671;
		
		W.allowRefresh = true;
		W.contentEndpoint = "fragments/mediamanager.php";
		
		W.onOpen = function(W) {
			W.loadingOff();
					
			new AjaxUpload('newImage', {
				action: 'fragments/mediamanager.php',
				responseType: "json",
				onComplete: function(file, response) {
					if(response.status && response.status == 201) {
						ice.Manager.getWindow('MedMAN').refresh();
					} else {
						alert(response.error);
					}
				}
			});
		};
		W.onContentChange = function(W) {
			W.toolbar = W.contentBox.find("#mmToolbar");
			var $buttons = W.toolbar.children();
			
			W.contentBox.find(".mediaList li").hover(function() {
				var $this = $(this), tb = W.toolbar;
				var offset = $this.position();
				tb.css({
					display: 'block',
					left: offset.left,
					top: offset.top
				});
				tb.target = $this;
				
			});

			$buttons.click(function() {
				
				var $this = $(this),
					index = $this.index(),
					W = ice.Manager.getWindow('MedMAN');
					
				switch(index) {
				case 0: //Delete
					if(confirm("You sure?")) {
						$.post('fragments/mediamanager.php', {del:W.toolbar.target.attr('data-name')});
						W.toolbar.target.animate({width:0}, 500).add(W.toolbar).fadeOut(0);
					}
					break;
				case 1: //View
					window.open("../media/" + W.toolbar.target.attr('data-name'));
				}
			});
			
			W.contentBox.find(".mediaList li img").slice(0,12).hide().each(function(index, el) {
				$(el).delay(index * 200).fadeIn(500);
			});
		};
		
		W.setContent(document.getElementById('mediaManager').innerHTML);
		ice.Manager.addWindow(W);
		
	}
	
</script>

<script type="text/template" id="mediaManager">
<?php endif; ?>

<div class="mediamanager">
	<ul id="mmToolbar">
		<li>
			DEL
		</li>
		<li>
			LINK
		</li>
	</ul>
	<div class="mediaList rounded6">
		<ul>
			<?php
			$images = IceImage::getImagePaths('../../media/*.*');
			foreach ($images as $key => $value) {
				echo "<li data-name=\"$value\"><img src=\"fragments/mediamanager.php?thumb=$value\"></li>";
			}

			?>
			<div style="clear: both"/>
		</ul>
	</div>
</div>
<?php if(!isset($_POST['refresh'])) : ?>
</script>
<?php endif; ?>