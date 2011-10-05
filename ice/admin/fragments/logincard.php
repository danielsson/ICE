<?php 

if(!empty($_POST['userid'])) {
	session_start();
	$key = md5($_POST['key']);
	$userid = intval($_POST['userid']);
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	$sql = "SELECT * FROM ice_users WHERE id = '$userid' LIMIT 1;";
	$db->connect();
	$res = $db->query($sql);
	if(!$res) {
		$db->close();
		die('{"status":"error","error":"Wrong PIN or unathorized IDd"}');
	} else {
		while($row = mysql_fetch_array($res)) {
			if($row['keyCardHash'] == $key) {
				
				$_SESSION['username']=$row['username'];
				$_SESSION['userlevel'] = $row['userlevel'];
				$db->close();
				die('{"status":"ok","error":""}');
			} else {
				$db->close();
				die('{"status":"error","error":"Wrong PIN or unathorized ID"}');
			}
		}
	}
}

?>
<script type="text/javascript" >
	var idFile;
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
			$pinBox = $('#pinBox', win.contentBox), $pinInput = $('input', $pinBox);
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
			document.getElementById('idBox').addEventListener('drop', function(e) {
				e.stopPropagation();
				e.preventDefault();

				var dt = e.dataTransfer;
				var files = dt.files;
				var reader = new FileReader();
				reader.onload = function(e) {
					
					try {
						idFile = $.parseJSON(e.target.result);
					} catch(exception) {
						alert('Invalid file');
						$droptarget.removeClass('drophover');
						
						return true;
					}
					if(typeof idFile === "undefined") {
						alert('Invalid file');
						return true;
					}
					$ic = $('#idBox').children();
					
					$ic.find('img').attr('src', idFile.userdata.image);
					var tmp = "<li><b>" + idFile.userdata.surname + "</b><span>Surname</span></li>"+
					"<li><b>" + idFile.userdata.firstname + "</b><span>Firstname</span></li>"+
					"<li><b>" + idFile.userdata.gender + "</b><span>Gender</span></li>"+
					"<li><b>" + idFile.userdata.birtdate + "</b><span>Date Of Birth</span></li>"+
					"<li><b>" + idFile.userdata.ssn + "</b><span>SSN</span></li>"+
					"<li><b>" + idFile.userdata.country + "</b><span>Country</span></li>";
					$ic.find('.idCardValues').html(tmp);

					$ic.fadeIn();
					$ic.parent().unbind();
					$('#pinBox').slideDown();
					return false;
				};
				reader.readAsText(files[0]);
				return false;
			}, false);
			$('#pwdPin', win.contentBox).keyup(function() {
				$this = $(this);
				if($this.val().length == 4) {
					String.prototype.replaceAt=function(index, ch) {
						return this.substr(0, index) + ch + this.substr(index+ch.length);
					};
					var key = idFile.keys.auth;
					var values = $this.val().split('');
					for( i in values) {
						key = key.replaceAt(values[i], "");
					}
					$this.attr('disabled', 'disabled');
					$.post('fragments/logincard.php', {key: key, userid: idFile.userdata.id}, function(d) {
						if(d.status == "ok") {
							$('#headerText').html('<a href="#" onclick="ice.logout();"><b>Log out<b></a>');
							ice.fragment.load('sidepanel');
							ice.Manager.removeWindow('CardLogin');
							ice.curtain.raise(false);
						} else {
							alert(d.error);
						}
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
	<div class="idCardLeft">
		
		<img src="" width="85"/>
	</div>
	<div class="idCardRight">
		<h2>eIdent</h2>
		<ul class="idCardValues">
			
		</ul>
	</div>
	<div class="countryCode">US</div>
	
</div>
</div>
	<div id="pinBox">
		<b>Enter PIN</b>
		<input type="password" id="pwdPin" />
	</div>
</div>

</script>
