<?php 

if(!empty($_POST['userid'])) {
	session_start();
	
	define('SYSINIT',true);

	require_once '../../ice-config.php';
	require_once '../../lib/db.class.php';
	require_once '../../models/IceUser.php';

	$key = $_POST['key'];
	$uid = intval($_POST['userid']);

	$db->connect();
	$user = IceUser::byId($db,$uid);
	if($user !== null && $user->keyCardHashEquals($key)) {
		$_SESSION['username']=$user->getUsername();
		$_SESSION['userlevel'] = $user->getUserlevel();
		$_SESSION['uid'] = $user->getId();
		$db->close();
		die('{"status":"ok","error":""}');
	} else {
		die('{"status":"error","error":"Wrong PIN or unathorized IDd"}');
	}
}

?>
<script type="text/javascript" >
	function logincard() {
		ice.fragment.addCss('logincard.css');
		var W = new ice.Window();
		W.name = "CardLogin";
		W.title = "Login With WebID";
		W.width = 342;
		W.closeable = false;
		W.element.css('zIndex',99999);
		W.setContent(document.getElementById('loginCardWindow').innerHTML);
		W.onOpen = function(win) {
			var $droptarget = $('#idBox', win.contentBox),
				$pinBox = $('#pinBox', win.contentBox),
				$pinInput = $('input', $pinBox);

			this.idFile = null;

			$droptarget.bind('dragenter', function(e) {
				e.stopPropagation();
				e.preventDefault();
				$droptarget.addClass('drophover');
				
				return false;
			});
			$droptarget.bind('dragleave', function(e) {
				e.stopPropagation();
				e.preventDefault();
				$droptarget.removeClass('drophover');
				
				return false;
			});
			$droptarget.bind('dragover', function(e) {
				e.stopPropagation();
				e.preventDefault();
			});

			win.dropHandler = function(e) {
				e.stopPropagation();
				e.preventDefault();

				var dt = e.dataTransfer,
					files = dt.files,
					reader = new FileReader();
					win = ice.Manager.getWindow($(this).inWindow());

				reader.onload = function(e) {
					
					try {
						win.idFile = $.parseJSON(e.target.result);
					} catch(exception) {
						alert('Invalid file');
						$droptarget.removeClass('drophover');
						
						return true;
					}
					if(typeof win.idFile === "undefined") {
						alert('Invalid file');
						return true;
					}
					
					$idtext = $('<h2>').text(win.idFile.user.name);

					$('.idCard').children().replaceWith($idtext)
					$('#idBox').css({boxShadow: "0 1px 1px #AEAEAE", backgroundColor: "#FFD"});

					$('#pinBox')
						.slideDown()
						//Focus to input
						.children('input')
							.focus();

					return false;
				};
				reader.readAsText(files[0]);
				return false;
			};

			document.getElementById('idBox').addEventListener('drop', win.dropHandler);

			
			$('#pwdPin', win.contentBox).keyup(function(e) {

				if(this.value.length > 2 && e.keyCode == 13) {
					var $this = $(this),
						win = ice.Manager.getWindow($this.inWindow());
					
					win.loadingOn();

					var key = ice.decodeKey(win.idFile.keys.auth, String(this.value));

					$.post('fragments/logincard.php',
						{key: key, userid: win.idFile.user.id},
						function(response,statuscode) {
							if(statuscode == 'success' && response.status == 'ok') {
								$('#headerText').html('<a href="#" onclick="ice.logout();"><b>Log out<b></a>');
								ice.fragment.load('sidepanel');
								ice.Manager.removeWindow('CardLogin');
								ice.curtain.raise();
								console.log('success');
								return;
							} else if(statuscode == 'success') {
								alert(response.error);
								console.info(statuscode, response);
							} else {
								alert('The server responded with a error. Response code: ' + statuscode);
								console.error(statuscode, response);
							}
							
							//Since we failed, reset the window
							ice.Manager.removeWindow('CardLogin');
							logincard();

					},'json');
				}
			});
		};
		ice.Manager.addWindow(W);
		
	}

</script>
<script type="text/template" id="loginCardWindow" >

<div class="winpadd" id="loginCard">
	<div id="idBox" class="rounded6">
		<div class="idCard rounded6">
			<h2 style="color:#DDD">Drop your WebID here.</h2>
		</div>
	</div>
	<div id="pinBox">
		<b>Enter PIN</b>
		<input type="password" id="pwdPin" />
	</div>
</div>

</script>
