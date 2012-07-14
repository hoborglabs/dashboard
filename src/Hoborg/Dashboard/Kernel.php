<?php
namespace Hoborg\Dashboard;

class Kernel {

	protected $environment = null;

	protected $config = null;

	protected $params = array();

	protected $defaultParams = array();

	protected $paths = array(
		'templates' => array(),
		'widgets' => array(),
		'data' => array(),
		'config' => array(),
	);

	public function __construct($rootFolder, $env = 'prod') {
		$this->environment = $env;

		$this->paths['templates'][] = $rootFolder . '/templates';
		$this->paths['widgets'][] = $rootFolder . '/widgets';
		$this->paths['data'][] = $rootFolder . '/data';
		$this->paths['config'][] = $rootFolder . '/conf';
	}

	/**
	 * Handles dashboard request.
	 *
	 * @param array $params
	 * @param Hoborg\Dashboard\Dashboard $dashboard
	 * 			Optional dashboard object for handling
	 * @param Hoborg\Dashboard\WidgetProvider $widgetProvider
	 */
	public function handle(array $params, Dashboard $dashboard = null, WidgetProvider $widgetProvider = null) {
		try {
			$this->setParams($params);

			if ($this->hasStaticParam()) {
				$proxy = new StaticAssetsProxy($this);
				$proxy->output($this->getParam('static', ''));
				return;
			}

			if ($this->hasWidgetParam()) {
				// render selected widget
				$widget = null;
				if (null == $widgetProvider) {
					$widgetProvider = new WidgetProvider();
				}
				$widget = $widgetProvider->createWidget($this, $this->getParam('widget'));
				$widget->bootstrap();
				$this->send($widget->getJson());
			} else {
				// render whole dashboard
				if (null == $dashboard) {
					$dashboard = new Dashboard($this);
				}
				$this->send($dashboard->render());
			}
		} catch (Exception $e) {
			$this->send($this->handleException($e));
		}
	}

	public function handleCli(array $params) {
		$cli = new Cli($this);
		$cli->handle($params);
	}

	public function hasWidgetParam() {
		return array_key_exists('widget', $this->params);
	}

	public function hasStaticParam() {
		return array_key_exists('static', $this->params);
	}

	public function getEnvironment() {
		return $this->environment;
	}

	public function setDefaultParam($key, $value) {
		$this->defaultParams[$key] = $value;
	}

	public function setParams(array $params) {
		$params = $params + $this->defaultParams;

		if (empty($params['conf'])) {
			throw new Exception('Missing `conf` parameter', 500);
		}

		$this->params = $params;
	}

	public function getParam($key, $default = null) {
		return isset($this->params[$key]) ? $this->params[$key] : $default;
	}

	public function setPath($key, array $paths) {
		$this->paths[$key] = $paths;
		return $this;
	}

	public function addPath($key, array $paths) {
		if (is_array($this->paths[$key])) {
			$this->paths[$key] = array_merge($this->paths[$key], $paths);
		}
		return $this;
	}

	public function getTemplatesPath() {
		return $this->paths['templates'];
	}

	public function setTemplatesPath(array $paths) {
		$this->paths['templates'] = $paths;
		return $this;
	}

	public function getConfigPath() {
		return $this->paths['config'];
	}

	public function getWidgetsPath() {
		return $this->paths['widgets'];
	}

	public function setWidgetsPath(array $paths) {
		$this->paths['widgets'] = $paths;
		return $this;
	}

	public function getDataPath() {
		return $this->paths['data'];
	}

	public function getConfig() {
		if (!empty($this->config)) {
			return $this->config;
		}

		if (empty($this->params['conf'])) {
			throw new Exception('no configuration specified', 500);
		}

		$configName = $this->params['conf'];
		$configFile = $this->findFileOnPath($configName . '.js', $this->getConfigPath());

		if (!is_file($configFile)) {
			$configFile = $this->findFileOnPath($configName . '.json', $this->getConfigPath());
			if (!is_file($configFile)) {
				$error = "configuration file not found";
				$code = '404';
				include TEMPLATE_DIR . '/error.phtml';
				$this->shutDown(1);
			}
		}

		// get configuration
		$config = json_decode(file_get_contents($configFile), true);

		if (empty($config)) {
			$error = "You have an error in your configuration";
			$code = '500';
			include TEMPLATE_DIR . '/error.phtml';
			$this->shutDown(1);
		}

		return $config;
	}

	public function findFileOnPath($file, array $paths = array()) {
		foreach ($paths as $path) {
			if (is_readable($path . DIRECTORY_SEPARATOR . $file)) {
				return $path . DIRECTORY_SEPARATOR . $file;
			}
		}

		// fallback to php include path
		if (is_readable($file)) {
			return $file;
		}

		return false;
	}

	public function shutDown($exitCode = 0) {
		exit($exitCode);
	}

	protected function send($content) {
		echo $content;
	}

	protected function handleException(Exception $e) {
		return "Application Error :( <br /> {$e->getMessage()}";
	}
}