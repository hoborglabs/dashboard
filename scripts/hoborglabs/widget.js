;
define([
	'lib/lodash',
	'lib/bonzo',
	'lib/promise',
	'lib/mustache'
], function(_, bonzo, promise, mustache) {

	var WIDGET_OPTIONS = {
		url : '',
		widgetGridWrapper : '<div class="grid-item"></div>',
		widgetWrapper : '<div class="widget"></div></div>',
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

		this.options = _.extend({}, WIDGET_OPTIONS, options);
		this.data = _.extend({}, this.options.defaults, data);
		this.timer = null;

		// widget element
		this.widget = null;
		// and the widget's grid wrapper element
		this.el = null;

		this.init();
	};

	Widget.prototype.init = function() {

		// create widget
		this.widget = bonzo(bonzo.create(this.options.widgetWrapper));
		this.widget.html(mustache.to_html(this.options.widgetLoading, this.data));

		// create widget wrapper
		this.el = bonzo(bonzo.create(this.options.widgetGridWrapper));
		this.el.addClass(this.data.size);
		this.el.addClass('stop');
		this.el.append(this.widget);
	};

	Widget.prototype.start = function() {

		this.startData();

		this.el.removeClass('stop');
		this.el.addClass('start');
	};

	Widget.prototype.startData = function() {
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

		clearInterval(this.timer);
		this.el.removeClass('start');
		this.el.addClass('stop');
	};

	Widget.prototype.reload = function() {

		var widgetConfig = _.extend({}, this.data);

		if (this.data.dataUrl) {
			return this.loadData(widgetConfig);
		}
		return;

		// no need to send body
		delete widgetConfig.body;

		$.ajax({
			url : this.options.url,
			processData : true,
			data : {
				conf : this.options.conf,
				widget : widgetConfig
			},
			type : 'POST',
			dataType : 'json',
			context : this,
			success : function(body) {
				this.data = _.extend({}, this.data, body);
				this.render();
			},
			error : function() {
				widgetConfig.body = 'JSON Error';
				this.render('JSON Error :(');
//				activateWidget(this);
			}
		});
	};

	Widget.prototype.loadData = function(widgetConfig) {
		// no need to send body
		delete widgetConfig.body;

		if (!this.data.dataUrl) {
			widgetConfig.body = 'oh snap, no data Url :(';
			return this.render('oh snap, no data Url :(');
		}

		var widget = this;
		promise.get(this.data.dataUrl).then(function(err, result) {
			if (err) {
				console.log('Error', err);
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

		if (overrideBody) {
			body = overrideBody;
		} else {
			var tpl = this.data.template;
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

	return Widget;
});
