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
	);

	public function __construct($env = 'prod') {
		$this->environment = $env;

		$this->paths['templates'][] = H_D_ROOT . '/templates';
	}

	public function handle(array $params, Dashboard $dashboard = null, WidgetProvider $widgetProvider = null) {
		try {
			$this->setParams($params);

			if ($this->hasWidgetParam()) {
				// render selected widget
				$widget = null;
				if (null == $widgetProvider) {
					$widget = new Widget($this);
				} else {
					$widget = $widgetProvider->createWidget($this, $this->getParam('widget'));
				}
				$this->send($widget->render());
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

	public function hasWidgetParam() {
		return array_key_exists('widget', $this->params);
	}

	public function getEnvironment() {
		return $this->environment;
	}

	public function setDefaultParam($key, $value) {
		$this->defaultParams[$key] = $value;
	}

	public function setParams(array $params) {
		$params = $this->defaultParams + $params;

		if (empty($params['conf'])) {
			throw new Exception('Missing `conf` parameter', 500);
		}

		$this->params = $params;
	}

	public function getParam($key, $default = null) {
		return isset($this->params[$key]) ? $this->params[$key] : $default;
	}

	public function getTemplatesPath() {
		return $this->paths['templates'];
	}

	public function setTemplatesPath(array $paths) {
		$this->paths['templates'] = $paths;
		return $this;
	}

	public function getConfig() {
		if (empty($this->params['conf'])) {
			throw new Exception('no configuration specified', 500);
		}

		$configName = $this->params['conf'];

		$configFile = CONFIG_DIR .'/' . $configName . '.js';
		if (!is_file($configFile)) {
			$configFile = CONFIG_DIR .'/' . $configName . '.json';
			if (!is_file($configFile)) {
				$error = "configuration file not found";
				$code = '404';
				include TEMPLATE_DIR . '/error.phtml';
				die(1);
			}
		}

		// get configuration
		$config = json_decode(file_get_contents($configFile), true);

		if (empty($config)) {
			$error = "You have an error in your configuration";
			$code = '500';
			include TEMPLATE_DIR . '/error.phtml';
			die(1);
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

	protected function send($content) {
		echo $content;
	}

	protected function handleException(Exception $e) {
		return "Application Error :( <br /> {$e->getMessage()}";
	}
}