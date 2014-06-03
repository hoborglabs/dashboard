<?php
namespace Hoborg\Dashboard\Client\Graphite;

use Hoborg\Dashboard\MockFactory;

class TargetTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Hoborg\Dashboard\MockFactory
	 */
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	public function testGetDataFromGraphiteClient() {
		$graphite = $this->mockFactory->getGraphiteClientMock();
		$target = new Target($graphite, 'test.target', array(), array());

		$graphite->expects($this->once())
			->method('getData')
			->with('test.target');

		$target->data();
	}

	public function testAddFunction() {
		$graphite = $this->mockFactory->getGraphiteClientMock();
		$target = new Target($graphite, 'test.target', array(), array());

		$graphite->expects($this->once())
			->method('getData')
			->with('test.target', array(
				'keepLastValue' => true,
				'scale' => 10
			));

		$target->fcn('keepLastValue', true)
			->fcn('scale', 10);

		$target->data();
	}

	/**
	 * @dataProvider dataAverage
	 */
	public function testAverage($expectedAvg, $mockData) {
		$graphite = $this->mockFactory->getGraphiteClientMock();
		$target = new Target($graphite, 'test.target', array(), array());

		$graphite->expects($this->once())
			->method('getData')
			->will($this->returnValue($mockData));

		$avg = $target->avg();
		$this->assertEquals($expectedAvg, $avg);
	}

	public function dataAverage() {

		return array(
			array(
				null,
				array()
			),
			array(
				null,
				$this->graphiteDataFromValues(array())
			),
			array(
				2,
				$this->graphiteDataFromValues(array(1, 2, 3))
			),
			array(
				2,
				$this->graphiteDataFromValues(array(null, 1, null, 2, null, 3, null))
			),
			array(
				1,
				$this->graphiteDataFromValues(array(null, 1, null, null))
			),
		);
	}

	/**
	 * @dataProvider dataStatistical
	 */
	public function testStatisctiaclData($expectedStats, $mockData) {
		$graphite = $this->mockFactory->getGraphiteClientMock();
		$target = new Target($graphite, 'test.target', array(), array());

		$graphite->expects($this->once())
			->method('getData')
			->will($this->returnValue($mockData));

		$stats = $target->stats();
		$this->assertEquals($expectedStats, $stats);
	}

	public function dataStatistical() {
		$makeStats = function($min, $max, $avg) {
			return array('min' => $min, 'max' => $max, 'avg' => $avg);
		};
		return array(
			array(
				$makeStats(5, 5, 5),
				$this->graphiteDataFromValues(array(5))
			),
			array(
				$makeStats(1, 3, 2),
				$this->graphiteDataFromValues(array(1, null, 2, null, null, 3, null))
			)
		);
	}

	protected function graphiteDataFromValues($dataValues) {
		$datapoints = array();
		foreach ($dataValues as $value) {
			$datapoints[] = array($value, time());
		}
		return array(array(
			'target' => 'test.target',
			'datapoints' => $datapoints
		));
	}
}
