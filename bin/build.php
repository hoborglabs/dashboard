<?php 

$path = $argv[1];

if (empty($path)) {
	echo 'please specify path';
	die;
}

// mini phar
$phar = new Phar('dashboard.phar', 0, 'dashboard.phar');
$phar->buildFromDirectory($path);
$phar->setStub('<?php Phar::mapPhar(\'dashboard.phar\'); include \'phar://dashboard.phar/htdocs/dashboard.php\'; __HALT_COMPILER();');