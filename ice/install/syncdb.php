<?php file_exists('LOCK') and die('You must delete the LOCK-file to run the installer again.');
	define('SYSINIT', true);
	require ('../ice-config.php');
	require ('../lib/db.class.php');
	
	require ('queries.php');
	
	$db->connect();
	
	foreach ($queries as $key => $val) {
		$db->query($val);
		if(strlen($db->error())>1) break;
	}
	
	
	$err = $db->error();
	$db->close();
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
		<?php
			if(strlen($err)>1) :
		?>
			<h1>Oops!</h1>
			<p><span>Something went bazonkas.</span> <br />
			<?php echo "<p class=err>".$err."</p>"; ?>
			</p>
		<?php 
			else :
		?>
			<h1>Done!</h1>
			<p><span>Now you should be able to use ICE!</span> <br />
			A file named LOCK will be placed in the installation directory. If you want to run the installer
			again, you will have to remove it by yourself.
			</p>
			<p>
				If everything is working, please <i>delete</i> the entire install directory.
			</p>
			<p>
				<b>Admin user:</b> admin<br/>
				<b>Admin password:</b> admin<br/>
				<b>Login here:</b> <a href="../admin/">administration</a><br/>
			</p>
		<?php endif; ?>
			<br style="clear: both" />
			
			<small>ICE! CMS</small>
		</div>
	</body>
</html>

<?php
if(strlen($err)<1)  {
	file_put_contents("LOCK", " ");
}

