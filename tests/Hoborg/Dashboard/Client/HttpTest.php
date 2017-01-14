<?php
namespace Hoborg\Dashboard\Client;

class HttpTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\Client\Http
	*/
	protected $fixture = null;

	public function setUp() {
		$this->fixture = new Http();
	}

	/** @test
	 * @dataProvider buildQueryStringData
	 */
	public function shoudBuildQueryString(array $parameters, $expectedQuery) {
		$actual = $this->fixture->getQueryString($parameters);

		$this->assertEquals($expectedQuery, $actual);
	}

	public function buildQueryStringData() {
		return array(
			array(
				array(),
				''
			),
			array(
				array('a' => 'b'),
				'a=b'
			),
			array(
				array('a:b' => 'c#d'),
				'a%3Ab=c%23d'
			),
			array(
				array('a' => array('b', 'c')),
				'a=b&a=c'
			),
			array(
				array('a:b' => array('c%d', 'e&f')),
				'a%3Ab=c%25d&a%3Ab=e%26f'
			),
		);
	}

	/** @test
	 */
	public function shouldUseEtagCache() {
		$mock = $this->getMockBuilder('\Hoborg\Dashboard\Client\Http')
			->setConstructorArgs([ [ 'etag' => true ] ])
			->setMethods([ 'getResponseCode', 'httpCall' ])
			->getMock();

		$url = 'http://test.http/request.html';
		$body = 'this is test body';
		$etag = 'fake-etag';
		$etagKey = md5('http-etag' . $url);
		$etagBodyKey = md5('http-etag-body' . $url);

		if (!extension_loaded('apc')) {
			$this->markTestIncomplete(
				'APC extension not loaded, skipping APC Etag test'
			);
		}

		apc_store($etagKey, $etag);
		apc_store($etagBodyKey, $body);
		$mock->method('getResponseCode')
			->willReturn(304);
		$mock->method('httpCall')
			->willReturn('');

		$t = $mock->get('http://test.http/request.html');
		$this->assertEquals($body, $t);
	}
}
