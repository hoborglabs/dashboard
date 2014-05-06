;
/**
 * Hoborglabs Dahsboard App.
 *
 * @author Wojtek Oledzki (github.com/woledzki)
 */
require([
	'lib/ready',
	'hoborglabs/widget',
	'hoborglabs/widgetManager'
], function(ready, Widget, WidgetManager) {

	// inject log aspect
	var logWrapper = function(namespace) {
		return function(level, message) {
			message = namespace + ': ' + message;
			if ('debug' == level) {
				console.debug(message);
			} else if ('info' == level) {
				console.info(message);
			} else {
				console.log(level + ': ' + message);
			}
		}
	}
	var log = logWrapper('app');

	WidgetManager.prototype.log = logWrapper('Hoborg.WidgetManager');
	Widget.prototype.log = logWrapper('Hoborg.Widget');

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
		// export to global scope
		window.Hoborglabs.WidgetManager = widgetManager;
	});
});
