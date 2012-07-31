<?php
/**
 * Hoborg Dashboard.
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 * Autoload all required files and defines.
 */

define('SRC_DIR', __DIR__ . '/src');

include_once SRC_DIR . '/Hoborg/Dashboard/IWidgetProvider.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Cli.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Dashboard.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Exception.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Kernel.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Proxy.php';
include_once SRC_DIR . '/Hoborg/Dashboard/StaticAssetsProxy.php';
include_once SRC_DIR . '/Hoborg/Dashboard/Widget.php';
include_once SRC_DIR . '/Hoborg/Dashboard/WidgetProvider.php';
