<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(2);
	$db->connect();
?>

<script type="text/javascript">
	function userwin(attrs) {
		var W = new ice.Window;
		W.width = 400;
		W.setContent(document.getElementById('editUsersDialogue').innerHTML);
		ice.Manager.addWindow(W);
		
	}
</script>
<script type="text/template" id="editUsersDialogue">
<div class="winpadd">
	<p>Editing: Someone</p>
	<br />
	<form>
	<p><b>UserName</b></p>
	<input type="text" name="username" style="width:90%"/>
	<p><b>Userlevel</b></p>
	<input type="text" name="path" style="width:90%"/>
	<p><b>Relative Url.</b><i> (Relative to the root)I.e. if url to the page is example.com/tmp/a.php, write /tmp/a.php in this box.</i></p>
	<input type="text" name="url" style="width:90%"/>
	<input type="button" value="Abort" style="float:left"/>
	<input type="submit" value="Add file" style="float:right" />
	</form>
	<div style="clear:both"></div>
</div>

</script>