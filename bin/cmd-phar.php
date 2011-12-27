<?php
/**
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 */
include __DIR__ . '/../build/dashboard.php';

$kernel = new \Hoborg\Dashboard\Kernel('dev');

$option = getopt('c:p:');
$kernel->handleCli($option);