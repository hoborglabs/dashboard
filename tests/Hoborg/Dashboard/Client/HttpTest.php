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
}
