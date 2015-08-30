<?php
namespace Hoborg\Dashboard;

require_once 'WidgetMockProvider.php';
require_once 'MockFactory.php';

class KernelTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	public function testThrowConfigurationParseError() {
		$kernel = $this->mockFactory->getKernelMock(array('getParam', 'handleError'));
		$kernel->expects($this->once())
				->method('handleError')
				->with($this->stringContains('Parse error on line 2:'));

		// prepare Kernel and try to get config from invalid json
		$kernel->setParams(array('conf' => 'not-valid'));
		$kernel->getConfig();
	}

	/**
	 * @expectedException Hoborg\Dashboard\Exception
	 * @expectedExceptionCode 500
	 */
	public function testSetParamsThrowException() {
		$kernel = new Kernel(TST_ROORT);

		// there is no required 'conf' param.
		$kernel->setParams(array());
	}

	public function testSetParamsWithDefaults() {
		$kernel = new Kernel(TST_ROORT);
		$kernel->setDefaultParam('conf', 'test');

		$kernel->setParams(array());
		$this->assertEquals('test', $kernel->getParam('conf'));
	}

	public function testHandleDashboard() {
		$kernel = $this->getKernel();
		$dashboardMock = $this->mockFactory->getDashboardMock($kernel);
		$dashboardMock->expects($this->once())
				->method('render');

		$kernel->handle(array(), $dashboardMock);
	}

	public function testHandleWidget() {
		$kernel = $this->getKernel();
		$widgetMock = $this->mockFactory->getWidgetMock($kernel);
		$widgetMock->expects($this->once())
				->method('getJson');

		$widgetProvider = $this->getWidgetMockProvider($kernel);
		$widgetProvider->injectMock($widgetMock);

		$kernel->handle(array('widget' => '{}'), null, $widgetProvider);
	}

	protected function getKernel() {
		$kernel = new Kernel(TST_ROORT);
		$kernel->setDefaultParam('conf', 'test');

		return $kernel;
	}

	protected function getWidgetMockProvider($kernel) {
		return new WidgetMockProvider($kernel);
	}
}
