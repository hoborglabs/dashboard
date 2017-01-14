<?php
namespace Hoborg\Dashboard\Client;

class Http {

	protected $responseCode;
	protected $headers = array();
	protected $body;

	public function get($url, array $headers = array()) {
		$this->responseCode = false;
		$this->headers = array();
		$this->body = '';

		$curl = curl_init($url);

		$headers[] = "User-Agent: hoborglabs/dashboard";

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'processHeader'));
		$this->body = curl_exec($curl);
		$this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $this->body;
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

	public function getResponseCode() {
		return $this->responseCode;
	}

	public function getResponseHeaders() {
		return $this->headers;
	}

	public function getResponseHeader($header) {
		if (array_key_exists($header, $this->headers)) {
			return $this->headers[$header];
		}
		return false;
	}

	private function processHeader($curl, $headerLine) {
		if (preg_match('#^HTTP#', $headerLine) === 1) {
			$this->headers['http_code'] = $headerLine;	
		} else {
			list ($key, $value) = explode(': ', $headerLine);
			$this->headers[strtolower(trim($key))] = trim($value);
		}
		return strlen($headerLine);
	}
}
