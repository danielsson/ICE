<?php
	define('SYSINIT',true);
	require '../../lib/auth.class.php';
	$Auth->init(1);
?>

<script type="text/javascript">
	function browser(data) {
		var B = new ice.Window, randName = "iframe_" + Math.floor(Math.random()*11);
		B.width = $(window).width() - 100;
		B.title = "Browsing: <i>" + data.url + "</i>";
		B.setContent(document.getElementById('browserContent').innerHTML);
		$('iframe', B.contentBox).attr('name', randName);

		if(data.postEdit === true) {
			
			var dm = $('<form>').attr({
				id: "iframePostShiv",
				action: data.url,
				method: "post",
				style: "display:none;",
				target: randName
			});
			$('<input type="text" name="edit" value="true">').appendTo(dm);
			dm.appendTo('body');
			
		} else {
			$('iframe', B.contentBox).attr('src', data.url);
		}
		
		ice.Manager.addWindow(B);
		if(data.postEdit === true) {
			$("#iframePostShiv").submit().remove();
		}
	}

</script>

<script type="text/template" id="browserContent">
<iframe src="#" style="width:100%; height:500px; background: #FFF;" name=""> </iframe>
</script>
