
define([], function() {
	
	var exports = {};

	// Detect that we are at bottom of page, and call autoloading function
	var killScroll = false;

	var handlers = [];

	var myWidth = 0,
		myHeight = 0;

	if (typeof(window.innerWidth) == 'number') {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	}

	window.onscroll = function(e) {
		if (killScroll) {
			return;
		}

		detectPageEnd(e);
	};

	function detectPageEnd(e) {
		var scrolledtonum = window.pageYOffset + myHeight + 200;
		var heightofbody = document.body.offsetHeight;

		if (scrolledtonum >= heightofbody) {
			if (e) {
				e.preventDefault && e.preventDefault();
				e.stopPropagation && e.stopPropagation();
				e.stopImmediatePropagation && e.stopImmediatePropagation();
				e.returnValue = false;
			}
			killScroll = true;
			runHandlers();
		}
	}

	function runHandlers() {
		for (var i in handlers) {
			handlers[i]();
		}
	}

	exports.addHandler = function(handler) {
		handlers.push(handler);
	}

	exports.done = function() {
		killScroll = false;
	}

	exports.stop = function() {
		window.onscroll = function() {};
	}

	exports.start = function() {
		detectPageEnd();
	}

	return exports;
});
