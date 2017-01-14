<?php
namespace Hoborg\Dashboard\Client;

class Github {

	protected $caller;
	protected $baseUrl;
	protected $githubOption = array();

	public function __construct($baseUrl, Http $caller = null) {
		if (null === $caller) {
			$caller = new Http();
		}
		$this->caller = $caller;
		$this->baseUrl = $baseUrl;
	}

	public function checkEtag($url, $etagKey, $etagBodyKey) {
		if (!extension_loaded('apc')) {
			return false;
		}

		$etag = apc_fetch($etagKey);
		if (false === $etag) {
			return false;
		}

		$this->caller->get($url, array("If-None-Match: {$etag}"));
		if (304 === $this->caller->getResponseCode()) {
			return apc_fetch($etagBodyKey);
		}

		return false;
	}

	public function get($url) {
		$headers = array();

		$url = "{$this->baseUrl}{$url}{$this->getGetParams()}";
	
		$etagKey = md5('github-etag' . $url);
		$etagBodyKey = md5('github-etag-body' . $url);

		$body = $this->checkEtag($url, $etagKey, $etagBodyKey);

		if ($body !== false) {
			return json_decode($body, true);
		}

		$body = $this->caller->get($url);
		$etag = $this->caller->getResponseHeader('etag');
		
		if (false !== $etag && extension_loaded('apc')) {
			apc_store($etagKey, $etag);
			apc_store($etagBodyKey, $body);
		}

		return json_decode($body, true);
	}

	public function setAccessToken($accessToken) {
		$this->githubOption['accessToken'] = $accessToken;
	}

	protected function getGetParams() {
		$get = array();
		if (!empty($this->githubOption['accessToken'])) {
			$get[] = 'access_token=' . urlencode($this->githubOption['accessToken']);
		}

		if (empty($get)) {
			return '';
		}
		return '?' . implode('&', $get);
	}
}
