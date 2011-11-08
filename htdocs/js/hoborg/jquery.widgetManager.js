(function ( $ ){

	/**
	 * @var array List of widgets.
	 **/
	var widgets = [];

	/**
	 * @var boolean On/Off flag.
	 **/
	var isActive = false;

	/**
	 * @var object Default options.
	 */
	var options = {
		callback: function(widget) {},
		conf: 'demo'
	};

	var defaultWidgetConfig = {
		tick: 60000
	};

	/**
	 * var object List of public methods.
	 */
	var methods = {
		init : init,
		addWidget : addWidget,
		start: start,
		stop: stop
	};

	function init(opt) {
		$.extend(options, opt);
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
		if (!isActive) {
			return;
		}

		var widgetConfig = widget.data('config');
		

   		$.ajax({
   		    url: '/ajax-widget.php',
   			data: {c: options.conf, widget: widgetConfig},
   			type: 'GET',
   			dataTypeString: 'json',
   			context: widget,
  			success: function(body) { renderWidget(this, body); activateWidget(this); }
  		});

	}

	function renderWidget(widget, body) {
		if (!body) {
			widget.addClass('hidden');
		} else {
			widget.removeClass('hidden');
			widget.html(body);
		}
		options.callback(widget);
	}
	
	function renderWidgetJson(widget, json) {
	    if (!json.body) {
	        renderWidget(widget, json.body);
	    }
	}


	// register plugin
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
