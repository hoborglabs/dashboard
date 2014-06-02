<?php
/**
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 * Dashbord CLI
 */
$autoloaderPath = __DIR__ . '/../../../autoload.php';
if (!is_file($autoloaderPath)) {
	$autoloaderPath = __DIR__ . '/../vendor/autoload.php';
}
include $autoloaderPath;

$kernel = new \Hoborg\Dashboard\Kernel(__DIR__ . '/../', 'prod');

$option = getopt('c:p:d:');
$kernel->handleCli($option);
