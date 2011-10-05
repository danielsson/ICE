<?php
	define('SYSINIT',true);
	require '../../ice-config.php';
	require '../../lib/db.class.php';
	require '../../lib/auth.class.php';
	$Auth->init(2);
	$db->connect();
	if(!empty($_POST['name'])) {
		$data = Array('status'=>'ok', 'error'=>'none');
		$_POST = $Auth->sanitize($_POST);
		$name = addslashes($_POST['name']);
		$tid = intval($_POST['tid']);
		$tmp = parse_url($config['baseurl'] . $_POST['url']);
		$url = addslashes($tmp['path']);
		$sql = "INSERT INTO ice_pages (name,tid,url) VALUES ('$name', '$tid', '$url');";
		$db->query($sql);
		if($db->error() != "") {
			$data = Array('status'=>'error', 'error'=>$db->error());
		}
		$db->close();
		$data['path'] = $tmp['path'];
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
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 5');
			var nr = 0 - ($win.data.currentSlide * 418);
			if($win.data.currentSlide != 4)  {
				$backBtn.removeAttr('disabled');
				this.disabled = false;
			} else {
				this.disabled = true;
			}
			if($win.data.currentSlide == 3) {
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
			} else if($win.data.currentSlide == 4) {
				this.disabled = true;
				$backBtn.attr('disabled', 'disabled');
				$win.loadingOn();
				name = $(':text[name=name]', $win.contentBox).val();
				url = $(':text[name=url]', $win.contentBox).val();
				template = $(':radio[name=tid]:checked', $win.contentBox).val();
				wizCreatePage(name, url, template, $win.name);
				$win.beforeClose = function(p){}; //Remove "You sure" dialog
				return false;
			}
			$('.horizSlider', $win.contentBox).animate({marginLeft: nr},500);
			
		});
		$(':button:eq(1)', win.contentBox).click(function(e) {
			var $this = $(this), $win = ice.Manager.getWindow($this.inWindow()),$nextBtn = $(':button:eq(0)', $win.contentBox);
			$win.data.currentSlide = $win.data.currentSlide-1;
			$win.titleBox.text('New page wizard - Step ' + ($win.data.currentSlide + 1) + ' of 5');
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
			$('.horizSlider', $win.contentBox).animate({marginLeft: 0-(4*418)},500);
			$('.wizEditBtn', $win.contentBox).click(function() {
				ice.fragment.load('browser',{}, {url: data.path, postEdit: true});
			});
		} else {
			alert(data.error);
		}
	}, 'json');
}

</script>

<script type="text/template" id="pageWizContent">

<div class="winpadd pageWiz">
	<div class="viewPort rounded6">
	<form method="post" action="fragments/pagewizard.php">
		<ul class="horizSlider">
			<li class="horizSlide">
				<div class="winpadd">
					<p>
					This wizard will help you create a new page based on a template. To begin, please choose a
					template in the list below.
					</p> 
				</div>
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
				<div class="winpadd">
					<p>Please choose an appropriate name for this page. The name is used backend to help you distinguish between different pages.</p><br /> <br />
					<label for="name">Name <input name="name" type="text" style="width: 285px;" /> </label>
				</div>
			</li>
			<li class="horizSlide">
				<div class="winpadd">
					<p> Complete the url to the page below. This will be the url used to navigate to the page.</p><br /> <br />
					<label for="url"><?php echo $config['baseurl']; ?> <input name="url" type="text" style="width:320px;"/> </label>
				</div>
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