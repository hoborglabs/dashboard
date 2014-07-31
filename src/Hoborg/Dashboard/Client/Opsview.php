<?php
namespace Hoborg\Dashboard\Client;

class Opsview {

	protected $opsviewUrl = null;

	protected $config = array();

	/**
	 * Logged-in user access data
	 * @var array
	 */
	protected $accessData = array();

	public function __construct($opsviewUrl, array $config) {
		$this->opsviewUrl = $opsviewUrl;
		$this->config = $config;
		$this->http = new Http();
	}

	protected function getData($endpoint, array $params = array(), $method = 'GET', $type = 'json') {

		if (empty($this->opsviewUrl)) {
			// throw error
			return null;
		}

		$access = $this->getAccessData();
		if (empty($this->accessData)) {
			return null;
		}

		$contentType = 'application/json';
		$query = $this->http->getQueryString($params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->opsviewUrl . $endpoint . '?' . $query);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-type: {$contentType}",
			"X-Opsview-Username: {$access['username']}",
			"X-Opsview-Token: {$access['token']}",
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		//close connection
		return json_decode($result, true);
	}

	protected function getAccessData() {
		if (empty($this->accessData)) {
			$this->login();
		}

		return $this->accessData;
	}

	protected function login() {
		$post = array(
			'username' => $this->config['username'],
			'password' => $this->config['password'],
		);

		$postString = array();
		foreach ($post as $k => $v) {
			$postString[] = urlencode($k) . '=' . urlencode($v);
		}
		$postString = implode('&', $postString);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->opsviewUrl . '/rest/login');
		curl_setopt($curl, CURLOPT_POST, count($post));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		//execute post
		$result = curl_exec($curl);
		curl_close($curl);
		$accessData = json_decode($result, true);

		if (empty($accessData)) {
			// throw new exception
			return false;
		}

		$accessData['username'] = $this->config['username'];
		$this->accessData = $accessData;
		return true;
	}
}
