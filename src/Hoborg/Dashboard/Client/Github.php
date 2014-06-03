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

	public function get($url) {
		$body = $this->caller->get("{$this->baseUrl}{$url}{$this->getGetParams()}");
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
