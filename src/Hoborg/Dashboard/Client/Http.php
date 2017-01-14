<?php
namespace Hoborg\Dashboard\Client;

class Http {

	protected $responseCode;
	protected $headers = [ ];
	protected $body;
	protected $options = [
		'etag' => false
	];

	public function __construct($options = [ ]) {
		$this->options = $options;
	}

	public function get($url, array $headers = [ ]) {
		$this->responseCode = false;
		$this->headers = array();
		$this->body = '';

		if ($this->options['etag']) {
			$body = $this->checkEtag($url);
			if ($body !== false) {
				$this->body = $body;
				return $this->body;
			}
		}

		$res = $this->httpCall($url, $headers);
		$this->body = $res['body'];
		$this->responseCode = $res['responseCode'];
		$this->headers = $res['headers'];

		if ($this->options['etag']) {
			$etag = $this->getResponseHeader('etag');
			$this->cacheEtag($url, $etag, $this->body);
		}

		return $this->body;
	}

	protected function httpCall($url, $headers = [ ]) {
		$resHeaders = [ ];
		$curl = curl_init($url);
		$headers[] = "User-Agent: hoborglabs/dashboard";

		$processHeader = function ($curl, $headerLine) use(&$resHeaders) {
			if (preg_match('#^HTTP#', $headerLine) === 1) {
				$resHeaders['http_code'] = $headerLine;
			} else if (preg_match('#^[a-zA-Z-]+:#', $headerLine) === 1) {
				list ($key, $value) = explode(': ', $headerLine);
				$resHeaders[strtolower(trim($key))] = trim($value);
			}

			return strlen($headerLine);
		};

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, $processHeader);

		$res = [
			'body' => curl_exec($curl),
			'responseCode' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
			'headers' => $resHeaders,
		];

		curl_close($curl);

		return $res;
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

	protected function checkEtag($url) {
		if (!extension_loaded('apc')) {
			return false;
		}

		$etagKey = md5('http-etag' . $url);
		$etagBodyKey = md5('http-etag-body' . $url);

		$etag = apc_fetch($etagKey);
		if (false === $etag) {
			return false;
		}

		$res = $this->httpCall($url, [ "If-None-Match: {$etag}" ]);
		if (304 === $res['responseCode']) {
			return apc_fetch($etagBodyKey);
		}

		return false;
	}

	protected function cacheEtag($url, $etag, $body) {
		$etagKey = md5('http-etag' . $url);
		$etagBodyKey = md5('http-etag-body' . $url);

		if (false !== $etag && extension_loaded('apc')) {
			apc_store($etagKey, $etag);
			apc_store($etagBodyKey, $body);
		}
	}
}
