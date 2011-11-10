<?php
use Hoborg\Dashboard\AjaxProxy;

require_once __DIR__ . '/../autoload.php';

try {
	$proxy = new AjaxProxy();
	$proxy->execute();
} catch (Exception $e) {
	echo 'Error :( ' . $e->getMessage();
}