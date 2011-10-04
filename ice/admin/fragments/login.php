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
		if($_SESSION['userlevel'] > 0) { ?>
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
		ice.fragment.addCss('login.css');
		var lWin = new ice.Window;
		lWin.name = "LoginWindow";
		lWin.title = "&nbsp;&nbsp;&nbsp;&nbsp;";
		lWin.width = 450;
		lWin.closeable = false;
		lWin.minimizeable = false;
		lWin.icon = "ice-logo_05.png";
		lWin.onOpen = function(win) {
			win.setContent(document.getElementById('loginWindow').innerHTML);
			var $t = win.element;
			$('input[type=submit]', $t).click(function(e) {
				e.preventDefault();
				if($('input:text', $t).val() == "" || $('input:password', $t).val() =="") {
					ice.message('All fields are required!', 'warning', '#loginError');
					return true;
				}
				var formData = $('form', $t).serialize();
				$.post('fragments/login.php?xhr=true', formData, function(data) {
					if(data !="true") {
						ice.message('Wrong username/password', 'warning', '#loginError');
					} else {
						$('#headerText').html('<a href="#" onclick="ice.logout();"><b>Log out<b></a>');
						ice.fragment.load('sidebar');
						ice.Manager.removeWindow('LoginWindow');
					}
				});
			});
			$('a', win.contentBox).click(function() {
				ice.fragment.load('logincard');
				ice.Manager.removeWindow("LoginWindow");
			});
		};
		
		ice.Manager.addWindow(lWin);
		
	}

</script>
<script type="text/template" id="loginWindow" >

<div class="loginwindow">
	<div class="loginForm">
		<div id="loginError"></div>
		<b>Please log in</b>
		<form action="#" method="post" >
			<label for="username">Username</label>
			<input type="text" id="username" name="username"></input>
			<br/>
			<label for="password">Password</label>
			<input type="password" id="password" name="password"></input
			<br />
			<input type="submit" value="Log in" style="float:right;"></input>
		</form>
		<a href="#">Have an WebID?</a>
	<br style="clear: both"/>
	</div>
</div>
</script>