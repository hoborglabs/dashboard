<?php

// widget body
ob_start();
include __DIR__ . '/chart-body.phtml';
$widget['body'] = ob_get_clean();

// widget head
ob_start();
include __DIR__ . '/chart-on-load.phtml';
$widget['head']['onLoad']['chart01'] = ob_get_clean();

ob_start();
include __DIR__ . '/chart-js.phtml';
$widget['head']['onceOnly']['rgraph'] = ob_get_clean();

return $widget;
