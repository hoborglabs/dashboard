<?php
$data = json_decode(file_get_contents(__DIR__ . '/../../data/' . $widget['conf']['dataFile']), true);

$widget['commit'] = array_pop($data);
$widget['commit'] = $widget['commit']['commit'];

ob_start();
include __DIR__ . '/commits.phtml';
$widget['body'] = ob_get_clean();

return $widget;
