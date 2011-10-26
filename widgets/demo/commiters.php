<?php
$data = json_decode(file_get_contents(__DIR__ . '/../../data/' . $widget['conf']['dataFile']), true);

$authors = array();

foreach ($data as & $commit) {
	$authors[$commit['commit']['committer']['email']] = $commit['commit']['committer'];
}
unset($commit);

ob_start();
include __DIR__ . '/commiters.phtml';
$widget['body'] = ob_get_clean();


return $widget;
