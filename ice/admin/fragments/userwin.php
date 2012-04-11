<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(3);
	
	if(isset($_POST['id']) && !isset($_POST['username'])) {
		$db->connect();
		$id = (int) $_POST['id'];
		$sql = "SELECT username, userlevel FROM ice_users WHERE id = $id;";
		
		$res = $db->query($sql);
		if($row = mysql_fetch_assoc($res)) {
			die(json_encode($row));
		} else {
			die("404");
		}
		
	} elseif (isset($_POST['username'])){
		if(!empty($_POST['username']) && !empty($_POST['id'])) {
			$db->connect();
			$uid = (int) $_POST['id'];
			$ulvl = (int) $_POST['userlevel'];
			$uname = $Auth->sanitize($_POST['username']);
			if(!empty($_POST['password'])) {
				$pwd = md5($_POST['password']);
				$sql = "UPDATE ice_users SET username = '$uname', password = '$pwd', userlevel = '$ulvl' WHERE id = $uid LIMIT 1;";
			} else {
				$sql = "UPDATE ice_users SET username = '$uname', userlevel = '$ulvl' WHERE id = $uid LIMIT 1;";
			}
			
			$db->query($sql);
			echo $db->error;
			$db->close();
		}
		
		die('{"status":"ok"}');
	}
?>

<script type="text/javascript">
	function userwin(attrs) {
		if(typeof attrs.id == "undefined") {return false;}
		var W = new ice.Window();
		W.width = 400;
		W.setContent(document.getElementById('editUsersDialogue').innerHTML);
		
		W.element.find('input[name=id]').val(attrs.id);
		W.uid = attrs.id;
		W.onOpen = function (win) {
			
			$.post('fragments/userwin.php', {id:win.uid}, function(data) {
				if(typeof data.username !== "undefined") {
					win.element.find('[name=username]').val(data.username);
					win.element.find('[name=userlevel]').val(data.userlevel);
					win.element.find('input,select').removeAttr('disabled');
				} else {
					ice.message('Error. data corrupt');
				}
				
			},'json');
			
			win.element.find('[type=submit]').click(function(e) {
				e.preventDefault();
				var d = win.element.find('form').serialize();
				$.post('fragments/userwin.php', d, function(data) {
					if(typeof data.status !== 'undefined') {
						ice.message('Saved user', 'info');
						ice.Manager.getWindow('USRMAN').refresh();
						ice.Manager.removeWindow(win.name);
					} else {
						ice.message('Error');
					}
				}, 'json');
			});
		};
		
		ice.Manager.addWindow(W);
		
	}
</script>
<script type="text/template" id="editUsersDialogue">
<div class="winpadd">

	<form>
	<p><b>UserName</b></p>
	<input type="hidden" name="id"/>
	<input type="text" name="username" style="width:90%" disabled="disabled"/>
	<p><b>Userlevel</b></p><br />
	<select name="userlevel" disabled="disabled">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
	</select>
	
	<br/>
	<p><b>Password</b></p>
	<input type="password" name="password" style="width:90%" disabled="disabled"/>
	<br style="clear: both" />
	<input type="button" value="Abort" style="float:left" onclick="ice.Manager.removeWindow($(this).inWindow())"/>
	
	<input type="submit" value="Update" style="float:right" disabled="disabled" />
	<input type="button" value="Create WebID" style="float:right" />
	</form>
	<div style="clear:both"></div>
</div>

</script>