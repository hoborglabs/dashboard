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
		$this->kernel->log("\nDashboard CLI by Wojtek Oledzki\n");
		$commands = $this->buildCommands();
		$params = $this->parseParams($params);

		if (empty($params['c'])) {
			exit($this->printHelp($commands));
		}

		if (empty($commands[$params['c']])) {
			$this->kernel->log("Unknown command {$params['c']}");
			return;
		}

		$commandName = $params['c'];
		$command = $commands[$commandName];
		unset($params['c']);

		$this->kernel->log("\nRunning {$commandName} ...\n");

		include $command['path'];
		if (empty($command['class'])) {
			echo "Running in deprecated mode. Please encapsulate your CLI in execute class.\n";
		}
		$cmd = new $command['class'];
		$cmd->execute($params);

		$this->kernel->log("Done\n");
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

	private function getCmdFromDir($dir, $prefix = '') {
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
				continue;
			}
			if (false !== strpos($entry, 'cmd-')) {
				$phpFiles[$prefix . substr($entry, 4, -4)] = $this->getFileMeta($dir . '/' . $entry);
			}
		}

		return $phpFiles;
	}

	private function getFileMeta($file) {
		$fileMeta = array(
			'path' => $file,
			'class' => null,
		);

		// get class names
		$tokens = token_get_all(file_get_contents($file));
		$fullClassName = '';
		for ($i = 0; $i < count($tokens); $i++) {
			$tokenName = is_array($tokens[$i]) ? $tokens[$i][0] : null;
			if (T_NAMESPACE == $tokenName) {
				$i += 2;
				$tokenValue = is_array($tokens[$i]) ? '\\' . $tokens[$i][1] : null;
				while (is_array($tokens[++$i])) {
					$tokenValue .= $tokens[$i][1];
				}
				$fullClassName .= $tokenValue;
				continue;
			}
			if (T_CLASS == $tokenName) {
				$tokenValue = is_array($tokens[$i+2]) ? $tokens[$i+2][1] : null;
				$fullClassName .= '\\' . $tokenValue;
				break;
			}
		}
		$fileMeta['class'] = $fullClassName;

		return $fileMeta;
	}
}
