;
define([], function() {
	var WIDGET_OPTIONS = {
		url : '',
		widgetGridWrapper : '<div class="grid-item"></div>',
		widgetWrapper : '<div class="widget"></div></div>',
		widgetLoading: '<div class="header">{{name}}</div><div class="body"><h3 class="loading">Loading Widget...</h3></div>',
		defaults : {
			dataUrl: null,
			tick : 60,
			enabled : 1,
			size : 'span8',
			template : '{{{body}}}'
		}
	};

	/**
	 * Main Widget class.
	 * 
	 * @param object data
	 * @param object options
	 */
	function Widget(data, options) {

		this.options = $.extend(true, {}, WIDGET_OPTIONS, options);
		this.data = $.extend(true, {}, this.options.defaults, data);
		this.timer = null;

		this.init();
	};

	Widget.prototype.init = function() {

		// create widget element
		this.el = $(this.options.widgetGridWrapper);
		var widget = $(this.options.widgetWrapper)
		widget.addClass(this.data.size);
		widget.html($.mustache(this.options.widgetLoading, this.data));
		
		this.el.append(widget);
	};
	
	Widget.prototype.start = function() {

		this.el.removeClass('stop');
		this.el.addClass('start');

		this.startData();
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

		var widgetConfig = $.extend({}, this.data);

		if (this.data.dataUrl) {
			return this.loadData(widgetConfig);
		}

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
				this.data = $.extend({}, this.data, body);
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

		$.ajax({
			url: this.data.dataUrl,
			type: 'GET',
			context : this,
			dataType: 'jsonp',
			success : function(response) {
				console.log(response);
				if (response.data) {
					this.data.data = response.data;
					this.render();
				}
			}
		});
	};

	Widget.prototype.render = function(overrideBody) {

		if (overrideBody) {
			body = overrideBody;
		} else {
			var tpl = this.data.template;
			var body = $.mustache(tpl, this.data);
		}
		if (!body) {
			this.el.addClass('hidden');
		} else {
			this.el.removeClass('hidden');
			this.el.html(body);
		}

		$.pub('widget:render', this);
	};

	// register class to widget store 
	if (window.Hoborglabs.Dashboard.widgetClasses) {
		window.Hoborglabs.Dashboard.widgetClasses.HoborgWidget = Widget;
	}

	return Widget;
});
