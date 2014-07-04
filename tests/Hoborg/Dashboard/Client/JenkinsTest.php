<?php
namespace Hoborg\Dashboard\Client;

class JenkinsTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Hoborg\Dashboard\Client\Github
	 */
	protected $fixture = null;
	protected $httpMock = null;

	public function setUp() {
		$this->httpMock = $this->getMock('\\Hoborg\\Dashboard\\Client\\Http', array('get'));
		$this->fixture = new \Hoborg\Dashboard\Client\Jenkins('http://jenkins.local', array(), $this->httpMock);
	}

	/** @test
	 * @dataProvider treeQueryData
	 */
	public function shouldReturnValidTreeQuery($treeArray, $expected) {
		$actual = $this->fixture->getTreeValue($treeArray);

		$this->assertEquals($expected, $actual);
	}

	public function treeQueryData() {
		return array(
			array(
				array(),
				''
			),
			array(
				array('a' => array('a')),
				'a[a]'
			),
			array(
				array(
					'a',
					'b' => array('a', 'b'),
					'c' => array('a', 'b' => array('c', 'd')),
				),
				'a,b[a,b],c[a,b[c,d]]'
			),
		);
	}


	/** @test */
	public function shouldUrlEscapeTreeParam() {
		$treeArray = array('a', 'b' => array('aa'), 'c');
		$treeString = 'a,b[aa],c';
		$apiResponse = json_encode(array(
			'a' => 'a value',
			'b' => array(
				'aa' => 'aa value'
			),
			'c' => 'c value'
		));

		$this->httpMock->expects($this->once())
			->method('get')
			->with('http://jenkins.local/job/test/api/json?tree=' . urlencode($treeString))
			->will($this->returnValue($apiResponse));

		$response = $this->fixture->get($treeArray, '/job/test');
	}

}
