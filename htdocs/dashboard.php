<?php
/**
 * Hoborg Dashboard.
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 */

require_once __DIR__ . '/../autoload.php';

$config = get_config();
$widgets = $config['widgets'];

// now we bootstrap each widget
foreach ($widgets as $index => & $widget) {
	if (isset($widget['enabled'])) {
		if (!$widget['enabled']) {
			unset($widgets[$index]);
			continue;
		}
	}
	bootstrap_widget($widget);
}
unset($widget);

$head = '';
$onceOnly = array();
$onLoad = array();
foreach ($widgets as & $widget) {
	if (empty($widget['head'])) {
		continue;
	}

	if (is_callable($widget['head'])) {
		$widget['head'] = $widget['head']();
	}

	foreach ($widget['head'] as $key => $values) {
		if ('onceOnly' === $key) {
			foreach ($values as $k => $v) { $onceOnly[$k] = $v; }
		}
		if ('onLoad' === $key) {
			foreach ($values as $k => $v) { $onLoad[$k] = $v; }
		}
	}
}
unset($widget);
$head .= join("\n", $onceOnly);

foreach ($widgets as $widget) {
	if (empty($widget['head'])) {
		continue;
	}

	foreach ($widget['head'] as $key => $values) {
		if ('always' === $key) {
			foreach ($values as $k => $v) {
				$head .= "\n" . $v;
			}
		}
	}
}

$head .= '<script type="text/javascript">
window.onload = function () {' . join("\n\n", $onLoad) . '};</script>';

include TEMPLATE_DIR. '/' . $config['template'] . '.phtml';
