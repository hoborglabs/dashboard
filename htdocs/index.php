<?php
require_once __DIR__ . '/../src/core.php';

$confDir = realpath(__DIR__ . '/../conf');
$config = $_GET['conf'];
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
$widgets = json_decode(file_get_contents($configFile), true);

// now we bootstrap each widget
foreach ($widgets as & $widget) {
	bootstrap_widget($widget);
}
unset($widget);

// ... collect `head` data

include __DIR__ . '/../templates/hoborg.phtml';

return;

$head = '';
$onceOnly = array();
$onLoad = array();
foreach ($data as $widget) {
	foreach ($widget['head'] as $key => $values) {
		if ('onceOnly' === $key) {
			foreach ($values as $k => $v) { $onceOnly[$k] = $v; }
		}
		if ('onLoad' === $key) {
			foreach ($values as $k => $v) { $onLoad[$k] = $v; }
		}
	}
}
$head .= join("\n", $onceOnly);

foreach ($data as $widget) {
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

?>

<html>
<head>
<?php echo $head; ?>

<style type="text/css">
.widget {
	width: 500px;
	float: left;
	margin: 0px 10px;
}

.widget h3 {
	border: 2px #DDD solid;
	border-width: 0px 0px 2px 0px;
	margin: 0px 0px 20px 0px;
	padding: 6px 0px 10px 0px;
}
</style>
</head>
<body>

<?php
foreach ($data as $display) {
	echo $display['body'];
}
?>

</body>