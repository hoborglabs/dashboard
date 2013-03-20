<?php
namespace Hoborg\DashboardCache;

use Symfony\Component\HttpFoundation\Response;

abstract class Api {

	protected function jsonSuccess(array $data, Response $response) {
		$response->setStatusCode(200);
		$response->headers->set('Content-Type', 'application/json');
		$response->setContent(json_encode($data));
	}

	protected function jsonError($msg, $code, Response $response) {
		$response->setStatusCode($code);
		$response->headers->set('Content-Type', 'application/json');
		$response->setContent(json_encode(array(
					't' => time(),
					'data' => null,
					'error' => array('message' => $msg),
		)));
	}
}