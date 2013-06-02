;
/**
 * Widgets Manager
 * 
 * 
 */
define([
	'lib/lodash',
	'lib/bonzo',
], function(_, bonzo) {
	
	function WidgetManager(config) {
		/**
		 * @var array List of widgets.
		 */
		this.widgets = [];
		
		/**
		 * @var boolean On/Off flag.
		 */
		this.isActive = false;
		
		this.config = config;
	};

	WidgetManager.prototype.init = function() {
		// create widget objects from config
		for (i in this.config.widgets) {
			// create instance of widget using class name (first element in array) and config (second element in array)
			this.widgets.push(new this.config.widgetClasses[this.config.widgets[i][0]](this.config.widgets[i][1]));
		}
		var b = bonzo(window.document.getElementById('dashboard'));
		for (i in this.widgets) {
			b.append(this.widgets[i].el);
		}
	};

	WidgetManager.prototype.start = function() {
		this.isActive = true;
		_.each(this.widgets, function(widget) {
			this.activateWidget(widget)
		}, this);
	}

	WidgetManager.prototype.activateWidget = function(widget) {
		if (this.isActive) {
			widget.start();
		}
	}

	/**
	 * @var object Default options.
	 */
	var options = {
			conf : 'demo',
			url : '',
			callback : function(widget) {
			},
			widgetGridWrapper: 'dic class="grid-item"></div>',
			widgetWrapper : '<div class="widget"></div>',
			template : '{{body}}',
			defaults : {
				size : 'span6'
			}
	};

	var defaultWidgetConfig = {
		tick : 60
	};

	function init(opt) {
		$.extend(options, opt);
		$.sub('widget:render', onWidgetRender);
	}

	function start() {
		isActive = true;
		$.each(widgets, function() {
			activateWidget(this);
		});
	}

	function stop() {
		isActive = false;
		$.each(widgets, function() {
			this.stop();
		});
	}

	function onWidgetRender(widget) {
		options.callback(widget);
	}

	function addWidget(widget) {
		widgets.push(widget);
		this.append(widget.el);
		activateWidget(widget);
	}

	function addWidgets(newWidgets) {
		m = this;
		$.each(newWidgets, function() {
			addWidget.apply(m, [ this ]);
		});
	}

	

	function reloadWidget(widget) {
		if (!isActive) {
			return;
		}

		var widgetConfig = widget.data('config');
		delete widgetConfig.body;
		delete widgetConfig.template;

		$.ajax({
			url : options.url,
			processData : true,
			data : {
				conf : options.conf,
				widget : widgetConfig
			},
			type : 'POST',
			dataType : 'json',
			context : widget,
			success : function(body) {
				renderWidgetJson(this, body);
				options.callback(this);
				activateWidget(this);
			},
			error : function() {
				widgetConfig.body = 'JSON Error';
				renderWidgetJson(this, widgetConfig);
				activateWidget(this);
			}
		});
	}

	/**
	 * Renders widget.
	 * 
	 * @param {object}
	 *            widget
	 * @param {string}
	 *            body
	 */
	function renderWidget(widget, body) {
		if (!body) {
			widget.addClass('hidden');
		} else {
			widget.removeClass('hidden');
			widget.html(body);
		}
	}

	function renderWidgetJson(widget, json) {
		if (!json) {
			renderWidget(widget, 'JSON ERROR');
			return;
		}
		if (!json.body && !json.template) {
			renderWidget(widget, '');
			return;
		}
		var tpl = json.template || options.template;
		var body = $.mustache(tpl, json);
		renderWidget(widget, body);
	}
	
	return WidgetManager;
});
