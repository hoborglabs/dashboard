;
/**
 * Hoborglabs Dahsboard App.
 * 
 * @author Wojtek Oledzki (github.com/woledzki)
 */
define([
	'lib/ready',
	'hoborglabs/widget',
	'hoborglabs/widgetManager'
], function(ready, Widget, WidgetManager) {

	var log = function (level, message) {};

	// on dom ready...
	ready(function() {
		// do we have Dashboard config object?
		if (!window.Hoborglabs || !window.Hoborglabs.Dashboard || !window.Hoborglabs.Dashboard.widgets) {
			log('debug', 'No widgets found in window.Hoborglabs.Dashboard.widgets');
			return;
		}

		// create and start Dashboard.
		var widgetManager = new WidgetManager(window.Hoborglabs.Dashboard);
		widgetManager.init();
		widgetManager.start();
	});

	// inject log aspect
	log = function (level, message) { console.log(message); };
});