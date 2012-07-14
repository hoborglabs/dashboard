;(function(context){

	WIDGET_OPTIONS = {
		url: '',
		widgetWrapper: '<div class="widget"></div>',
		defaults: {
			tick: 60,
			enabled: 1,
			size: 'span8',
			template: '{{{body}}}'
		}
	};

	Widget.prototype.data = {};

	Widget.prototype.timer = null;

	function Widget(data, options) {

		this.options = $.extend(true, {}, WIDGET_OPTIONS, options);
		this.data = $.extend(true, {}, this.options.defaults, data);

		this.init();
	};

	Widget.prototype.init = function() {

		//create widget element
		this.el = $(this.options.widgetWrapper);
		this.el.addClass(this.data.size);
	}

	Widget.prototype.start = function() {

		var widget = this;
		if (this.data.tick) {
			this.timer = setInterval(
				function() { widget.reload(); },
				this.data.tick * 1000
			);
		}

		this.el.removeClass('stop');
		this.el.addClass('start');
		this.reload();
	};

	Widget.prototype.stop = function() {

		clearInterval(this.timer);
		this.el.removeClass('start');
		this.el.addClass('stop');
	};

	Widget.prototype.reload = function() {
		
		var widgetConfig = $.extend({}, this.data);
		delete widgetConfig.body;

		$.ajax({
			url: this.options.url,
			processData: true,
			data: {conf: this.options.conf, widget: widgetConfig},
			type: 'POST',
			dataType: 'json',
			context: this,
			success: function(body) {
				this.data = $.extend({}, this.data, body);
				this.render();
			},
			error: function() { widgetConfig.body =  'JSON Error'; renderWidgetJson(this, widgetConfig); activateWidget(this);}
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

	context.HoborgWidget = Widget;
})(window);
