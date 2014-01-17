<?php
namespace Hoborg\Dashboard\Client;

class Http {

	public function get($url) {
		return file_get_contents($url);
	}

}
