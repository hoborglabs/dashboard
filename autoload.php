<?php
/**
 * Hoborg Dashboard.
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 *
 * Autoload all required files and defines.
 */


$confDir = __DIR__ . '/conf';
if (is_file($confDir . '/init.php')) {
    include_once $confDir . '/init.php';
} else {
    include_once $confDir . '/init.dist.php';
}

include_once SRC_DIR . '/core.php';
include_once SRC_DIR . '/proxy.php';