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

	public function testDefaultEnv() {
		$kernel = new Kernel(TST_ROORT);
		$this->assertEquals('prod', $kernel->getEnvironment());
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

		$widgetProvider = $this->getWidgetMockProvider();
		$widgetProvider->injectMock($widgetMock);

		$kernel->handle(array('widget' => array()), null, $widgetProvider);
	}

	protected function getKernel() {
		$kernel = new Kernel(TST_ROORT);
		$kernel->setDefaultParam('conf', 'test');

		return $kernel;
	}

	protected function getWidgetMockProvider() {
		return new WidgetMockProvider();
	}
}