define([
	'hoborglabs/widget'
], function(HoborglabsWidget){

	function TestWidget() {
		HoborglabsWidget.apply(this, arguments);
		this.counter = 1;
	}
	// extend Hoborglabs main Widget
	TestWidget.prototype = Object.create( HoborglabsWidget.prototype );

	TestWidget.prototype.startData = function() {
		var widget = this;
		this.timer = setInterval(function() {
			widget.hackIt();
		}, 2000);
	};

	TestWidget.prototype.hackIt = function() {
		this.widget.html('counter: ' + this.counter++);
	};

	// register class to widget store 
	if (window.Hoborglabs.Dashboard.widgetClasses) {
		window.Hoborglabs.Dashboard.widgetClasses.TestWidget = TestWidget;
	}

	return TestWidget;
});