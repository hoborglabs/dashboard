<?php
$rootPath = __DIR__ . '/..';
require_once $rootPath . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;
use Hoborg\DashboardCache\Kernel;

$req = Request::createFromGlobals();
$res = Response::create();
$res->setContent('Hi !!');
$kernel = new Kernel("{$rootPath}/dashboardCache.properties");
$kernel->handle($req, $res);

$res->send();