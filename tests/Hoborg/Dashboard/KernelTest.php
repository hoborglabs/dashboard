<?php
namespace Hoborg\Dashboard;

require_once 'WidgetMockProvider.php';

class KernelTest extends \PHPUnit_Framework_TestCase {

	public function testDefaultEnv() {
		$kernel = new Kernel();
		$this->assertEquals('prod', $kernel->getEnvironment());
	}

	/**
	 * @expectedException Hoborg\Dashboard\Exception
	 * @expectedExceptionCode 500
	 */
	public function testSetParamsThrowException() {
		$kernel = new Kernel();

		// there is no required 'conf' param.
		$kernel->setParams(array());
	}

	public function testSetParamsWithDefaults() {
		$kernel = new Kernel();
		$kernel->setDefaultParam('conf', 'test');

		$kernel->setParams(array());
		$this->assertEquals('test', $kernel->getParam('conf'));
	}

	public function testHandleDashboard() {
		$kernel = $this->getKernel();
		$dashboardMock = $this->getDashboardMock($kernel);

		$dashboardMock->expects($this->once())
				->method('render');

		$kernel->handle(array(), $dashboardMock);
	}

	public function testHandleWidget() {
		$kernel = $this->getKernel();
		$widgetMock = $this->getWidgetMock($kernel);
		$widgetMock->expects($this->once())
				->method('getJson');

		$widgetProvider = $this->getWidgetMockProvider();
		$widgetProvider->injectMock($widgetMock);

		$kernel->handle(array('widget' => array()), null, $widgetProvider);
	}

	protected function getKernel() {
		$kernel = new Kernel();
		$kernel->setDefaultParam('conf', 'test');

		return $kernel;
	}
	protected function getDashboardMock($kernel) {
		$mock = $this->getMock('\Hoborg\Dashboard\Dashboard', array('render'), array($kernel));
		return $mock;
	}

	protected function getWidgetMock($kernel) {
		$mock = $this->getMock('\Hoborg\Dashboard\Widget', array('getJson'), array($kernel));
		return $mock;
	}

	protected function getWidgetMockProvider() {
		return new WidgetMockProvider();
	}
}