<?php
namespace Hoborg\Dashboard\Client;

class Http {

	public function get($url, array $headers = array()) {
		$curl = curl_init($url);

		$headers = array("User-Agent: hoborglabs/dashboard");

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		return curl_exec($curl);
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
