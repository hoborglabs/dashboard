;
define([
	'lib/lodash',
	'lib/bonzo',
	'lib/promise',
	'lib/mustache'
], function(_, bonzo, promise, mustache) {

	var WIDGET_OPTIONS = {
		url : '',
		conf: 'ankh-morpork',
		widgetGridWrapper : '<div class="grid-item"></div>',
		widgetWrapper : '<div class="widget"></div>',
		widgetLoading: '<div class="widget-header">{{name}}</div><div class="widget-body"><h3 class="loading">Loading Widget...</h3></div>',
		defaults : {
			dataUrl: null,
			tick : 60,
			enabled : 1,
			size : 'span8',
			template : '{{{body}}}'
		}
	};

	var win = window,
		doc = win.document;

	/**
	 * Main Widget class.
	 * 
	 * @param object data
	 * @param object options
	 */
	function Widget(data, options) {
		// debug, debug ...
		this.log('debug', 'new Widget()');

		this.options = _.extend({}, WIDGET_OPTIONS, options);
		this.data = _.extend({}, this.options.defaults, data);
		this.timer = null;
		this.name = this.data.name || 'no-name';

		// widget element
		this.widget = null;
		// and the widget's grid wrapper element
		this.el = null;

		this.init();
	};

	Widget.prototype.init = function() {
		// debug, debug ...
		this.log('debug', 'init ' + this.name);

		// create widget
		this.widget = bonzo(bonzo.create(this.options.widgetWrapper));
		this.widget.html(mustache.to_html(this.options.widgetLoading, this.data));

		// create widget wrapper
		this.el = bonzo(bonzo.create(this.options.widgetGridWrapper));
		this.el.addClass(this.data.size);
		this.el.addClass('stop');
		this.el.append(this.widget);

		this.log('info', 'Widget `' + this.name + '` initialised.');
	};

	Widget.prototype.start = function() {
		// debug, debug ...
		this.log('debug', 'start ' + this.name);

		this.startData();

		this.el.removeClass('stop');
		this.el.addClass('start');
	};

	Widget.prototype.startData = function() {
		// debug, debug ...
		this.log('debug', 'startData ' + this.name);

		if (this.data.tick > 0) {
			var widget = this;
			this.timer = setInterval(function() {
				widget.reload();
			}, this.data.tick * 1000);
			this.reload();
		} else {
			this.reload();
		}
	};

	Widget.prototype.stop = function() {
		// debug, debug ...
		this.log('debug', 'stop ' + this.name);

		clearInterval(this.timer);
		this.el.removeClass('start');
		this.el.addClass('stop');
	};

	Widget.prototype.reload = function() {
		// debug, debug ...
		this.log('debug', 'reload ' + this.name);

		var widgetConfig = _.extend({}, this.data);

		if (this.data.dataUrl) {
			return this.loadData(widgetConfig);
		}

		// no need to send body
		delete widgetConfig.body;

		var widget = this;
		promise.post(this.options.url, {
			widget : JSON.stringify(widgetConfig)
		}).then(function(err, body) {
			if (err) {
				widget.log('error', 'reload `' + this.name + '` GET `'+ this.data.dataUrl + '` error: ' + err);
				widgetConfig.body = 'JSON Error';
				widget.render('JSON Error :(');
				return;
			}
			wData = JSON.parse(body);
			widget.data = _.extend({}, widget.data, wData);
			widget.render();
		});
	};

	Widget.prototype.loadData = function(widgetConfig) {
		// debug, debug ...
		this.log('debug', 'loadData ' + this.name);

		// no need to send body
		delete widgetConfig.body;

		if (!this.data.dataUrl) {
			widgetConfig.body = 'oh snap, no data Url :(';
			return this.render('oh snap, no data Url :(');
		}

		var widget = this;
		var data = {config: JSON.stringify(this.data.config || {})};
		promise.post(this.data.dataUrl, data).then(function(err, result) {
			if (err) {
				widget.log('error', 'loadData `' + this.name + '` GET `'+ this.data.dataUrl + '` error: ' + err);
				return;
			}

			wData = JSON.parse(result);
			if (wData) {
				widget.data.data = wData.data;
				widget.render();
			}
		});
		
		return;
	};

	Widget.prototype.render = function(overrideBody) {
		// debug, debug
		this.log('debug', 'render ' + this.name);
		console.log(this.data);
		
		if (overrideBody) {
			body = overrideBody;
		} else {
			var tpl = this.data.template || '{{{body}}}';
			var body = mustache.to_html(tpl, this.data);
		}
		if (!body) {
			this.widget.addClass('hidden');
		} else {
			this.widget.removeClass('hidden');
			this.widget.html(body);
		}

		//$.pub('widget:render', this);
	};

	// register class to widget store 
	if (window.Hoborglabs.Dashboard.widgetClasses) {
		window.Hoborglabs.Dashboard.widgetClasses.HoborgWidget = Widget;
	}

	/*
	 * This is just placeholder for proper log function.
	 * Inject your own log function if needed
	 */
	Widget.prototype.log = function(level, msg) {};

	return Widget;
});
