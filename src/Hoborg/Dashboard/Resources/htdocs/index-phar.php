<?php
$rootPath = getenv('DASHBOARD_ROOT') ? getenv('DASHBOARD_ROOT') : __DIR__ . '/..';
require_once "{$rootPath}/dashboard.phar";

$kernel = new \Hoborg\Dashboard\Kernel($rootPath);
$kernel->setDefaultParam('conf', 'demo');
$kernel->handle(array_merge($_GET, $_POST));
