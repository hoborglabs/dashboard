<?php
if ('clear' == $_GET['k']) {
	apc_clear_cache('user');
	exit();
}

apc_store($_GET['k'], json_decode($_GET['v'], true));