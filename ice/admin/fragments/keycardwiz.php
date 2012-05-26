<?php
	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/db.class.php';
	require_once '../../lib/auth.class.php';
	require_once '../../models/IceUser.php';

	$Auth->init(1);
	
	if(isset($_GET['download'])) {

		$uid = intval($_GET['uid']);
		$key = $_GET['fk'];
		($_SESSION['uid'] == $uid) or die('You can only download your own webid');
		
		$db->connect();
		$user = IceUser::byId($db, $uid);
		
		$user->hasKeyCard() or die('You dont have a keycard');

		header('Content-Type: text/x-webid');
		header('Content-Disposition: attachment; filename="' . $user->getUsername() . '.webid.json"');
		echo '{"user":{"id":"', $user->getId(), '","name":"', $user->getUsername(), '"},"keys":{"auth":"', $key,'"}}';
		die();
	}

	if(isset($_POST['set'])) {
		$key = $_POST['decodedKey'];
		$uid = intval($_POST['uid']);

		($_SESSION['uid'] == $uid) or die('{"status":"error", "error":"You can only create a webid for yourself"}');

		$db->connect();
		$user = IceUser::byId($db,$uid);

		($user !== null) or die('{"status":"error", "error":"Unknown user id."}');

		$user->setKeyCardHash($key);
		$user->save($db);
		die('{"status":"ok", "error":""}');
	}
?>

<script type="text/javascript">

function keycardwiz(user) {
	
	ice.fragment.addCss('pagewizard.css');
	
	var W = new ice.Window();
	W.title = 'Configure WebId for ' + user.username;
	W.width = 440;
	W.setContent(document.getElementById('webIdWizContent').innerHTML);
	W.icon = "layout_add.png";
	W.user = user;
	W.onOpen = function(win) {
		$('#btnGenerate', win.element).click(function() {
			var win = ice.Manager.getWindow($(this).inWindow()),
				key = $('#txtWebIdKey',win.element).val(),
				pin = $('#txtWebIdPin',win.element).val(),
				decodedKey = ice.decodeKey(key,pin);

			if(key.length < 64) {
				return alert("Your key must be at least 64 characters. Keep in mind that you do not need to remember it.");
			}
			if(pin.length < 3) {
				return alert("Your pin must be 3 characters long.");
			}
			//$('#keyCardWizPaneOne').slideUp();
			win.loadingOn();

			//$('#keyCardWizPaneTwo').slideDown();
			$.post('fragments/keycardwiz.php', {set:true,uid:win.user.id,decodedKey:decodedKey}, function(response,code) {
				if(code == 'success') {
					if(response.status == 'ok') {
						$('#keyCardWizPaneOne',win.element).slideUp();
						$('#keyCardWizPaneTwo',win.element).slideDown();
						win.loadingOff();
					} else {
						alert(response.error);
						console.error(response);
					}
				} else {
					alert('The server responded with an error');
					console.error(code,response);
				}
			}, 'json');

		});
		$('#btnDownload', win.element).click(function() {
			var win = ice.Manager.getWindow($(this).inWindow()),
				key = $('#txtWebIdKey',win.element).val();
			window.open('fragments/keycardwiz.php?download&uid='
				+ win.user.id + '&fk=' + encodeURIComponent(key));
		})

		$('#txtWebIdKey',win.element).val(win.generateWebIdKey(64));
	}

	W.generateWebIdKey = function(length) {
		var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_/*-+,;:<>|?=)(%#@£$[]{}§',
			out = '',
			charlen = chars.length;
		for(;length > 0; length--) {
			out += chars[Math.floor(Math.random() * charlen)];
		}
		return out;
	};

	ice.Manager.addWindow(W);
}

</script>

<script type="text/x-template" id="webIdWizContent">

<div class="winpadd" id="keyCardWizPaneOne">
	<p class="enlight">This key has ben generated for you. Please feel free to alter it for increased security.</p>
	<input type="text" id="txtWebIdKey" style="width:350px; margin: 0 0 20px 20px; font-family:monospace" maxlength="64"/>
	<br />
	<p class="enlight">A pin will be required to unlock the key. A minimum of 3 numbers are required. Max 4 096 numbers. Keep in mind that you will have to remember this.</p>
	<div class="winfpadd">
		<input type="text" id="txtWebIdPin" 
			style="width:60px; margin: 0 auto; display: block; font-size:26px;" placeholder="1234" minlength="3"/>
	</div>
	<input type="button" value="Generate file" id="btnGenerate" style="float:right;"/><br style="clear:both" />
</div>
<hr/>
<div class="winpadd" style="display:none" id="keyCardWizPaneTwo">
	<p class="enlight"><em>Done! </em>Please download the file by clicking the button below. </p>
	<input type="button" value="Download" id="btnDownload" style="width:100px; margin: 0 auto; display: block;"/><br style="clear:both" />
</div>
</script>