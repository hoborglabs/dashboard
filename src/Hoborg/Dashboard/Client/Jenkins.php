<?php
namespace Hoborg\Dashboard\Client;

class Jenkins {

	protected $jenkinsUrl = null;

	protected $config = array();

	public function __construct($jenkinsUrl, array $config = array()) {
		$this->jenkinsUrl = $jenkinsUrl;
		$this->config = $config;
	}

	public function get(array $tree, $path = '') {
		$url = $this->jenkinsUrl . '/' . $path . '/api/json?tree=';
		$url .= urlencode($this->getTreeValue($tree));

		// get data from url
		$data = file_get_contents($url);
		if (empty($data)) {
			error_log(__METHOD__ . ' No JSON data returned from: ' . $url);
			return array();
		}

		return json_decode($data, true);
	}

	public function getTreeValue(array $tree) {
		$value = '';

		foreach ($tree as $key => $val) {
			if (is_array($val)) {
				$value .= ','.$key.'['. $this->getTreeValue($val) . ']';
			} else {
				$value .= ','.$val;
			}
		}

		return substr($value, 1);
	}
}
