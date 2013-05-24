<?php
$rootPath = __DIR__ . '/..';
require_once "{$rootPath}/autoload.php";

$kernel = new \Hoborg\Dashboard\Kernel($rootPath, 'dev');
$kernel->setDefaultParam('conf', 'demo');
$kernel->handle(array_merge($_GET, $_POST));