<?php
date_default_timezone_set('UTC');

/**
 * Returns Configurtation array
 *
 * @return array
 */
function get_config() {
	if (empty($_GET['conf']) && !defined('DEFAULT_CONFIG')) {
		$error = "no configuration specified";
		$code = '500';
		include TEMPLATE_DIR . '/error.phtml';
		die(1);
	}
	
	$configName = empty($_GET['conf']) ? DEFAULT_CONFIG : $_GET['conf'];

	$configFile = CONFIG_DIR .'/' . $configName . '.js';
	if (!is_file($configFile)) {
		$configFile = CONFIG_DIR .'/' . $configName . '.json';
		if (!is_file($configFile)) {
			$error = "configuration file not found";
			$code = '404';
			include TEMPLATE_DIR . '/error.phtml';
			die(1);
		}
	}

	// get configuration
	$config = json_decode(file_get_contents($configFile), true);

	if (empty($config)) {
		$error = "You have an error in your configuration";
		$code = '500';
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
		if ($path = get_widget_path($widget['static'])) {
			$widget['body'] = file_get_contents($path);
		}
	}

	if (!empty($widget['url'])) {
		get_widget_from_url($widget);
	}

	if (!empty($widget['cgi'])) {
		get_widget_from_cgi($widget);
	}

	if (!empty($widget['php'])) {
		if ($path = get_widget_path($widget['php'])) {
			$widget + include $path;
		}
	}
}

function get_widget_path($widgetFile) {
	$paths = explode(PATH_SEPARATOR, WIDGETS_ROOT);
	foreach ($paths as $path) {
		if (is_readable($path . '/' . $widgetFile)) {
			return $path . '/' . $widgetFile;
		}
	}
	
	return null;
}

function get_template_path($templateFile) {
	$paths = explode(PATH_SEPARATOR, TEMPLATE_DIR);
	foreach ($paths as $path) {
		if (is_readable($path . '/' . $templateFile)) {
			return $path . '/' . $templateFile;
		}
	}
	
	return null;
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