<?php
namespace Ice;
define('SYSINIT', true);

require '../ice-config.php';
require '../lib/Auth.php';

Auth::init(1);
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="../lib/codemirror.js"></script>
		<style>
			.CodeMirror-line-numbers {
				background-color: #eee;
				color: #aaa;
				font-family: monospace;
				font-size: 10pt;
				padding-right: .3em;
				padding-top: .4em;
				text-align: right;
				width: 2.2em;
			}
			body {
				background: transparent !important;
			}
		</style>
		<link href="../admin/admin.css" rel="stylesheet" type="text/css" />
		<script>
		var htmlEditor;
		document._ready = function() {
			htmlEditor = new CodeMirror(document.getElementById("iceHtmlEditorTarget"), {
				path : "../lib/",
				basefiles : ["codemirror_base.js"],
				parserfile : ["css_parser.js", "js_parser.js", "xml_parser.js", "htmlmix_parser.js"],
				lineNumbers : true,
				stylesheet : "../lib/cm_colors.css",
				content : document.popup.payload.html,
				height : "450px"
			});
			document.getElementById('btnSave').addEventListener("click",function(e) {
				document.popup.exec(function(html) {
					this.iceEdit.objTarget.html(html);
					
					}, htmlEditor.getCode());
				document.popup.destroy();
			}, false);
		}
		</script>
	</head>
	<body>
		<div id="iceHtmlEditorTarget"></div>
		<input type="button" value="Cancel" onclick="document.popup.destroy()" style="float:right"/>
		<input type="button" value="Save" id="btnSave" style="float:right"/>

</div>
	</body>
</html>
