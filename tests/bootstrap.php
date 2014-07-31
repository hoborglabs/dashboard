<?php
define('TST_ROORT', __DIR__);
date_default_timezone_set('Europe/London');

$composerHome = !getenv('COMPOSER_HOME') ? __DIR__ . '/../vendor' : getenv('COMPOSER_HOME');
include_once $composerHome . '/autoload.php';
