<?php
/**
 * Hoborg Dashboard.
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 * Autoload all required files and defines.
 */

define('SRC_DIR', __DIR__ . '/src');

$loader = include_once __DIR__ . '/vendor/autoload.php';
$loader->add('Hoborg\\Dashboard\\', SRC_DIR);
$loader->add('Hoborg\\DashboardCache\\', SRC_DIR);
