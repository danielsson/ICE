<?php 

	if(isset($_POST['username'])) {
		define('SYSINIT',true);
		require '../../ice-config.php';
		require '../../lib/db.class.php';
		require '../../lib/auth.class.php';
		$Auth->loginProcess();
		die(); //This should be unreachable
	} elseif($_POST['logout']=="true"){
		session_start();
		unset($_SESSION['username'], $_SESSION['userlevel']);
		die();
	} else {
		session_start();
		if(isset($_SESSION['userlevel']) && $_SESSION['userlevel'] > 0) { ?>
				<script type="text/javascript" >
					function login() {
						ice.fragment.load("sidebar");
					}
				</script>
			<?php 
			die();
		}
	}

?>
<script type="text/javascript" >
	function login() {
		
		var lWin = new ice.Window();
		lWin.name = "LoginWindow";
		lWin.title = "Please log in:";
		lWin.width = 450;
		lWin.closeable = false;
		lWin.minimizeable = false;
		lWin.icon = " ";
		lWin.element.css({zIndex: 99999, boxShadow:'none'});
		lWin.onOpen = function(win) {
			ice.curtain.lower(false);
			
			win.setContent(document.getElementById('loginWindow').innerHTML);
			var $t = win.element;
			$('input[type=submit]', $t).click(function(e) {
				e.preventDefault();
				if($('input:text', $t).val() == "" || $('input:password', $t).val() == "") {
					ice.message('All fields are required!', 'warning', '#loginError');
					return true;
				}
				var formData = $('form', $t).serialize();
				win.loadingOn();
				$.post('fragments/login.php?xhr=true', formData, function(data) {
					win.loadingOff();
					if(data !="true") {
						ice.message('Wrong username/password', 'warning', '#loginError');
						win.element.effect('shake', 100);
					} else {
						ice.Manager.displayNoWindowsWarning = true;
						$('#headerText').html('<a href="#" onclick="ice.logout();"><b>Log out<b></a>');
						ice.fragment.load('sidepanel');
						ice.Manager.removeWindow('LoginWindow');
						ice.curtain.raise();
						
					}
				});
			});
			$('a', win.contentBox).click(function() {
				ice.fragment.load('logincard');
				ice.Manager.removeWindow("LoginWindow");
			});

			win.element.css({top:(window.innerHeight - 332) / 2});
		};
		
		ice.Manager.addWindow(lWin);
		
	}

</script>
<script type="text/template" id="loginWindow" >
	<div id="loginError"></div>
	<form action="#" method="post" >
		<br />
		<dl class="form">
			<dt><label>Username</label></dt>
			<dd><input type="text" id="username" name="username" style="width:300px" /></dd>

			<dt><label>Password</label></dt>
			<dd><input type="password" id="password" name="password" style="width:300px" /></dd>

		</dl>
		<div class="winpadd">
			<a href="#" style="float: left; margin-top: 15px;">Have a WebID?</a>
			<input type="submit" value="Log in" style="float:right;"></input>
		</div>
	</form>
</script>