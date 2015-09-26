<?php

function main(array $args = array()) {
	$defaults = array(
		'install-dir' => getcwd()
	);
	$args += $defaults;

	if (!createFolders($args['install-dir'])) return 1;
	if (!download($args['install-dir'])) return 1;
	if (!install($args['install-dir'])) return 1;

	return 0;
}

function createFolders($rootFolder) {
	out("Creating folder structure in `{$rootFolder}`");

	foreach (array('config', 'data', 'widgets', 'templates', 'htdocs', 'tmp') as $folder) {
		if (!is_dir("{$rootFolder}/{$folder}")) {
			mkdir("{$rootFolder}/{$folder}");
		}
	}

	return true;
}

function download($rootFolder) {
	out("Downloading dashboard.phar `{$rootFolder}/`");
	getFile("{$rootFolder}/dashboard.phar", "http://get.hoborglabs.com/dashboard/dashboard.phar");

	return true;
}

function install($rootFolder) {
	out('Installing');
	chdir("{$rootFolder}/");

	$phar = new Phar("{$rootFolder}/dashboard.phar");
	$phar->extractTo("{$rootFolder}/tmp/", null, true);

	system("mv {$rootFolder}/tmp/src/Hoborg/Dashboard/Resources/htdocs/index-phar.php {$rootFolder}/htdocs/index.php");
	system("mv {$rootFolder}/tmp/src/Hoborg/Dashboard/Resources/htdocs/static {$rootFolder}/htdocs/static");
	system("mv {$rootFolder}/tmp/src/Hoborg/Dashboard/Resources/htdocs/images {$rootFolder}/htdocs/images");

	system("rm -rf {$rootFolder}/tmp");

	return true;
}

function getFile($localPath, $remotePath) {
	if (is_readable($localPath)) {
		unlink($localPath);
	}

	return file_put_contents($localPath, file_get_contents($remotePath));
}

function getOptions($options) {
	$opt;
	$short = '';
	$long = array();
	$map = array();

	foreach ($options as $option) {
		$req = isset($option['required']) ? ($option['required'] ? ':' : '::') : '';

		if (!empty($option['short'])) {
			$short .= $option['short'] . $req;
		}
		if (!empty($option['long'])) {
			$long[] = $option['long'] . $req;
		}
		if (!empty($option['short']) && !empty($option['long'])) {
			$map[$option['short']] = $option['long'];
		}
	}

	$arguments = getopt($short, $long);
	foreach ($map as $s => $l) {
		if (isset($arguments[$s]) && isset($arguments[$l])) {
			unset($arguments[$s]);
			continue;
		}
		if (isset($arguments[$s])) {
			$arguments[$l] = $arguments[$s];
			unset($arguments[$s]);
			continue;
		}
	}

	return $arguments;
}

function out($message, $pre = '  ') {
	echo "{$pre}{$message}\n";
}

/**
 * Script options
 */
$options = array(
	array(
		'short' => 'i',
		'long' => 'install-dir',
		'desc' => 'Installation folder',
		'required' => true,
	)
);

exit(main(getOptions($options)));
