<?php
namespace Hoborg\Dashboard;

class Cli {

	/**
	 * @var Hoborg\Dashboard\Kernel
	 */
	protected $kernel = null;

	public function __construct(Kernel $kernel) {
		$this->kernel = $kernel;
	}

	public function handle($params) {
		echo "\nDashboard CLI by Wojtek Oledzki\n";
		$commands = $this->buildCommands();
		$params = $this->parseParams($params);

		if (empty($params['c'])) {
			exit($this->printHelp($commands));
		}

		$file = $params['c'];
		unset($params['c']);

		echo "\nRunning {$file} ...\n";
		include $commands[$file];
		echo "Done\n";
	}

	protected function parseParams(array $params) {
		if (empty($params['d'])) {
			$params['d'] = array();
		}
		if (!is_array($params['d'])) {
			$params['d'] = array($params['d']);
		}

		foreach ($params['d'] as $value) {
			list ($key, $val) = explode('=', $value, 2);
			if (null === $val) {
				$val = true;
			}
			$params[$key] = $val;
		}
		unset($params['d']);

		return $params;
	}

	protected function printHelp(array $commands) {
		echo "Example Usage: `cmd -c widget.hoborg.commiters.git-collector -p \"master 100\"`\n";
		echo "  -c    Command in format widget.path.to.cmd.file\n";
		echo "  -p    Parameters\n\n";
		echo "\nAvailable commands\n  ";
		echo implode("\n  ", array_keys($commands)) . "\n";
	}

	protected function buildCommands() {
		$cmds = array();

		foreach ($this->kernel->getWidgetsPath() as $path) {
			$cmds += $this->getCmdFromDir($path, 'widget.');
		}

		return $cmds;
	}

	protected function getCmdFromDir($dir, $prefix = '') {
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
				$phpFiles += $this->getCmdFromDir($dir . '/' . $entry, $prefix.$entry.'.');
			}
			if (false !== strpos($entry, 'cmd-')) {
				$phpFiles[$prefix . substr($entry, 4, -4)] = $dir . '/' . $entry;
			}
		}

		return $phpFiles;
	}
}