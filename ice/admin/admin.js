'use strict';
var ice = {
	subscriptions: {},

	ready : function() {
		this.Manager.taskBar = $('#taskBar ul');
		this.Manager.windowSandbox = $('#windowSandbox');
		if(!('console' in window)) {
			window.console = {log:function(m){ice.message(m,'info');}}
		}
		
		ice.subscribe('sidepanel:fragment/load', function() {
			if (window.location.hash.indexOf('#!') === 0) {
				$.map(window.location.hash.substr(2).split(','), function(val, i){
					ice.fragment.load(val);
				});
			}
		});

		ice.subscribe('ice:message/create', function(message, type, loc) {
			ice.message(message, type, loc);
		});

		ice.subscribe('ice:auth/login', function() {
			ice.curtain.raise(false);
			ice.fragment.load('sidepanel');
		});

		ice.subscribe('ice:auth/logout', function() {
			ice.curtain.lower(false);
		});

	},

	Manager : {
		windowsStorage : {},
		incrementer : 0,
		displayNoWindowsWarning: true,
		windowSandbox : {},
		taskBar : {},

		
		getWindow : function(name) {
			if( name in this.windowsStorage) {
				return this.windowsStorage[name];
			} else {
				return false;
			}
		},
		addWindow : function(win) {//First argument is a windowClass object.
			if(this.displayNoWindowsWarning) {
				this.windowSandbox.find("div:has(img)").html("");
				this.displayNoWindowsWarning = false;
			}//Ensure empty canvas
			var name = win.name;
			if(name.length < 1) {//Anonymous windows
				name = "anon_" + this.incrementer++;
				win.name = name;
			}
			if( name in this.windowsStorage === false) {//Prevent duplicates.
				this.windowsStorage[name] = win;
			} else {
				//Move requested window to front
				this.windowsStorage[name].element.css({zIndex:this.maxZindex()});
				console.log("ID already in use: " + name);
				return false;
			}

			this.renderWindow(name);
			return true;
		},
		renderWindow : function(name) {
			var $win = this.getWindow(name);
			if($win.closeable === false) {
				$win.exitBtn.remove();
			} else {
				$win.exitBtn.click(function() {
					ice.Manager.removeWindow(name);
				});
			}
			if($win.allowRefresh) {
				$win.refreshBtn
					.css({display:'block'})
					.click(function() {
						ice.Manager.getWindow($(this).inWindow()).refresh();
					});
			}
			if($win.icon !== "") {
				$win.titleBox.css({
					background : 'url("resources/' + $win.icon + '") 5px  no-repeat'
				});
			}

			if($win.minimizeable) {
				$win.element.find('.winBar').dblclick(function() {
					ice.Manager.minimizeWindow($(this).inWindow());
				});
				$win.element.find('.winMini').click(function() {
					ice.Manager.minimizeWindow($(this).inWindow());
				});
			} else {
				$win.element.find('.winMini').remove();
			}
			
			$win.element
				.attr('data-win-name', name)
				.appendTo(this.windowSandbox);
				
				
			$win.element.css({
				width : $win.width,
				left : (this.windowSandbox.width() - $win.width) / 2,
				zIndex : ice.Manager.maxZindex(),
				top : 51
			}).draggable({
				handle : '.winBar',
				stack : '.window',
				containment : 'parent'
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
			ice.publish("ice:window/open", [$win]);
			ice.publish($win.name + ":window/open", [$win]);

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
				$win = null;

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

			for(var x in $win.handles) {
				ice.unsubscribe($win.handles[x]);
			}
			
			delete this.windowsStorage[name];
			return true;
		},
		flushWindows : function(callback) {
			for(var n in this.windowsStorage) {
				if(!this.removeWindow(n)) {
					return false;
				}
			}
			
			this.windowsStorage = {};
			
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
		this.element = $('<div class="window rounded6 shadow" />').html('<div class="winBorder rounded6"><div class="winBar"><div class="winTitle"></div><div class="winExit"></div><div class="winMini"></div><div class="winRefresh"></div></div><div class="winContent"></div><div class="winLoader" ></div></div>');
		this.exitBtn = this.element.find('.winExit:eq(0)');
		this.refreshBtn = this.element.find('.winRefresh:eq(0)');
		this.titleBox = this.element.find('.winTitle:eq(0)');
		this.contentBox = this.element.find('.winContent:eq(0)');
		this.loader = this.element.find('.winLoader');
		this.contentEndpoint = "";
		this.allowRefresh = false;
		this.handles = [];

		this.refresh = function(attrs) {
			if(this.contentEndpoint === "") {return false;}
			if(typeof attrs == 'undefined') {
				attrs = {refresh:true};
			}
			var ref = this;
			this.loadingOn();
			$.post(this.contentEndpoint, attrs, function(data) {
				if(data.length === 0 || data == "404") {
					ice.message('Ajax error');
				} else {
					ref.setContent(data);
					ref.loadingOff();
				}
			})
		}
		
		this.setContent = function(c) {
			this.contentBox.children().remove();
			this.contentBox.html(c);
			this.onContentChange(this);
		};
		this.loadingOn = function() {
			this.loader.stop().fadeIn();
			this.contentBox.css('-webkit-filter','blur(2px)');
		};
		this.loadingOff = function() {
			this.loader.stop().fadeOut();
			this.contentBox.css('-webkit-filter','none');
		};
		this.onOpen = function(winObj) {
		};
		this.beforeClose = function(winObj) {
		};
		this.onContentChange = function(winObj) {
		};
		this.handle = function(handle) {
			this.handles.push(handle);
		};
	},
	fragment : {
		usedCss : [],
		load : function(fragmentName, postData, fnattrs, callback) {
			if(fragmentName in window) {
				window[fragmentName](fnattrs);
				try {callback(fragmentName, true);
				} catch(e) {
				}
			} else {
				$(document.body).addClass('loading');
				$.post("fragments/" + fragmentName + ".php", postData, function(data) {
					var k = $('<div>');
					k.html(data).appendTo('body');
					$(document.body).removeClass('loading');
					window[fragmentName](fnattrs);
					try {callback(fragmentName, true);
					} catch(e) {}
				});
			}
			ice.publish("ice:fragment/load", [fragmentName]);
			ice.publish(fragmentName + ":fragment/load", [fragmentName]);
		},
		get : function(fragmentName, win, postData, callback) {
			$.post("fragments/" + fragmentName + ".php", postData, function(data) {
				callback(win, data);
			});
		},
		addCss : function(filename) {
			if (filename in this.usedCss) {
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
		var l, target = (typeof customloc === 'undefined') ? this.Manager.windowSandbox:customloc;
		if(type == "warning" || type === 'undefined') {
			l = $('<div class="msg msgWarning"> <div class="winExit"></div> </div>');
		} else if(type == "info") {
			l = $('<div class="msg msgInfo"> <div class="winExit"></div> </div>');
		}
		l.html('<p>'+message+'</p>' + l.html());
		l.find('.winExit').click(function() {
			$(this).parent().slideUp(500, function() {
				$(this).remove();
			});
		});
		
		//Only display identical messages once
		var duplicate = $(":contains('" + message + "')", target);
		if(duplicate.size()) {
			duplicate.fadeOut(100).fadeIn(200);
			return;
		}

		l.appendTo(target);
		l.fadeIn();
		ice.publish('ice:message/new', [message,type,customloc]);
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
					ice.publish("ice:auth/logout");
				});
			});
		}
	}, //End logout()
	curtain : {
		lower : function(now) {
			if(now === true) {
				$('#header').css({height:"100%", zIndex:88888});
				$('#header .center').css({marginTop:100});
			} else {
				$('#header').animate({height:"100%"},800).css({zIndex:88888});
				$('#header .center').delay(400).animate({marginTop:100}, 400);
			}
			ice.publish("ice:curtain/lower",[now]);
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
			ice.publish("ice:curtain/raise", [now]);
		}
	}, //End curtain
	/**
	 * Takes a string and applies a simple shift chiper
	 * @param String str The string to be shifted
	 * @param String pin A string of numbers to use as shift key.
	 * @return String decodedKey
	 */
	decodeKey : function(str, pin) {
		var pinNums = [],
			out = [],
			i = 0,
			strlen = str.length,
			pinlen = pin.length;
			
		/*
		 * Create an array(pinNums) of the numbers in the input string.
		 * The numbers are modified to depend on the other numbers,
		 * to ensure greater difference in result when there is a slight
		 * difference in the pin.
		 */
		for(i = 0; i < pinlen; i++) {
			pinNums[i] = parseInt(pin[i], 10);
			if(i > 0) {
				pinNums[i] = pinNums[i] + ((i * pinNums[i-1]) % 10);
			}
		}

		for(i = 0; i < strlen; i++) {

			out.push(
				String.fromCharCode(
					str.charCodeAt(i) + pinNums[i % pinlen]
				) 
			);
		}

		return out.join("");

	},

	// PubSub system
	// Adaption of https://github.com/daniellmb/MinPubSub/
	publish: function(topic, args){
		var handlers = this.subscriptions[topic],
			hlen = handlers ? handlers.length : 0;

		while (hlen--) {
			handlers[hlen].apply(this, args || []);
		}
		console ? console.log("Published " + args + " to " + topic):null;
	},
	subscribe: function(topic, callback) {
		if (!(topic in this.subscriptions)) {
			this.subscriptions[topic] = [];
		}

		this.subscriptions[topic].push(callback);
		return [topic, callback];
	},
	unsubscribe: function(handle, callback) {
		var handlers = this.subscriptions[callback ? handle : handle[0]],
			callback = callback || handle[1],
			len = handlers ? handlers.length : 0;

		while(len--) {
			if(handlers[len] === callback){
				handlers.splice(len,1);
			}
		}
	}
};
//End ice

$.fn.inWindow = function() {
	return this.eq(0).closest('.window').attr('data-win-name');
};
