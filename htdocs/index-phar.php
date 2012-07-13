<?php
include 'phar://dashboard.phar/autoload.php';
$root = 'phar://dashboard.phar';

// set-up dashboard and run
$kernel = new \Hoborg\Dashboard\Kernel($root);
$kernel->setDefaultParam('conf', 'demo');
$kernel->setDefaultParam('public', '/dashboard.phar');
$kernel->addPath('config', array($root . '/conf'));
$kernel->addPath('templates', array($root . '/templates'));
$kernel->addPath('widgets', array($root . '/widgets'));
$kernel->handle(array_merge($_GET, $_POST));