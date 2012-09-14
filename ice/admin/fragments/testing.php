<?php
	namespace Ice;
	use \PDO;
	use Ice\Models\User;

	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/Auth.php';
	require_once '../../lib/DB.php';
	require_once '../../models/User.php';
	require_once '../../lib/IceImage.php';
	Auth::init(2);
?>

<script type="text/javascript">
	function testing() {
		var W = new ice.Window();
		W.name = "testwin";
		W.width = 360;

		W.setContent(document.getElementById('testingTemplate').innerHTML);

		ice.Manager.addWindow(W);

		if(ice.testing == undefined) {
			ice.testing = {
				addPages: function() {
					ice.fragment.load('pagewizard', {}, {}, function() {
						var n = parseInt(prompt('How many pages do you wish to add', "10")), k;
						while (n > 0) {
							k = "pag" + n;
							wizCreatePage(k, k, 1, 'testwin');
							n--;

						}
					});
				}
			}
		}

	}
</script>

<script id="testingTemplate" type="text/x-template">
<div class="winpadd">
	<ul class="big_grid">
		<li onclick="ice.testing.addPages()">
			<h3>Add pages</h3>
		</li>
	</ul>
	<div style="clear:both" />
</div>
<hr />
<div class="winpadd">
	<?php
		$stmt = DB::prepare('SELECT * FROM ice_users WHERE id = :id');
		//$id = 450344;
		//$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
		$stmt -> execute(array(':id' => 1));

		$res = $stmt -> fetchAll(PDO::FETCH_CLASS, 'Ice\Models\User');
		//print_r($res[0] -> getArray());
		print($res[0] -> hasKeyCard() === true);


	?>
</div>
</script>