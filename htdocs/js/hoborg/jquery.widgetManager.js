(function ( $ ){

	var widgets = [];
	var isActive = false;
	var defaultWidgetConfig = {
		tick: 60000
	};

	var methods = {
		init : init,
		addWidget : addWidget,
		start: start,
		stop: stop
	};

	function init(options) {
		console.log(options);
	}

	function start() {
		isActive = true;
		$.each(widgets, function() { activateWidget(this); });
	}

	function stop() {
		isActive = false;
	}

	function addWidget(widget) {
		widgets.push(widget);
		activateWidget(widget);
	}

	function activateWidget(widget) {
		if (isActive) {
			widgetConfig = $.extend(defaultWidgetConfig, widget.data('config'));
			var t = setTimeout(function() { reloadWidget(widget); }, widgetConfig.tick);
		}
	}

	function reloadWidget(widget) {
		console.log(widget, widget.data('config'));
		var body = '';

		// get new body
		$.ajax({
			url: '/ajax-widget.php',
			data: {c: 'demo', i: 0},
			type: 'GET',
			context: widget,
			success: function(body) { renderWidget(this, body); activateWidget(this); }
		});
	}

	function renderWidget(widget, body) {
		if (!body) {
			widget.hide();
		} else {
			widget.show();
			widget.html(body);
		}
	}


	$.fn.widgetManager = function(method) {
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply(this, arguments);
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.widgetManager' );
		}  
	};

})(jQuery);
