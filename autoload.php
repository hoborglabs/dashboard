<?php
/**
 * Hoborg Dashboard.
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 * Autoload all required files and defines.
 */

define('H_D_ROOT', __DIR__);

$confDir = H_D_ROOT . '/conf';
if (is_file($confDir . '/init.php')) {
    include_once $confDir . '/init.php';
} else {
    include_once $confDir . '/init.dist.php';
}

include_once SRC_DIR . '/Hoborg/Dashboard/Dashboard.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Exception.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Kernel.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Proxy.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Widget.php';
include_once SRC_DIR . '/Hoborg/Dashboard/WidgetProvider.php';
