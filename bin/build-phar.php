<?php

$buildFolder = __DIR__ . '/../phar';
$options = getopt('v:');

$phar = new Phar(__DIR__ . '/../dashboard.phar', 0, 'dashboard.phar');
$phar->buildFromDirectory($buildFolder);
$phar->setStub("<?php
Phar::mapPhar('dashboard.phar');
require_once 'phar://dashboard.phar/vendor/autoload.php';
__HALT_COMPILER();
");
