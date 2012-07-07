<?php
	namespace Ice;
	use Ice\Models\Page;

	define('SYSINIT',true);
	
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/Auth.php';
	require '../../models/Page.php';
	
	Auth::init(2);
	$db->connect();
	
	if(!empty($_POST['name'])) {
		$data = Array('status'=>'ok', 'error'=>'none');
		$_POST = Auth::sanitize($_POST);

		$tmp = parse_url($config['baseurl'] . $_POST['url']);
		$url = $db->escape($tmp['path']);

		$page = new Page(
				0,
				$db->escape($_POST['name']),
				intval($_POST['tid']),
				$url
			);

		$page->save($db);
		$db->close();

		die(json_encode($data));
	}
?>

<script type="text/javascript">

function pagewizard() {
	
	ice.fragment.addCss('pagewizard.css');
	
	var W = new ice.Window();
	W.title = 'New page wizard';
	W.width = 440;
	W.setContent(document.getElementById('pageWizContent').innerHTML);
	W.data = {currentSlide: 0};
	W.icon = "layout_add.png";
	W.onOpen = function(win) {
		$(':button:eq(0)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()), $backBtn = $(':button:eq(1)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide+1;
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 4');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide != 3)  {
				$backBtn.removeAttr('disabled');
				this.disabled = false;
			} else {
				this.disabled = true;
			}
			if($win.data.currentSlide == 2) {
				$wdr = $('.wizDataReview', $win.contentBox);
				if($win.contentBox.find('input[name]').filter(function() { return $(this).val() == ""; }).length > 0) {
					ice.message('One or more fields are empty. Please go back and fill it in.', 'warning', $wdr);
					this.disabled = true;
				} else {
					name = $(':text[name=name]', $win.contentBox).val();
					url = $(':text[name=url]', $win.contentBox).val();
					template = $(':radio[name=tid]:checked', $win.contentBox).val();
					$wdr.html('<b>Name:</b> ' + name + '<br /><b>Url:</b> <?php echo $config['baseurl']; ?>' + url + '<br /><b>Template ID:</b> ' + template);
				}
			} else if($win.data.currentSlide == 3) {
				this.disabled = true;
				$backBtn.attr('disabled', 'disabled');
				$win.loadingOn();
				name = $(':text[name=name]', $win.contentBox).val();
				url = $(':text[name=url]', $win.contentBox).val();
				template = $(':radio[name=tid]:checked', $win.contentBox).val();
				wizCreatePage(name, url, template, $win.name);
				$win.beforeClose = function(){}; //Remove "You sure" dialog
				return false;
			}
			$('.horizSlider', $win.contentBox).animate({marginLeft: nr},500);
			
		});
		$(':button:eq(1)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()),$nextBtn = $(':button:eq(0)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide-1;
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 4');
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

function wizCreatePage(name,url,tid, wName) {
	$.post('fragments/pagewizard.php', {name: name, url:url, tid:tid}, function(data) {
		if(data.status == "ok") {
			$win = ice.Manager.getWindow(wName);
			$win.loadingOff();
			$('.horizSlider', $win.contentBox).animate({marginLeft: 0-(3*418)},500);
			$('.wizEditBtn', $win.contentBox).click(function() {
				ice.fragment.load('browser',{}, {url: data.path, postEdit: true});
			});
			try{ice.Manager.getWindow('IcePM').refresh();} catch(e){}
		} else {
			alert(data.error);
		}
	}, 'json');
}

</script>

<script type="text/x-template" id="pageWizContent">

<div class="winpadd pageWiz" style="max-height: 500px;">
	<div class="viewPort rounded6">
	<form method="post" action="fragments/pagewizard.php">
		<ul class="horizSlider">
			<li class="horizSlide">
				<p class="enlight">
					This wizard will help you create a new page based on a template. To begin, please choose a
					template in the list below.
				</p> 
				<table>
					<thead>
						<tr>
							<td> </td>
							<td>Name</td>
							<td>ID</td>
						</tr>
					</thead>
					<tbody>
						<?php 
						$sql = "SELECT id, name FROM ice_files";
						$res = $db->query($sql);
						if(!$res) {
							echo "No pages";
						} else {
							while ($row = mysql_fetch_array($res)) {
								$n = $row['name'];
								$id = $row['id'];
								echo "<tr><td><input type=\"radio\" name=\"tid\" value=\"$id\" /></td><td>$n</td><td>$id</td></tr>";
							}
						}
						$db->close();
						?>
					</tbody>
				</table>
				
			</li>
			<li class="horizSlide">
				<p class="enlight">Please choose an appropriate name for this page.
					The name is used backend to help you distinguish between different pages.</p>
				<label for="name">Name: <input name="name" type="text" style="width: 285px;" /> </label>
				<p class="enlight"> Complete the url to the page below. This will be the url used to navigate to the page.</p>
				<label for="url"><?php echo $config['baseurl']; ?><br />
				<input name="url" type="text" style="width: 285px; margin-left: 70px"/> </label>
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
					<p>You have successfully created a new page. You should click the first button below to add content now.</p>
					<br /> <br /> <br />
					<div class="expBtn rounded6 wizEditBtn">
						<p class="big">Edit your page</p>
						<p class="small">It's currently empty!</p>
					</div>
					<div onclick="ice.fragment.load('pagemanager')" class="expBtn rounded6">
						<p class="big">Open Page Manager</p>
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