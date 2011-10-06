var ice = {
	Manager : {
		windowsStorage : [],
		incrementer : 0,
		windowSandbox : {},
		taskBar : {},
		ready : function() {
			this.taskBar = $('#taskBar ul');
			this.windowSandbox = $('#windowSandbox');

		},
		getWindow : function(name) {
			if( name in this.windowsStorage) {
				return this.windowsStorage[name];
			} else {
				return false;
			}
		},
		addWindow : function(window) {//First argument is a windowClass object.
			if(Object.keys(this.windowsStorage).length == 0) {
				this.windowSandbox.find("div:has(img)").html("");
			}//Ensure empty canvas
			console.log(Object.keys(this.windowsStorage).length);
			var name = window.name;
			if(name.length < 1) {//Anonymous windows
				name = "anon_" + this.incrementer++;
				window.name = name;
			}
			if( name in this.windowsStorage === false) {//Prevent duplicates.
				this.windowsStorage[name] = window;
			} else {
				return false;
			}

			this.renderWindow(name);
			return true;
		},
		renderWindow : function(name) {
			$win = this.getWindow(name);
			if($win.closeable === false) {
				$win.exitBtn.remove();
			} else {
				$win.exitBtn.click(function() {
					ice.Manager.removeWindow(name);
				});
			}
			if($win.icon !== "") {
				$win.titleBox.css({
					background : 'url("resources/' + $win.icon + '") 5px  no-repeat'
				});
			}

			if($win.minimizeable) {
				$win.element.find('.winBar').dblclick(function() {
					var d = $(this).closest('.window').attr('data-win-name');
					ice.Manager.minimizeWindow(d);
				});
				$win.element.find('.winMini').click(function() {
					var d = $(this).closest('.window').attr('data-win-name');
					ice.Manager.minimizeWindow(d);
				});
			} else {
				$win.element.find('.winMini').remove();
			}

			$win.element.attr('data-win-name', name).appendTo(this.windowSandbox).css({
				width : $win.width,
				left : (this.windowSandbox.width() - $win.width) / 2,
				zIndex : ice.Manager.maxZindex(),
				top : (this.windowSandbox.height() - $win.element.height()) / 2
			}).draggable({
				handle : '.winBar',
				stack : '.window'
			});
			$win.titleBox.html($win.title);

			var taskItem = $('<li>');
			taskItem.attr('data-win-name', name).html($win.title).click(function() {
				var el = ice.Manager.getWindow($(this).attr('data-win-name')).element;
				if(el.css('display') == "none") {
					el.fadeIn('fast');
				}
				el.css({
					zIndex : ice.Manager.maxZindex()
				});

			});
			if($win.icon !== "") {
				taskItem.css({
					background : 'url("resources/' + $win.icon + '") 5px 5px no-repeat'
				});
			}

			taskItem.appendTo(this.taskBar);
			$win.onOpen($win);

		},
		minimizeWindow : function(name) {
			this.getWindow(name).element.fadeOut('fast');
		},
		removeWindow : function(name) {

			var $win = this.getWindow(name);
			if($win.beforeClose($win) === false) {
				return false;
			}
			$win.element.animate({
				opacity : 0
			}, 300, function() {
				$(this).remove();
			});
			var tmp = $('li[data-win-name=' + name + ']', this.taskBar);
			tmp.css({
				width : tmp.width(),
				minWidth : 0
			}).animate({
				width : 0,
				paddingLeft : 0,
				paddingRight : 0,
				opacity : 0
			}, 800, function() {
				$(this).remove();
			});
			delete this.windowsStorage[name];
			return true;
		},
		flushWindows : function(callback) {
			for(n in this.windowsStorage) {
				if(!this.removeWindow(n)) {
					return false;
				}
			}
			this.windowsStorage = [];
			//reset, just in case
			if( typeof callback == 'function') {
				callback();
			}
		},
		maxZindex : function() {
			var max = 0;
			$('.window', this.windowSandbox).each(function() {
				max = Math.max(this.style.zIndex, max);
			});
			return max + 1;
		}
	},

	Window : function() {
		this.name = "";
		this.width = 200;
		this.closeable = true;
		this.minimizeable = true;
		this.title = "Window title";
		this.icon = "";
		this.modal = false;
		this.element = $('<div class="window rounded6 shadow" />').html('<div class="winBorder rounded6"><div class="winBar"><div class="winTitle"></div><div class="winExit"></div><div class="winMini"></div></div><div class="winContent"></div><div class="winLoader" ></div></div>');
		this.exitBtn = this.element.find('.winExit');
		this.titleBox = this.element.find('.winTitle');
		this.contentBox = this.element.find('.winContent');
		this.loader = this.element.find('.winloader');
		this.setContent = function(c) {
			this.contentBox.html(c);
		};
		this.loadingOn = function() {
			this.loader.fadeIn();
		};
		this.loadingOff = function() {
			this.loader.stop().fadeOut();
		};
		this.onOpen = function(winObj) {
		};
		this.beforeClose = function(winObj) {
		};
	},
	fragment : {
		usedCss : [],
		load : function(fragmentName, postData, fnattrs, callback) {
			if(eval('window.' + fragmentName)) {
				eval(fragmentName + '(fnattrs);');
				try {callback(fragmentName, true);
				} catch(e) {
				}
			} else {
				$(document.body).addClass('loading');
				$.post("fragments/" + fragmentName + ".php", postData, function(data) {
					var k = $('<div>');
					k.html(data).appendTo('body');
					$(document.body).removeClass('loading');
					eval(fragmentName + '(fnattrs);');
				});
			}
		},
		get : function(fragmentName, win, postData, callback) {
			$.post("fragments/" + fragmentName + ".php", postData, function(data) {
				callback(win, data);
			});
		},
		addCss : function(filename) {
			if( filename in this.usedCss) {
				return false;
			}
			var head = document.getElementsByTagName("head")[0], ele = document.createElement('link');
			ele.type = 'text/css';
			ele.rel = 'stylesheet';
			ele.href = 'fragments/' + filename;
			ele.media = 'screen';
			head.appendChild(ele);
			this.usedCss[filename] = true;
			return true;
		}
	}, //End Fragment
	message : function(message, type, customloc) {
		var l;
		if(type == "warning" || type === undefined) {
			l = $('<div class="msg msgWarning"> <div class="winExit"></div> </div>');
		} else if(type == "info") {
			l = $('<div class="msg msgInfo"> <div class="winExit"></div> </div>');
		}
		l.html(message + l.html());
		l.find('.winExit').click(function() {
			$(this).parent().slideUp(500, function() {
				$(this).remove();
			});
		});
		if(!customloc) {
			l.appendTo(this.Manager.windowSandbox);
		} else {
			l.appendTo(customloc);
		}
		l.fadeIn();
	}, //End message()
	logout : function() {
		if(confirm("You sure you want to log out? All windows will be closed and unsaved work will be lost.")) {
			this.Manager.flushWindows(function() {
				$.post("fragments/login.php", {
					logout : true
				}, function(data) {
					$('#headerText').html('Not logged in.');
					$('aside').html("");
					ice.fragment.load('login');
					ice.message("Successfully logged out", 'info');
				});
			});
		}
	}, //End logout()
	curtain : {
		lower : function(now) {
			if(now === true) {
				$('#header').css({height:"100%", zIndex:88888});
				$('#header .center').css({marginTop:200});
			} else {
				$('#header').animate({height:"100%"},800).css({zIndex:88888});
				$('#header .center').delay(400).animate({marginTop:200}, 400);
			}
			
		},
		raise : function(now) {
			if(now===true) {
				$('#header .center').css({marginTop:0});
				$('#header').css({height : 48})
					.css('zIndex', 1);
			} else {
				$('#header .center').animate({marginTop:0}, 200);
				$('#header').animate({height : 48}, 500, function() {
					$(this).css('zIndex', 1);
				});
			}
			
		}
	}
};
//End ice

$.fn.inWindow = function() {
	return this.eq(0).closest('.window').attr('data-win-name');
};
