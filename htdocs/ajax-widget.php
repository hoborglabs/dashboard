<?php
require_once __DIR__ . '/../src/core.php';

$confDir = realpath(__DIR__ . '/../conf');
$config = $_GET['c'];
$widgetIndex = $_GET['i'];
$configFile = realpath($confDir .'/' . $config . '.js');

if (empty($configFile)) {
	echo "erorr - no config";
	// redirect to error page;
	return;
}

if (strpos($confDir, $configFile)) {
	echo "erorr :(";
	// redirect to error page;
	return;
}

// get widgets
$config = json_decode(file_get_contents($configFile), true);

$widget = $config['widgets'][$widgetIndex];

bootstrap_widget($widget);
if (!empty($config['ajaxTemplate'])) {
	include __DIR__ . '/../templates/' . $config['ajaxTemplate'] . '.phtml';
}
