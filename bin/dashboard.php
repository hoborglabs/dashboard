<?php
/**
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 */
include __DIR__ . '/../../autoload.php';
$kernel = new \Hoborg\Dashboard\Kernel('dev');

$option = getopt('c:p:d:');
$kernel->handleCli($option);
