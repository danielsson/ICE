<?php
namespace Ice;
use Ice\Models\User;
use \PDO;

file_exists('LOCK') and die('You must delete the LOCK-file to run the installer again.');
define('SYSINIT', true);

require ('../ice-config.php');
require ('../lib/DB.php');
require ('../models/User.php');
require ('queries.php');

$error = "";
$state = 0;
$config=null;
$htaccess = null;
$htpath = null;
$cfgpath = null;
 
 
if(isset($_POST['dbhost'])) {
	if(empty($_POST['dbhost']) || empty($_POST['dbuser']) || empty($_POST['dbname'])) {
		$error = "Required field empty. Please, check your data.";
	} else {
		$state = 1;
		
		$htaccess = file_get_contents("htaccess.txt");
		$config = file_get_contents("config.txt");
		
		$config = str_replace("%HOST%", $_POST['dbhost'], $config);
		$config = str_replace("%USER%", $_POST['dbuser'], $config);
		$config = str_replace("%PASS%", $_POST['dbpass'], $config);
		$config = str_replace("%DBNAME%", $_POST['dbname'], $config);
		
		$config = str_replace("%BASE%", $_POST['base'], $config);
		
		$htaccess = str_replace("%SYSDIR%", $_POST['path'], $htaccess);
		
		$htpath = dirname(__FILE__)."/../../.htaccess";
		$cfgpath = dirname(__FILE__)."/../ice-config.php";
		
		file_put_contents($cfgpath, $config);
		file_put_contents($htpath, $htaccess);

		// The DB singleton automatically initialize itself from the settings
		// in the config file. However, since we just now inserted these values,
		// we must create a valid connection manually.
		DB::setDB(new PDO(
					"mysql:host={$_POST['dbhost']};dbname={$_POST['dbname']}",
					$_POST['dbuser'],
					$_POST['dbpass']
				));

		foreach ($queries as $val) {
			try {
				if(DB::exec($val) === false) {
					throw new Exception('SQL ERROR: ' . print_r(DB::errorInfo(), true));
				}
			} catch (PDOException $e){
				$error = $e->getMessage();
				$error .= $e->getTraceAsString();
				break;
			} catch (Exception $e) {
				$error = $e->getMessage();
				$error .= $e->getTraceAsString();
				break;
			}
		}

		if (strlen($error) < 1){
			$admin = new User(0,3,$_POST['username']);
			$admin->setPassword($_POST['password']);
			$admin->save();

			header("Location: done.php");
			die();
		}
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
			
			label { width: 100px; float:left; line-height: 25px; clear:left;}
			input { float: right;clear: right; width: 250px}
			.err {color:red}
			fieldset {border: 1px solid #CCC;}
		</style>
		<title>ICE! installer</title>
	</head>
	<body>
		<div class="fof">ICE</div>
		<div class="ff">
			<?php
				if(strlen($error)>1) :
			?>
				<h1>Oops!</h1>
				<p><span>Something went bazonkas.</span> <br />
				<?php echo "<p class=err>".$err."</p>"; ?>
				</p>
			<?php 
				else :
			?>
			<h1>Hi!<br /> Welcome to the installer for ICE! Lets get started.</h1>
			<p><span>Please notice that when submitted successfully, this installer will self-destruct.</span> <br />
			First, make sure you have access to your database information.
			</p>

			<form action="" method="post">
				<fieldset>
					<b>Admin user credentials</b><br />
					<p>Select a unique username and password</p>
					<label for="username">Username</label>
					<input type="text" id="username" name="username" value="" required/>
					<br/>
					<label for="password">Password</label>
					<input type="password" id="password" name="password" value="" required/>
				</fieldset>
				<br />
				<fieldset>
					<b>Database settings</b><br />
					<label for="dbhost">Host</label><input type="text" name="dbhost" value="localhost"/>
					<label for="dbuser">Username</label><input type="text" name="dbuser" value=""/>
					<label for="dbpass">Password</label><input type="text" name="dbpass" value=""/>
					<label for="dbname">Database</label><input type="text" name="dbname" value=""/>
				</fieldset>
				<br/>
				<fieldset>
					<b>Path settings</b><br />
					<p>Base url is the url to the folder which house the ice(system) folder.</p>
					<label for="base">Baseurl</label><input type="text" id="txtBase" name="base" value="http://example.com/rootdir/"/>
					<br/>
					<p>The absolute path is the path to the ice folder from the document root.</p>
					<label for="path">Absolute path</label><input type="text" id="txtPath" name="path" value="/rootdir/"/>
				</fieldset>
				<input type="submit" value="Install"/>
			</form>
			<br style="clear: both" />
			<?php endif; ?>
			<small>ICE! CMS</small>
		</div>
	<script type="text/javascript">
		window.onload = function() {
			var 
				txtBase = document.getElementById('txtBase'),
				txtPath = document.getElementById('txtPath'),
				base = document.location.href.replace('ice/install/install.php',''),
				path = document.location.pathname.replace('ice/install/install.php','');

			txtBase.value = base;
			txtPath.value = path;
		};
	</script>
	</body>
</html>