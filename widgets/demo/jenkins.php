<?php

$data = file_get_contents(__DIR__ . '/../../data/' . $widget['conf']['data']);
$data = json_decode($data, true);

ksort($data);
$lastBuild = array_pop($data);

ob_start();
include __DIR__ . '/jenkins-body.phtml';
$widget['body'] = ob_get_clean();

return $widget;
