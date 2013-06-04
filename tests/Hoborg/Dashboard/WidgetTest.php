<?php
namespace Hoborg\Dashboard;

require_once 'MockFactory.php';

class WidgetTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	public function testConstructor() {
		$widget = $this->createWidget();
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"data":[],"template":""}'));

		$widget = $this->createWidget(array('test' => 'field'));
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"data":[],"template":"","test":"field"}'));
	}

	public function testSetData() {
		$widget = $this->createWidget();
		$widget->extendData(array('name' => 'test'));
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('{"name": "test", "data": [], "template": ""}')
		);

		$widget = $this->createWidget(array('test' => 'this will be overriden'));
		$widget->extendData(array('name' => 'test', 'test' => 'value'));
		$this->assertEquals(
			json_decode($widget->getJson(), true),
			array(
				'template' => '',
				'data' => array (),
				'name' => 'test',
				'test' => 'value',
			)
		);
	}

	public function testDefaultValues() {
		$widget = $this->createWidget();
		$widget->extendData(array());
		$this->assertEquals(
			json_decode($widget->getJson(), true),
			array(
				'template' => '',
				'data' => array (),
			)
		);
	}

	public function testHasHead() {
		$kernel = $this->mockFactory->getKernelMock();
		$widget = new Widget($kernel);
		$widget->extendData(array('head' => 'test head'));
		$this->assertTrue($widget->hasHead());
	}

	/**
	 * @param array $data
	 *
	 * @return Hoborg\Dashboard\Widget
	 */
	private function createWidget(array $data = array()) {
		$kernel = $this->mockFactory->getKernelMock();
		$widget = new Widget($kernel, $data);

		return $widget;
	}
}