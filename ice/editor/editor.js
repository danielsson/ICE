var bubbles = Array(), $iceMessage;
// title, url/text, isimage, command, argument, allowedOnField
var toolbarButtons = [['Bold', '<b>B</b>', false, 'bold', '', true], ['Italic', '<i>I</i>', false, 'italic', '', true], ['Underline', '<u>U</u>', false, 'underline', '', true], ['Strike through', '<del>S</del>', false, 'strikeThrough', '', true], ['Divider', '', false, '', '', false], ['Heading 1', 'text_heading_1.png', true, 'formatBlock', '<H1>', false], ['Heading 2', 'text_heading_2.png', true, 'formatBlock', '<H2>', false], ['Heading 3', 'text_heading_3.png', true, 'formatBlock', '<H3>', false], ['Heading 4', 'text_heading_4.png', true, 'formatBlock', '<H4>', false], ['Divider', '', false, '', '', false], ['Justify left', 'text_align_left.png', true, 'justifyLeft', '', false], ['Justify Center', 'text_align_center.png', true, 'justifyCenter', '', false], ['Justify Right', 'text_align_right.png', true, 'justifyRight', '', false], ['Divider', '', false, '', '', false], ['Undo Ctrl+Z', 'arrow_undo.png', true, 'undo', '', false], ['Redo Ctrl+Y', 'arrow_redo.png', true, 'redo', '', false], ['Br', '', false, '', '', true], ['Insert unordered list', 'text_list_bullets.png', true, 'insertUnorderedList', '', false], ['Insert ordered list', 'text_list_numbers.png', true, 'insertOrderedList', '', false], ['Toggle superscript', 'text_superscript.png', true, 'superscript', '', false], ['Toggle subscript', 'text_subscript.png', true, 'subscript', '', false], ['Divider', '', false, '', '', false], ['Insert Horizontal Rule', '&mdash;', false, 'insertHorizontalRule', '', false], ['Insert Link', 'link_add.png', true, 'createLink', '', false], ['Remove Link', 'link_delete.png', true, 'unLink', '', false], ['Add image', 'image_add.png', true, 'insertImage', '', false], ['Divider', '', false, '', '', false], ['Indent text', 'text_indent.png', true, 'indent', '', false], ['Outdent text', 'text_indent_remove.png', true, 'outdent', '', false], ['Insert blockquote', '"', false, 'formatBlock', 'blockquote', false], ['Divider', '', false, '', '', false], ['Edit HTML', 'html.png', true, 'editHTML', '', false], ['Remove Formatting', 'css_delete.png', true, 'removeFormat', '', false], ['floatRight', '', false, '', '', true], ['Cancel editing', 'cross.png', true, 'cancel', '', true], ['Save', 'page_save.png', true, 'save', '', true], ['endFloat', '', false, '', '', true]];

var icePopUp = function(u) {
	var iFrame,
		$el
		url = u
		win = window;
	
	this.payload = {}; //For sending params to iframe
	
	this.create = function() {
		$('.iceOverlay').fadeIn();
		$el = $('<div class="iceFloatWin" id>').html('<div class="iceRounded" id="iceImageEditor"><iframe width="800" height="450"></iframe></div>').appendTo('body');
		iFrame = $el.find('iframe');
		iFrame.attr("src", iceBasePath + u);
		var thisPopup = this;
		iFrame.get()[0].onload = function() {
			this.contentDocument.popup = thisPopup;
			if('_ready' in this.contentDocument) {
				this.contentDocument._ready();
			}
		};
		
	};
	
	this.destroy = function() {
		$el.remove();
		$('.iceOverlay').fadeOut();
	};
	
	this.exec = function(fn,arg) {
		fn.call(win,arg);
	}
	
}

