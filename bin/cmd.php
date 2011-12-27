<?php
/**
 * @author Wojtek Oledzki <wojtek@hoborglabs.com>
 */
include __DIR__ . '/../autoload.php';
echo "\nDashboard CLI by Wojtek Oledzki\n\n";

$options = getopt('c:p::');
$commands = buildCommands();

if (empty($options['c'])) {
	exit(printHelp($commands));
}

$fileToInclude = $commands[$options['c']];
include $fileToInclude;

function printHelp($commands) {
	echo "Example Usage: `cmd -c widget.hoborg.commiters.git-collector -p \"master 100\"`\n";
	echo "  -c    Command in format widget.path.to.cmd.file\n";
	echo "  -p    Parameters\n\n";
	echo "\nAvailable commands\n  ";
	echo implode("\n  ", array_keys($commands)) . "\n";
}

function buildCommands() {
	$cmds = array();
	$cmds += getCmdFromDir(__DIR__ . '/../widgets', 'widget.');
	return $cmds;
}

function getCmdFromDir($dir, $prefix = '') {
	$c = scandir($dir);
	$phpFiles = array();
	foreach ($c as $entry) {
		if (in_array($entry, array('.', '..'))) {
			continue;
		}
		if (!is_readable($dir . '/' . $entry)) {
			continue;
		}
		if (is_dir($dir . '/' . $entry)) {
			$phpFiles += getCmdFromDir($dir . '/' . $entry, $prefix.$entry.'.');
		}
		if (false !== strpos($entry, 'cmd-')) {
			$phpFiles[$prefix . substr($entry, 4, -4)] = $dir . '/' . $entry;
		}
	}
	return $phpFiles;
}