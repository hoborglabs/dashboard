<?php
namespace Hoborg\Dashboard\Client;

class Http {

	public function get($url, array $headers = array()) {
		return file_get_contents($url);
	}

	public function getQueryString(array $params) {
		$getQuery = array();
		foreach ($params as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $val) {
					$getQuery[] = urlencode($k) . '=' . urlencode($val);
				}
			} else {
				$getQuery[] = urlencode($k) . '=' . urlencode($v);
			}
		}

		return implode('&', $getQuery);
	}

}
