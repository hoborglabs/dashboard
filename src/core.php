<?php

/**
 * Bootstraps all widgets
 *
 * @param array $widget
 *
 * @return void
 */
function bootstrap_widget(array & $widget) {
	if (!empty($widget['url'])) {
		$widget['body'] = file_get_contents($widget['url']);
	}

	if (!empty($widget['php'])) {
		if (is_readable($widget['php'])) {
			$widget + include $widget['php'];
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