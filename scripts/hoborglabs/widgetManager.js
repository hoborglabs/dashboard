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
	
	/**
	 * @var object Default options.
	 */
	var options = {
		conf : 'demo',
		url : '',
		callback : function(widget) {},
		template : '{{body}}',
		defaults : {
			size : 'span6'
		}
	};

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
		//this.config = _.extend(options, config);
	};

	WidgetManager.prototype.init = function() {
		// create widget objects from config
		for (i in this.config.widgets) {
			if (!this.config.widgetClasses[this.config.widgets[i][0]]) {
				this.log('err', 'missing widget class `' + this.config.widgets[i][0] + '`');
				continue;
			}
			// create instance of widget using class name (first element in array) and config (second element in array)
			this.widgets.push(new this.config.widgetClasses[this.config.widgets[i][0]](this.config.widgets[i][1]));
		}
		for (i in this.widgets) {
			this.appendWidget(this.widgets[i]);
		}

		//$.sub('widget:render', onWidgetRender);
	};

	WidgetManager.prototype.appendWidget = function(widget) {
		var b = bonzo(window.document.getElementById('dashboard'));
		b.append(widget.el);
	}

	WidgetManager.prototype.start = function() {
		this.isActive = true;
		_.each(this.widgets, function(widget) {
			this.activateWidget(widget)
		}, this);
	}

	WidgetManager.prototype.stop = function() {
		this.isActive = false;
		_.each(this.widgets, function(widget) {
			widget.stop();
		}, this);
	}

	WidgetManager.prototype.activateWidget = function(widget) {
		if (this.isActive) {
			widget.start();
		}
	}

	var defaultWidgetConfig = {
		tick : 60
	};


	// TODO ...
	
	WidgetManager.prototype.onWidgetRender = function(widget) {
		//options.callback(widget);
	}

	WidgetManager.prototype.addWidget = function(widget) {
		this.widgets.push(widget);
		this.append(widget.el);
		activateWidget(widget);
	}

	WidgetManager.prototype.addWidgets = function(newWidgets) {
		m = this;
		$.each(newWidgets, function() {
			addWidget.apply(m, [ this ]);
		});
	}

	// inject log aspect here
	WidgetManager.prototype.log = function(level, msg) {};

	return WidgetManager;
});