var iceEditorClass = function() {
	this.element = $('<div class="ice iceEditor iceRounded iceShadow" id="editor" />').html('<div class="iceEditorHead">ICE</div><div class="iceEditorToolbar"></div><div class="iceEditorToolbarFloat"></div>'), this.objTarget = null;this.head = this.element.children('.iceEditorHead'), this.toolbar = this.element.children('.iceEditorToolbar'), this.oldHTML = "";
	this.htmlEditor = null;
	this.popUps = {};
	
	this.renderToolbar = function() {
		var cache = '',
			isField = this.objTarget.hasClass('iceArea');

		for(var i = 0; i < toolbarButtons.length; i++) {
			var k, btn = toolbarButtons[i];
			if(btn[5] == false
				&& isField == false) { //If this is a field, skip area controllers
				continue;
			}
			switch (btn[0]) {
				case 'Divider':
					cache = cache + '<div class="iceEditorToolbarDivider" ></div>';
					continue;
				case 'Br':
					cache = cache + '<br />';
					continue;
				case 'floatRight':
					cache = cache + '<div style="float:right">';
					continue;
				case 'endFloat':
					cache = cache + '</div>';
					continue;
			}
			k = '<a href="#" title="' + btn[0] + '" class="iceEditorToolbarButton" data-cmd="' + btn[3] + '" data-arg="' + btn[4] + '" >';
			if(btn[2] == true) {
				k = k + '<img src="' + iceBasePath + '/editor/res/' + btn[1] + '" />';
			} else {
				k = k + btn[1];
			}
			k = k + '</a>';
			cache = cache + k;
			k = null;
		}
		this.toolbar.html(cache);
		cache = null;
	};

	this.render = function() {

		if(this.objTarget.hasClass('iceImage')) { //If this is an image element, dont render toolbar 
			var mediaManager = new icePopUp('editor/imageexplorer.php');
			mediaManager.payload.isTypeImage = true;
			mediaManager.create();
			return;
		}
		
		var off = this.objTarget.offset();
		this.oldHTML = this.objTarget.html();
		this.head.html('ICE! <span>- editing ' + this.objTarget.attr('data-ice-fieldname') + '</span>');
		var offTop = off.top - 105;
		if(offTop < 0) {//Render under the element if there isn't enough space
			offTop = off.top + this.objTarget.height() + 20;
		}
		this.element.css({
			left : off.left,
			top : offTop,
			minWidth : this.objTarget.width()
		}).appendTo('body').draggable({
			snap : this.objTarget,
			handle : '.iceEditorHead',
			scroll : true
		});
		this.objTarget.attr('contentEditable', true);
		this.renderToolbar();
		this.toolbar.find('a').click(this.toolbarButtonClicked);
		this.element.fadeIn();
		if(!this.objTarget.hasClass('iceArea')) {
			this.objTarget.keypress(function(event) {
				// Cancel if enter is pressed
				return event.which != 13;
			});
		}
	};
	this.save = function(q) {
		this.element.animate({
			opacity : 0
		}, 300, function() {
			$(this).remove();
		});
		iceEdit.objTarget.attr('contentEditable', false).removeClass('icemarked');
		var text = q || iceEdit.objTarget.html();
		var fieldname = iceEdit.objTarget.attr('data-ice-fieldname');
		$.post(iceBasePath + 'editor/endpoint.php', {
			text : text,
			fieldname : fieldname,
			pagename : icePageName
		}, function(data) {
			if(data.status != 'success') {
				if(data.error == "auth") {
					alert('You are not authenticated. Please login in a new window then try to save the element again.')
				} else {
					alert(data.error);
				}
			}
		}, 'json');
		renderEditBubbles();
		iceEdit = null;
		iceEdit = new iceEditorClass();
	};
	this.saveImage = function(url) {
		var fieldname = iceEdit.objTarget.attr('data-ice-fieldname'),
			w = iceEdit.objTarget.attr('width'),
			h = iceEdit.objTarget.attr('height');

		$.post(iceBasePath + 'editor/endpoint.php', {
			url:url,
			fieldname: fieldname,
			pagename: icePageName,
			h: h,
			w: w
		}, function(data){
			if(data.status != 'success') {
				if(data.error == "auth") {
					alert('You are not authenticated. Please login in a new window then try to save the element again.')
				} else {
					alert(data.error);
				}
			} else {
				iceEdit.objTarget.attr('src', data.url);
			}

			iceEdit.objTarget.removeClass('icemarked');
			renderEditBubbles();
			iceEdit = null;
			iceEdit = new iceEditorClass();

		}, 'json');

	};
	this.cancel = function() {
		this.element.animate({
			opacity : 0
		}, 300, function() {
			$(this).remove();
		});
		this.objTarget.attr('contentEditable', false).html(this.oldHTML).removeClass('icemarked');

		renderEditBubbles();
		iceEdit = null;
		iceEdit = new iceEditorClass;

	};

	this.toolbarButtonClicked = function(e) {
		e.preventDefault();
		$this = $(this);
		var cmd = $this.attr('data-cmd'), arg = $this.attr('data-arg');
		switch(cmd) {
			case 'save':
				iceEdit.save();
				return;
				break;
			case 'cancel':
				iceEdit.cancel();
				return;
				break;
			case 'editHTML':
				var htmlEditor = new icePopUp('editor/htmleditor.php');
				
				htmlEditor.payload.html = iceEdit.objTarget.html();
				
				htmlEditor.create();
				return;
			case 'createLink':
				iceEdit.getSelection()
				if(iceEdit.getSelection().length < 1) {
					alert('You must select the text you want to make a link.');
					return;
				}
				arg = prompt('Please enter the desired url for the link:', 'http://');
				break;
			case 'insertImage':
				var imageManager = new icePopUp('editor/imageexplorer.php');
				imageManager.create();
				return;
				break;
		}
		document.execCommand(cmd, false, arg);
		iceEdit.objTarget.focus();
		return;
	};
	this.getSelection = function() {
		var txt = '';
		if(window.getSelection) {
			txt = window.getSelection();
		} else if(document.getSelection) {
			txt = document.getSelection();
		} else if(document.selection) {
			txt = document.selection.createRange().text;
		}
		return txt.toString();
	};
};
var iceEdit = new iceEditorClass();
function renderEditorOnObject($objTarget) {
	removeEditBubbles();
	$objTarget.addClass('icemarked');
	iceEdit.objTarget = $objTarget;
	iceEdit.render();
}

function renderEditBubbles() {
	$('.iceEditable').each(function() {
		var $this = $(this), off = $this.position();
		var $bubble = $('<div> <span>Edit</span> </div>');
		bubbles.push($bubble);
		$bubble.addClass('ice icePointer').css({
			left : off.left - 70,
			top : off.top
		}).appendTo('body').click(function() {renderEditorOnObject($this);
		}).hover(function() {
			$this.addClass('icemarked');
		}, function() {
			$this.removeClass('icemarked');
		});
	});
}

function removeEditBubbles() {
	for(var i = 0; i < bubbles.length; i++) {
		bubbles[i].remove();
	}
	bubbles = [];
}


$(window).load(function() {
	setTimeout(function() {
		$('<div class="iceOverlay" />').appendTo('body');
		renderEditBubbles();
		if($.browser.msie) {
			$.getScript('http://ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js', function() {
				CFInstall.check({
					mode : "overlay"
				});
			});
		}
	}, 500);
});
