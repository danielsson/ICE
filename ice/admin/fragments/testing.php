<?php
	namespace Ice;
	define('SYSINIT',true);
	require_once '../../ice-config.php';
	require_once '../../lib/Auth.php';
	require_once '../../lib/image.class.php';
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
	<p>Vinyl wayfarers keffiyeh, shoreditch carles cred thundercats occupy fixie pork belly leggings synth organic terry richardson.</p>
	<input type="text" />
	<input type="password" />
	<h3>Wayfarers</h3>
	<p>Raw denim echo park beard, bushwick swag tofu ethnic next level brunch irony master cleanse.</p>
	<label>Brunch: <input type="text" /></label>
	<div style="clear:both" />
</div>
</script>