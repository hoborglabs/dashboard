<?php

require_once __DIR__ . '/../autoload.php';
$kernel = new \Hoborg\Dashboard\Kernel('dev');
$kernel->setDefaultParam('conf', 'demo');
$kernel->handle(array_merge($_GET, $_POST));