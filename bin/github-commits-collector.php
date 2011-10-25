<?php
$url = 'https://api.github.com/repos/:user/:repo/commits/:ref';
$opt = array(
	'r:' => 'repo:',
	'u:' => 'user:',
	'v::' => 'ref::',
);
$defaults = array(
	'ref' => 'HEAD',
);

$params = get_options($opt);
$params += $defaults;

$storeFile = __DIR__ . "/../data/github-commits-{$params['user']}-{$params['repo']}.js";

$storeData = json_decode(file_get_contents($storeFile), true);

foreach ($params as $param => $value) {
	$url = str_replace(':'.$param, $value, $url);
}

$data = file_get_contents($url);
if (empty($data)) {
	return;
}
$data = json_decode($data, true);

if (empty($data)) {
	return;
}

$commitData = array(
	'commit' => $data['commit'],
);
$storeData[$data['sha']] = $commitData;

file_put_contents($storeFile, json_encode($storeData));



function get_options(array $params) {
	$options = array();
	$shortOpt = array();
	array_walk($params, function($val, $shortParam) use(& $shortOpt) {
		$k = str_replace(':', '', $shortParam);
		$v = str_replace(':', '', $val);
		$shortOpt[$k] = $v;
	 });

	$opt = getopt(implode('', array_keys($params)), $params);
	foreach ($opt as $key => $value) {
		if (isset($shortOpt[$key])) {
			$options[$shortOpt[$key]] = $value;
		} else {
			$options[$key] = $value;
		}
	}

	return $options;
}
