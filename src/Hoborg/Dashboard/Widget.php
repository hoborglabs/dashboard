<?php
namespace Hoborg\Dashboard;

class Widget {

	protected $kernel = null;

	protected $data = array(
		'name' => '',
		'body' => '',
	);

	public function __construct(Kernel $kernel, array $data = array()) {
		$this->kernel = $kernel;
		if (!empty($data)) {
			$this->setData($data);
		}
	}

	public function setData(array $data) {
		$this->data = $data;
	}

	public function bootstrap() {
		// first load static content (body only
		$bodyFields = array(
			'static',
			'url',
		);
		foreach ($this->data as $key => $value) {
			if (in_array($key, $bodyFields)) {
				if (!is_array($value)) {
					$value = array($value);
				}
				$this->loadBody($key, $value);
			}
		}

		// now load dynamic content
		$cgiFields = array(
			'cgi',
			'php',
		);
		foreach ($this->data as $key => $value) {
			if (in_array($key, $cgiFields)) {
				if (!is_array($value)) {
					$value = array($value);
				}
				$this->loadWidget($key, $value);
			}
		}

		return $this;
	}

	public function hasHead() {
		return !empty($this->data['head']);
	}

	public function loadBody($type, $sources) {
		if (empty($sources)) {
			$this->data['hasBody'] = 0;
			$this->data['body'] = '';
		}

		if ('static' ==  $type) {
			foreach ($sources as $src) {
				$body = $this->loadBodyFromStatic($src);
				if (!empty($body)) {
					$this->data['body'] = $body;
					return;
				}
			}
		} else if ('url' == $type) {
			foreach ($sources as $src) {
				$body = $this->loadBodyFromUrl($src);
				if (!empty($body)) {
					$this->data['body'] = $body;
					return;
				}
			}
		}
	}

	public function loadWidget($type, $sources) {
		if (empty($sources)) {
			$this->data['hasBody'] = 0;
			$this->data['body'] = '';
		}

		if ('cgi' == $type) {
			foreach ($sources as $src) {
				$w = $this->loadWidgetFromCgi($src);
				if (!empty($w)) {
					$this->data = $w;
					return;
				}
			}
		} else if ('php' == $type) {
			foreach ($sources as $src) {
				$w = $this->loadWidgetFromPhp($src);
				if (!empty($w)) {
					$this->data = $w;
					return;
				}
			}
		}
	}

	public function setDefaults(array $data) {
		$this->data = $this->arrayMergeRecursive($data, $this->data);
	}

	public function getJson() {
		return json_encode($this->data);
	}

	protected function loadWidgetFromPhp($src) {
		$path = $this->kernel->findFileOnPath(
			$src,
			$this->kernel->getWidgetsPath()
		);

		if ($path) {
			$widget = & $this->data;
			return include $path;
		}

		return false;
	}

	protected function loadWidgetFromCgi($src) {
		return false;
	}

	protected function loadBodyFromStatic($src) {
		$path = $this->kernel->findFileOnPath(
			$src,
			$this->kernel->getWidgetsPath()
		);

		$body = file_get_contents($path);
		return $body;
	}

	protected function loadBodyFromUrl($src) {
		return false;
	}

	protected function arrayMergeRecursive($arrayA, $arrayB) {
		// merge arrays if both variables are arrays
		if (is_array($arrayA) && is_array($arrayB)) {
			// loop through each right array's entry and merge it into $arrayA
			foreach ($arrayB as $key => $value) {
				if (isset($arrayA[$key])) {
					$arrayA[$key] = $this->arrayMergeRecursive($arrayA[$key], $value);
				} else {
					if ($key === 0) {
						$arrayA= array(0 => $this->arrayMergeRecursive($arrayA, $value));
					} else {
						$arrayA[$key] = $value;
					}
				}
			}
		} else {
			// one of values is not an array
			$arrayA = $arrayB;
		}

		return $arrayA;
	}
}