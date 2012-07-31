<?php
include 'phar://dashboard.phar/autoload.php';
$pharRoot = 'phar://dashboard.phar';

$conf = array();
if (is_readable(realpath(PHAR_ROOT . '/init.php'))) {
	$conf = require PHAR_ROOT . '/init.php';
}

// set-up dashboard and run
$kernel = new \Hoborg\Dashboard\Kernel($pharRoot);

$kernel->setDefaultParam('conf', 'demo');
$kernel->setDefaultParam('public', '/dashboard.phar');
$kernel->addDefaultParams($conf);

$kernel->addPath('config', array($pharRoot . '/conf', PHAR_ROOT . '/conf'));
$kernel->addPath('templates', array($pharRoot . '/templates', PHAR_ROOT . '/templates'));
$kernel->addPath('widgets', array($pharRoot . '/widgets', PHAR_ROOT . '/widgets'));

$kernel->handle(array_merge($_GET, $_POST));