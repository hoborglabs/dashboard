<?php
include_once __DIR__ . '/../src/command_line.php';

$tree = array(
	'name',
	'id',
	'lastBuild' => array(
		'number',
		'timestamp',
		'result',
		'url'
	),
	'healthReport' => array('score'),
);
$url = 'http://leeds.oledzki.info:5080/jenkins/job/:job/api/json?tree=';
$opt = array(
        'j:' => 'job:',
);
$defaults = array();

$params = get_options($opt);
$params += $defaults;

$storeFile = __DIR__ . "/../data/jenkins-job-{$params['job']}.js";
$storeData = json_decode(file_get_contents($storeFile), true);

foreach ($params as $param => $value) {
        $url = str_replace(':'.$param, $value, $url);
}

$url .= urlencode(get_tree_value($tree));

// get data from url
$data = file_get_contents($url);
if (empty($data)) {
	die('no Data');
}
$data = json_decode($data, true);

$id = $data['lastBuild']['number'];

$storeData[$id] = $data;
file_put_contents($storeFile, json_encode($storeData));



function get_tree_value(array $tree) {
	$value = '';

	foreach ($tree as $key => $val) {
		if (is_array($val)) {
			$value .= ','.$key.'['.get_tree_value($val).']';
		} else {
			$value .= ','.$val;
		}
	}

	return substr($value, 1);
}

