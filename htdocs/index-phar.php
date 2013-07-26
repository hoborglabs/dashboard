<?php
$rootPath = __DIR__ . '/..';
require_once $rootPath . '/vendors/dashboard.phar';

$kernel = new \Hoborg\Dashboard\Kernel('phar://dashboard.phar');
$kernel->setDefaultParam('conf', 'demo');
$kernel->handle(array_merge($_GET, $_POST));
