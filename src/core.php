<?php
define('WIDGETS_ROOT', realpath(__DIR__ . '/../widgets'));
date_default_timezone_set('UTC');

/**
 * Returns Configurtation array
 *
 * @return array
 */
function get_config() {
	if (empty($_GET['conf'])) {
		$error = "no configuration specified";
		include TEMPLATE_DIR . '/error.phtml';
		die(1);
	}
	$configName = $_GET['conf'];

	$configFile = realpath(CONFIG_DIR .'/' . $configName . '.js');
	if (empty($configFile)) {
		$error = "configuration file not found";
		include TEMPLATE_DIR . '/error.phtml';
		die(1);
	}

	// get configuration
	$config = json_decode(file_get_contents($configFile), true);

	if (empty($config)) {
		$error = "You have an error in your configuration";
		include TEMPLATE_DIR . '/error.phtml';
		die(1);
	}

	return $config;
}

/**
 * Bootstraps all widgets
 *
 * @param array $widget
 *
 * @return void
 */
function bootstrap_widget(array & $widget) {
	if (!empty($widget['static'])) {
		if (is_readable(WIDGETS_ROOT . '/' . $widget['static'])) {
			$widget['body'] = file_get_contents(WIDGETS_ROOT . '/' . $widget['static']);
		}
	}

	if (!empty($widget['url'])) {
		get_widget_from_url($widget);
	}

	if (!empty($widget['cgi'])) {
		get_widget_from_cgi($widget);
	}

	if (!empty($widget['php'])) {
		if (is_readable(WIDGETS_ROOT . '/' . $widget['php'])) {
			$widget + include WIDGETS_ROOT . '/' . $widget['php'];
		}
	}
}

/**
 * Renders widgets
 *
 * @param array $widget
 *
 * @return void
 */
function render_widget(array & $widget) {
	if (is_callable($widget['body'])) {
		$widget['body'] = $widget['body']();
	}

	return (string) $widget['body'];
}

/**
 *
 * @param array $widget
 *
 * @return array
 */
function get_head(array & $widget) {
	if (is_callable($widget['head'])) {
		$widget['head'] = $widget['head']();
	}

	if (empty($widget['head'])) {
		return array();
	}

	return (array) $widget['head'];
}

function get_widget_from_url(array & $widget) {
	$method = empty($widget['_method']) ? 'GET' : $widget['_method'];
	$body = null;

	switch ($method) {
		case 'GET':
			// @todo: add proper handler (add GET widget=json_encode($widget))
			//$body = file_get_contents($widget['url']);
			$widgetNew = json_decode(file_get_contents($widget['url']), true);
			if (!empty($widgetNew)) {
			    $widget = $widgetNew + $widget;
			}
			break;

		default:
			break;
	}

	//$widget['body'] = $body;
}