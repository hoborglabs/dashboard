<?php
namespace Hoborg\Dashboard;

class Widget {

	protected $kernel = null;

	protected $defaults = array(
		'cacheable_for' => 0,
		'data' => [],
		'template' => '',
	);

	protected $data = array();

	public function __construct(Kernel $kernel, array $widgetConfig = array()) {
		$this->kernel = $kernel;
		$this->init($widgetConfig);
	}

	/**
	 * Extends widget data.
	 *
	 * @param array $data
	 */
	public function extendData(array $data) {
		$this->data = $this->arrayMergeRecursive($this->data, $data);

		return $this;
	}

	/**
	 * Bootstrap widget class
	 *
	 */
	public function bootstrap() {
		return $this;
	}

	/**
	 * Returns data used by JSONP endpoint
	 * @return array
	 */
	public function getData() {
		return array();
	}

	/**
	* Returns widget field(s).
	* You can specify key and default value. If you don't specify key you will get whole widget array.
	*
	* @param string $key
	* @param mixed $default
	*
	* @return mixed
	*/
	public function get($key = null, $default = null) {
		if (null !== $key) {
			return isset($this->data[$key]) ?
					$this->data[$key] : $default;
		}

		return $this->data;
	}

	/**
	 * Returns stringify JSON representation of widget.
	 *
	 * return string
	 */
	public function getJson() {
		return json_encode($this->data);
	}

	/**
	 * Returns JS widget class name defined in `jsClass` or 'HoborgWidget' if it's not present.
	 *
	 * @return string
	 */
	public function getJsClassName() {
		if (empty($this->data['jsClass'])) {
			return 'HoborgWidget';
		}

		return $this->data['jsClass'];
	}

	/**
	 * Sets widget data.
	 * Data array is automatically extended with default values.
	 *
	 * @param array $data
	 *
	 * @return Hoborg\Dashboard\Widget
	 */
	protected function init(array $widgetConfig) {
		$this->data = $widgetConfig;
		$this->applyDefaults();

		return $this;
	}






	/**
	 * Sets widget default values.
	 *
	 * @param array $data
	 */
	public function setDefaults(array $data) {
		$this->defaults = $data;
		$this->applyDefaults();
	}

	public function addDefaults(array $data) {
		$this->defaults = $this->arrayMergeRecursive($data, $this->defaults);
		$this->applyDefaults();
	}

	public function getAssetFiles($type) {

		if (empty($this->data['assets'][$type])) {
			return array();
		}

		$assets = $this->data['assets'][$type];
		if (!is_array($assets)) {
			$assets = array($assets);
		}

		return $assets;
	}

	public function hasJS() {
		return !empty($this->data['js']);
	}

	public function getJS() {
		$js = $this->get('js', array());
		if (!is_array($js)) {
			$js = array($js);
		}

		return $js;
	}

	public function hasCSS() {
		return !empty($this->data['css']);
	}

	public function getCSS() {
		$css = $this->get('css', array());
		if (!is_array($css)) {
			$css = array($css);
		}

		return $css;
	}

	private function arrayMergeRecursive($arrayA, $arrayB) {
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

	private function applyDefaults() {
		$this->data = $this->arrayMergeRecursive($this->defaults, $this->data);
	}
}
