<?php
namespace Hoborg\Dashboard\Client;

class GraphiteTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Hoborg\Dashboard\Client\Graphite
	 */
	protected $fixture = null;

	public function setUp() {
		$this->fixture = $this->getMock('Hoborg\Dashboard\Client\Graphite',
			array('getJsonData'),
			array('http://graphite.local')
		);
	}

	/**
	 * @dataProvider getTargetsDataProvider
	 */
	public function testGetTargetsData($expectedUrl, $targets, $from, $to) {
		$graphiteUrl = 'http://graphite.local';
		$expectedUrl = $graphiteUrl . $expectedUrl;
		$this->fixture->expects($this->once())
			->method('getJsonData')
			->with($this->equalTo($expectedUrl));

		$this->fixture->getTargetsData($graphiteUrl, $targets, $from, $to);
	}

	public function getTargetsDataProvider() {
		return array(
			array(
				'/render?from=-10min&to=now&target=a.b.c',
				array('a.b.c'),
				'-10min',
				'now'
			),
			array(
				'/render?from=-2days&to=today&target=a.b.c&target=x.y.z',
				array('a.b.c', 'x.y.z'),
				'-2days',
				'today'
		)
		);
	}

	/**
	 * @dataProvider getAvgTargetValueUrlProvider
	 */
	public function testGetAvgTargetValueUrl($expectedUrl, $target, $options) {
		$graphiteUrl = 'http://graphite.local';
		$expectedUrl = $graphiteUrl . $expectedUrl;

		$this->fixture->expects($this->once())
			->method('getJsonData')
			->with($this->equalTo($expectedUrl))
			->will($this->returnValue(array()));

		$this->fixture->getAvgTargetValue($target, $graphiteUrl, $options);
	}

	public function getAvgTargetValueUrlProvider() {
		return array(
			array(
				'/render?target=a.b.c&from=-10min&to=now',
				'a.b.c',
				array('from' => '-10min', 'to' => 'now'),
			),
			array(
				'/render?target=a.b.c',
				'a.b.c',
				array(),
			),
		);
	}

	/**
	* @dataProvider getAvgTargetValueProvider
	*/
	public function testGetAvgTargetValue($expectedAvg, $data, $nullAsZero) {
		$graphiteUrl = 'http://graphite.local';

		$this->fixture->expects($this->once())
			->method('getJsonData')
			->will($this->returnValue($data));

		$actualAvg = $this->fixture->getAvgTargetValue('mock.it', $graphiteUrl, array(), $nullAsZero);
		$this->assertEquals($expectedAvg, $actualAvg);
	}

	public function getAvgTargetValueProvider() {
		return array(
			array(
				2, // avg from (1, 3)
				array(
					array(
						'datapoints' => array(
							array(1, 123456789),
							array(null, 123456789),
							array(3, 123456789),
						)
					)
				),
				false,
			),
			array(
				1, // avg from (1, 0, 3, 0)
				array(
					array(
						'datapoints' => array(
							array(1, 123456789),
							array(null, 123456789),
							array(3, 123456789),
							array(null, 123456789),
						)
					)
				),
				true,
			),
		);
	}

	/**
	 * @dataProvider getTargetsStatisticalDataProvider
	 */
	public function testGetTargetsStatisticalData($expectedStats, $data, $avgSpan, $nullAsZero) {
		$graphiteUrl = 'http://graphite.local';

		$this->fixture->expects($this->once())
			->method('getJsonData')
			->will($this->returnValue($data));

		$actualStats = $this->fixture->getTargetsStatisticalData($graphiteUrl, array('mock.it'), '-3min', 'now',
				$avgSpan, $nullAsZero);
		$this->assertEquals($expectedStats, $actualStats);
	}

	public function getTargetsStatisticalDataProvider() {
		return array(
			array(
				//stats
				array( array(
					'target' => 'test.target',
					'min' => 1,
					'max' => 3,
					'avg' => 2,
				)),
				array(
					array(
						'target' => 'test.target',
						'datapoints' => array(
							array(1, 123456789),
							array(null, 123456789),
							array(3, 123456789),
						)
					)
				),
				6, false
			),
			// AVG is calculated from last 3 datapoints
			array(
				//stats
				array( array(
					'target' => 'test.target',
					'min' => -456,
					'max' => 123,
					'avg' => 4/2, // nullAsZero = false, so only two points to AVG from
				)),
				array( array(
					'target' => 'test.target',
					'datapoints' => array(
						array(123, 123456789),
						array(-456, 123456789),
						// last 3 data points
						array(1, 123456789),
						array(null, 123456789),
						array(3, 123456789),
					)
				)),
				3, false
			),

			// AVG is calculated from last 3 datapoints
			array(
				//stats
				array( array(
					'target' => 'test.target',
					'min' => -456,
					'max' => 123,
					'avg' => 4/3, // nullAsZero = true,
				)),
				array( array(
					'target' => 'test.target',
					'datapoints' => array(
						array(123, 123456789),
						array(-456, 123456789),
						// last 3 data points
						array(1, 123456789),
						array(null, 123456789),
						array(3, 123456789),
					)
				)),
				3, true
			),

			// AVG span > datapoints count
			array(
				//stats
				array( array(
					'target' => 'test.target',
					'min' => 1,
					'max' => 1,
					'avg' => 1, // nullAsZero = true,
				)),
				array( array(
					'target' => 'test.target',
					'datapoints' => array(
						array(1, 123456789),
					)
				)),
				10, true
			),
		);
	}
}
