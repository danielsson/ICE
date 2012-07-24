<?php
	use Ice\Models\User;

	define('SYSINIT',true);
	
	require_once '../../ice-config.php';
	require_once '../../lib/Auth.php';
	require_once '../../models/User.php';

	Auth::init(3);
	
	if(isset($_POST['username'])) {
		if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['userlevel'])) {
			$uname = Auth::sanitize($_POST['username']);
			$pass = User::hash($_POST['password']);
			$lvl = (int) $_POST['userlevel'];

			$user = new User(0,$lvl,$uname,$pass);

			$user->save();

			die('{"status":"ok"}');

		} else {
			die('ERROR - EMPTY STRINGS');
		}
		
	}
?>

<script type="text/javascript">

function userwizard() {
	
	ice.fragment.addCss('pagewizard.css');
	
	var W = new ice.Window();
	W.title = 'New user wizard';
	W.width = 440;
	W.setContent(document.getElementById('userWizContent').innerHTML);
	W.data = {currentSlide: 0};
	W.icon = "layout_add.png";
	W.onOpen = function(win) {
		//Next button
		$(':button:eq(0)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()), $backBtn = $(':button:eq(1)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide+1;
			$win.titleBox.text('New user wizard - Step ' + ($win.data.currentSlide + 1) + ' of 4');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide != 2)  {
				$backBtn.removeAttr('disabled');
				this.disabled = false;
			} else {
				this.disabled = true;
			}
			if($win.data.currentSlide == 1) {
				$wdr = $('.wizDataReview', $win.contentBox);
				if($win.contentBox.find('input[name]').filter(function() { return $(this).val() == ""; }).length > 0) {
					ice.message('One or more fields are empty. Please go back and fill it in.', 'warning', $wdr);
					this.disabled = true;
				} else {
					var name = $(':text[name=username]', $win.contentBox).val(),
						lvl = $('select', $win.contentBox).val();
					$wdr.html('<b>Username:</b> ' + name + '<br /><b>Password:</b> ********'
						+ '<br /><b>Userlevel:</b> ' + lvl);
				}
			} else if($win.data.currentSlide == 2) {
				this.disabled = true;
				$backBtn.attr('disabled', 'disabled');
				$win.loadingOn();
				var formdata = $('form', $win.contentBox).serialize();
				wizCreateUser(formdata, $win.name);
				$win.beforeClose = function(p){}; //Remove "You sure" dialog
				return false;
			}
			$('.horizSlider', $win.contentBox).animate({marginLeft: nr},500);
			
		});
		//Prev button
		$(':button:eq(1)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()),$nextBtn = $(':button:eq(0)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide-1;
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 4');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide == 0)  {
				$nextBtn.removeAttr('disabled');
				this.disabled = true;
			} else {
				this.disabled = true;
			}
			$('.horizSlider', $win.contentBox).animate({marginLeft: nr},500);
		});
	};

	W.beforeClose = function(win) {
		if(confirm('You sure? Unsaved data will be lost.')) {
			return true;
		} else {
			return false;
		}
	};
	ice.Manager.addWindow(W);
}
function wizCreateUser(formdata, wName) {
	$.post('fragments/userwizard.php', formdata, function(data) {
		if(typeof data.status !== "undefined") {
			$win = ice.Manager.getWindow(wName);
			$win.loadingOff();
			$('.horizSlider', $win.contentBox).animate({marginLeft: 0-(2*418)},500);
			
			try{ice.Manager.getWindow('USRMAN').refresh();} catch(e){}
		} else {
			ice.message('Unknown error - ' + data);
			$win = ice.Manager.getWindow(wName);
			$win.loadingOff();
		}
	}, 'json');
}

</script>

<script type="text/x-template" id="userWizContent">

<div class="winpadd pageWiz">
	<div class="viewPort rounded6">
	<form method="post" action="fragments/userwizard.php">
		<ul class="horizSlider">
			<li class="horizSlide">
				<p class="enlight">
				This wizard will help you create a new user. To begin, please choose a
				username and a password.
				</p>
				<dl class="form">
					<dt><label for="usrWizUsername">Username</label></dt>
					<dd><input name="username" type="text" style="width: 255px;" /></dd>
				</dl>
				<dl class="form">
					<dt><label for="usrWizPassword">Password</label></dt>
					<dd><input name="password" type="password" style="width: 255px;" /></dd>
				</dl>
				<p class="enlight"> Select Userlevel for the new user. Be careful with level three.</p>
				<dl class="form">
					<dt><label>Userlevel</label></dt>
					<dd>
						<select name="userlevel">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
					</dd>
				</dl>
			</li>
			<li class="horizSlide">
				<div class="winpadd">
					<p>Please review the data you've entered below, then click next to create the page.</p>
					<p class="wizDataReview"></p>
				</div>
			</li>
			<li class="horizSlide">
				<div class="winpadd">
					<h2>Done</h2>
					<p>You have successfully created a new user.</p>
					<br /> <br /> <br />
					<div onclick="ice.fragment.load('usermanager')" class="expBtn rounded6">
						<p class="big">Open User Manager</p>
						<p class="small">To get access to more features.</p>
					</div>
				</div>
			</li>
		</ul>
	</form>
	</div>
	
	<input type="button" value="Next step >>" />
	<input type="button" value="<< Prev step" disabled="disabled" />
	<div style="clear:both"></div>
	
</div>
</script>