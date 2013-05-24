<?php
namespace Hoborg\Dashboard;

require_once 'MockFactory.php';
require_once 'WidgetProviderTestCase.php';
require_once 'WidgetProviderSpec.php';

class WidgetProviderTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	private $widgetProviderTestCase = null;

	private $spec = null;

	private $kernel = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
		$this->kernel = $this->mockFactory->getKernelMock();
		$this->widgetProviderTestCase = new WidgetProviderTestCase($this->kernel);
	}

	/**
	 * @dataProvider widgetSourcesProvider
	 */
	public function testgetWidgetSources($widgetData, $expectedSources) {
		$widget = $this->mockFactory->getWidgetMock($this->kernel);
		$widget->expects($this->once())
				->method('getData')
				->will($this->returnValue($widgetData));

		$sources = $this->widgetProviderTestCase->testGetWidgetSources($widget);
		$this->assertEquals($expectedSources, $sources);
	}

	public function widgetSourcesProvider() {
		$this->spec = new WidgetProviderSpec();
		return $this->spec->widgetSources();
	}

}