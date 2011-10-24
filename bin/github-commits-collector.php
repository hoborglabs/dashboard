<?php
$url = 'https://api.github.com/repos/:user/:repo/commits/:ref';
$params = array(
	'repo' => 'Dashboard',
	'user' => 'hoborglabs',
	'ref' => 'HEAD',
);
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
