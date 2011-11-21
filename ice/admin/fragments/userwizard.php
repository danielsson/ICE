<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(3);
	
	if(isset($_POST['username'])) {
		if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['userlevel'])) {
			$uname = $Auth->sanitize($_POST['username']);
			$pass = md5($_POST['password']);
			$lvl = (int) $_POST['userlevel'];
			
			$db->connect();
			$sql = "INSERT INTO ice_users (username,password,userlevel) VALUES ('$uname','$pass','$lvl');";
			$db->query($sql);
			if($db->error()) {
				
				echo $db->error();
				$db->close();
				die();
			} else {
				$db->close();
				die('{"status":"ok"}');
				
			}
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
		$(':button:eq(0)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()), $backBtn = $(':button:eq(1)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide+1;
			$win.titleBox.text('New user wizard - Step ' + ($win.data.currentSlide + 1) + ' of 5');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide != 4)  {
				$backBtn.removeAttr('disabled');
				this.disabled = false;
			} else {
				this.disabled = true;
			}
			if($win.data.currentSlide == 3) {
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
			} else if($win.data.currentSlide == 4) {
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
		$(':button:eq(1)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()),$nextBtn = $(':button:eq(0)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide-1;
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 5');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide !== 0)  {
				$nextBtn.removeAttr('disabled');
				this.disabled = false;
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
			$('.horizSlider', $win.contentBox).animate({marginLeft: 0-(4*418)},500);
			
			try{ice.Manager.getWindow('USRMAN').refresh();} catch(e){}
		} else {
			ice.message('Unknown error - ' + data);
			$win = ice.Manager.getWindow(wName);
			$win.loadingOff();
		}
	}, 'json');
}

</script>

<script type="text/template" id="userWizContent">

<div class="winpadd pageWiz">
	<div class="viewPort rounded6">
	<form method="post" action="fragments/userwizard.php">
		<ul class="horizSlider">
			<li class="horizSlide">
				<div class="winpadd">
					<p>
					This wizard will help you create a new user. To begin, please choose a
					username.
					</p><br /> <br />
					<label for="usrWizUsername">Username<input name="username" type="text" style="width: 285px;" /> </label>
				</div>
				
				
			</li>
			<li class="horizSlide">
				<div class="winpadd">
					<p>Please enter the password</p><br /> <br />
					<label for="usrWizPassword">Password <input name="password" type="password" style="width: 285px;" /> </label>
				</div>
			</li>
			<li class="horizSlide">
				<div class="winpadd">
					<p> Select Userlevel for the new user. Be careful with level three.</p><br /> <br />
					<select name="userlevel">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
					</select>
				</div>
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