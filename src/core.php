<?php
define('WIDGETS_ROOT', realpath(__DIR__ . '/../widgets'));

/**
 * Bootstraps all widgets
 *
 * @param array $widget
 *
 * @return void
 */
function bootstrap_widget(array & $widget) {
	if (!empty($widget['url'])) {
		get_widget_from_url($widget);
	}

	if (!empty($widget['static'])) {
		if (is_readable(WIDGETS_ROOT . '/' . $widget['static'])) {
			$widget['body'] = file_get_contents(WIDGETS_ROOT . '/' . $widget['static']);
		}
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
			$body = file_get_contents($widget['url']);
			break;

		default:
			break;
	}

	$widget['body'] = $body;
}