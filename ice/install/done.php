<?php 

	file_exists('LOCK') and die('You must delete the LOCK-file to run the installer again.');

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
			<h1>Done!</h1>
			<p><span>Now you should be able to use ICE!</span> <br />
			A file named LOCK will be placed in the installation directory. If you want to run the installer
			again, you will have to remove it by yourself.
			</p>
			<p>
				If everything is working, please <i>delete</i> the entire install directory.
			</p>
			<p>
				<b>Login here:</b> <a href="../admin/#!filescanner">administration</a><br/>
			</p>
			<br style="clear: both" />
			
			<small>ICE! CMS</small>
		</div>
	</body>
</html>

<?php file_put_contents("LOCK", " "); 
