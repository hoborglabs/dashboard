<?php
namespace Hoborg\DashboardCache;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class KernelTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider url404provider
	 */
	public function test404Response($url) {
		$kernel = new Kernel();
		$request = new Request();
		$request->server->set('REQUEST_URI', $url);
		$response = new Response();

		$kernel->handle($request, $response);
		$this->assertEquals('404', $response->getStatusCode());
	}

	public function url404provider() {
		return array(
			array(''),
			array('/'),
			array('/loremipsum'),
			array('/lorem/ipsum'),
			array('/lorem/ipsum/dolor'),
			array('/api/loremipsum'),
			array('/api/1/loremipsum'),
		);
	}

}