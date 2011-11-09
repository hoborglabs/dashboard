<?php
require_once __DIR__ . '/../src/autoload.php';

define('HD_PUBLIC', '/projects/Dashboard/htdocs/');

if (empty($_GET['conf'])) {
	$error = "no configuration specified";
        include TEMPLATE_DIR . '/error.phtml';
	return;
}
$config = $_GET['conf'];

$configFile = realpath(CONFIG_DIR .'/' . $config . '.js');
if (empty($configFile)) {
	$error = "configuration file not found";
        include TEMPLATE_DIR . '/error.phtml';
	return;
}

if (strpos($confDir, $configFile)) {
	echo "erorr :(";
	// redirect to error page;
	return;
}

// get widgets
$config = json_decode(file_get_contents($configFile), true);

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

include __DIR__ . '/../templates/' . $config['template'] . '.phtml';
