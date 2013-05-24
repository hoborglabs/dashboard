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
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"name":"","body":""}'));

		$widget = $this->createWidget(array('test' => 'field'));
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"name":"","body":"","test":"field"}'));
	}

	public function testSetData() {
		$widget = $this->createWidget();
		$widget->extendData(array('name' => 'test'));
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('{"name":"test","body":""}')
		);

		$widget = $this->createWidget(array('body' => 'this will be overriden'));
		$widget->extendData(array('name' => 'test', 'test' => 'value'));
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('{"name":"test","body":"","test":"value"}')
		);
	}

	public function testDefaultValues() {
		$widget = $this->createWidget();
		$widget->extendData(array());
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('{"name":"","body":""}')
		);

		$widget->setDefaults(array());
		$widget->extendData(array());
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('[]')
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