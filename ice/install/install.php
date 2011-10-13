<?php
$error = "";
$state = 0;
$config=null;
$htaccess = null;
$htpath = null;
$cfgpath = null;
 
 
if(isset($_POST['host'])) {
	if(empty($_POST['host']) || empty($_POST['user']) || empty($_POST['dbname'])) {
		$error = "Required field empty. Please, check your data.";
	} else {
		$state = 1;
		
		$htaccess = file_get_contents("ha.txt");
		$config = file_get_contents("cfg.txt");
		
		$config = str_replace("%HOST%", $_POST['host'], $config);
		$config = str_replace("%USER%", $_POST['user'], $config);
		$config = str_replace("%PASS%", $_POST['pass'], $config);
		$config = str_replace("%DBNAME%", $_POST['dbname'], $config);
		
		$config = str_replace("%BASE%", $_POST['base'], $config);
		
		$htaccess = str_replace("%SYSDIR%", $_POST['path'], $htaccess);
		
		$htpath = dirname(__FILE__)."/../../.htaccess";
		$cfgpath = dirname(__FILE__)."/../ice-config.php";
		
		file_put_contents($cfgpath, $config);
		file_put_contents($htpath, $htaccess);
		
	}
}


	
?>

<!DOCTYPE html>
<html>
	<head>
		<style>
		body {font-size: 13px;}
			.ff {
				font-family: sans-serif;
				width: 400px;
				margin: -100px auto 0 auto;
			}
			.ff p {
				line-height: 2;
			}
			.ff span {
				font-size: 15px;
			}
			.ff small {
				font-size: 9px;
				color: #888;
				text-align: center;
				display: block;
			}
			.fof {
				font-size:200px;
				font-family: sans-serif;
				color: #F5F5F5;
				text-align: center;
			}
			
			label { width: 100px; float:left; line-height: 25px;}
			input { float: right;clear: right; width: 250px}
			.err {color:red}
		</style>
		<title>ICE! installer</title>
	</head>
	<body>
		<div class="fof">ICE</div>
		<div class="ff">
			
			<?php if($state === 0) : ?>
				
			<h1>Hi!<br /> Welcome to the installer for ICE! Lets get started.</h1>
			<p><span>Please notice that when submitted successfully, this installer will self-destruct.</span> <br />
				First, make sure you have access to your database information.
			</p>
			<?php 
				if(strlen($error)>0) echo "<p class=err>".$error."</p>"
			?>
			<form action="" method="post">
				<fieldset>
					<b>Database settings</b><br />
					<label for="host">Host</label><input type="text" name="host" value="localhost"/>
					<label for="user">Username</label><input type="text" name="user" value=""/>
					<label for="pass">Password</label><input type="text" name="pass" value=""/>
					<label for="dbname">Database</label><input type="text" name="dbname" value=""/>
				</fieldset>
				<br/>
				<fieldset>
					<b>Path settings</b><br />
					<p>Base url is the url to the folder which house the ice(system) folder.</p>
					<label for="base">Baseurl</label><input type="text" name="base" value="http://example.com/rootdir/"/>
					<br/>
					<p>The absolute path is the path to the ice folder from the document root.</p>
					<label for="path">Absolute path</label><input type="text" name="path" value="/rootdir/"/>
				</fieldset>
				<input type="submit"/>
			</form>
			
			<?php elseif ($state===1) :
				echo "<h1>Writing files... [DONE]</h1>";
			 endif; ?>
			<br style="clear: both" />
			
			<small>ICE! CMS</small>
		</div>
	</body>
</html>

<?php 
	if($state === 1) {
		rmdir(dirname(__FILE__));
		
	}
?>