<?php 
	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/auth.class.php';
	require_once '../../lib/image.class.php';
	$Auth->init(2);
	
	
	if(isset($_GET['thumb'])) {
		$name = $Auth->sanitize($_GET['thumb']);
		$path = realpath('../../media/' . $name);
		$tpath = '../../cache/thumb_' . $name;
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
	if(isset($_POST['del'])) {
		$name = $Auth->sanitize($_POST['del']);
		$path = realpath('../../media/' . $name);
		if(file_exists($path)) {
			unlink($path);
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
		W.title = "Media Manager";
		W.loader.css("display","block");
		W.width = 671;
		
		W.allowRefresh = true;
		W.contentEndpoint = "fragments/mediamanager.php";
		
		W.onOpen = function(W) {
			W.loadingOff();
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

			$buttons.eq(0).click(function() {
				var W = ice.Manager.getWindow('MedMAN');
				if(confirm("You sure?")) {
					$.post('fragments/mediamanager.php', {del:W.toolbar.target.attr('data-name')});
					W.toolbar.target.animate({width:0}, 500).add(W.toolbar).fadeOut(0);
				}
			});
			
			$
		};
		
		W.setContent(document.getElementById('mediaManager').innerHTML);
		ice.Manager.addWindow(W);
		
		W.contentBox.find(".mediaList li img").slice(0,12).hide().each(function(index, el) {
			$(el).delay(index * 200).fadeIn(500);
		});
	}
	
</script>

<script type="text/template" id="mediaManager">
<?php endif; sleep(1); ?>

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