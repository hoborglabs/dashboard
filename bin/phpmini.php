<?php
/**
 * strips comments and whitespaces from php files.
 */

if (empty($argv[1])) {
	die('Please specify file name `php phpmini,php /my/file.php`');
}

$src = $argv[1];
$dst = $src . '.tmp';

exec("php -w $src > $dst");
rename($dst, $src);