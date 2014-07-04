<?php
namespace Hoborg\Dashboard\Client;

class GithubTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Hoborg\Dashboard\Client\Github
	 */
	protected $fixture = null;
	protected $httpMock = null;

	public function setUp() {
		$this->httpMock = $this->getMock('\\Hoborg\\Dashboard\\Client\\Http', array('get'));
		$this->fixture = new \Hoborg\Dashboard\Client\Github('http://api.github.test', $this->httpMock);
	}

	/** @test */
	public function shouldPassValueFromFakeResponse() {
		$this->httpMock->expects($this->once())
			->method('get')
			->with('http://api.github.test/test')
			->will($this->returnValue('{"test": "value"}'));
		$response = $this->fixture->get('/test');

		$this->assertEquals(array('test' => 'value'), $response);
	}

	public function testPassAccessTokenToCaller() {
		$this->httpMock->expects($this->once())
			->method('get')
			->with('http://api.github.test/test?access_token=123abc');
		$this->fixture->setAccessToken('123abc');

		$response = $this->fixture->get('/test');
	}

}
