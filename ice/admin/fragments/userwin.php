<?php
	namespace Ice;
	use Ice\Models\User;

	define('SYSINIT',true);
	
	require_once '../../ice-config.php';
	require_once '../../lib/db.class.php';
	require_once '../../lib/Auth.php';
	require_once '../../models/User.php';
	
	Auth::init(3);
	
	if(isset($_POST['id']) && !isset($_POST['username'])) {
		$db->connect();
		$id = (int) $_POST['id'];
		
		$user = User::byId($db, $id);
		
		if($user != NULL) {
			$arr = $user->getArray();
			$arr['isCurrent'] = ($user->getId() == $_SESSION['uid']);
			die(json_encode($arr));
		} else {
			die ('error');
		}
	} elseif (isset($_POST['username'])){
		if(!empty($_POST['username']) && !empty($_POST['id'])) {
			$db->connect();
			$uid = (int) $_POST['id'];
			$ulvl = (int) $_POST['userlevel'];
			$uname = Auth::sanitize($_POST['username']);

			$user = User::byId($db,$uid);
			
			$user->setUserLevel($ulvl);
			$user->setUsername($uname);

			if(!empty($_POST['password'])) {
				$user->setPassword($_POST['password']);
			}

			$user->save($db);
			$db->close();
		}
		
		die('{"status":"ok"}');
	} elseif (isset($_POST['delete'])) {
		$uid = intval($_POST['delete']);
		$db->connect();

		$user = User::byId($db,$uid);

		if($user != null) {
			$user -> delete($db);
		}
		die('{"status":"ok"}');
	}
?>

<script type="text/javascript">
	function userwin(attrs) {
		if(typeof attrs.id == "undefined") {return false;}
		var W = new ice.Window();
		W.width = 400;
		W.title = "Edit user"
		W.setContent(document.getElementById('editUsersDialogue').innerHTML);
		
		W.element.find('input[name=id]').val(attrs.id);
		W.uid = attrs.id;
		W.onOpen = function (win) {
			win.loadingOn();
			$.post('fragments/userwin.php', {id:win.uid}, function(data) {
				win.loadingOff();
				if(typeof data.username !== "undefined") {
					win.element.find('[name=username]').val(data.username);
					win.element.find('[name=userlevel]').val(data.userlevel);
					win.element.find('input,select').removeAttr('disabled');
					win.user = data;
					if(!data.isCurrent) {
						$('#btnCreateWebId', win.element).hide();
					}
				} else {
					ice.message('Error. data corrupt');
				}
				
			},'json');
			
			win.element.find('[type=submit]').click(function(e) {
				e.preventDefault();
				var d = win.element.find('form').serialize();
				win.loadingOn();
				$.post('fragments/userwin.php', d, function(data) {
					win.loadingOff();
					if(typeof data.status !== 'undefined') {
						ice.message('Saved user', 'info');
						ice.Manager.getWindow('USRMAN').refresh();
						ice.Manager.removeWindow(win.name);
					} else {
						ice.message('Error');
					}
				}, 'json');
			});
			win.element.find('#btnCreateWebId').click(function() {
				var win = ice.Manager.getWindow($(this).inWindow());
				ice.fragment.load('keycardwiz',{},win.user);
			});
			win.element.find('#btnDeleteUser').click(function() {
				if(confirm('Sure?')) {
					$.post('fragments/userwin.php', {delete: win.user.id}, function(response, code) {
						if(code != 'success') {
							ice.message(code);
							console.log(response);
						} else {
							ice.Manager.removeWindow(win.name);
							try {ice.Manager.getWindow('USRMAN').refresh();} catch(e){}
						}
					});
				}
			});
		};
		
		ice.Manager.addWindow(W);
		
	}
</script>
<script type="text/template" id="editUsersDialogue">


	<form>
	<input type="hidden" name="id"/>

	<dl class="form">
		<dt><label for="username">Username:</label></dt>
		<dd><input type="text" name="username" style="width:250px" disabled="disabled"/></dd>

		<dt><label for="userlevel">Userlevel: </label></dt>
		<dd>
			<select name="userlevel" disabled="disabled">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
			</select>
		</dd>
		<dt><label for="password">Password:</label></dt>
		<dd><input type="password" name="password" style="width:250px" disabled="disabled"/></dd>
	</dl>

	<br style="clear: both" />
	<!--<input type="button" value="Abort" style="float:left" onclick="ice.Manager.removeWindow($(this).inWindow())"/>-->
	
	<input type="submit" value="Update" style="float:right" disabled="disabled" />
	<input type="button" value="Create WebID" style="float:right" id="btnCreateWebId" />
	<input type="button" value="Delete" style="float:right; color:#F00" id="btnDeleteUser" />
	</form>
	<div style="clear:both"></div>


</script>