<?php

include_once __DIR__ . '/core.php';

if (is_file(__DIR__ . '/init.php')) {
       include_once __DIR__ . '/init.php'; 
} else {
        include_once __DIR__ . '/init.dist.php';
}
