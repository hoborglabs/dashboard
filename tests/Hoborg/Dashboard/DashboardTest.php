<?php
namespace Hoborg\Dashboard;

require_once 'WidgetMockProvider.php';
require_once 'MockFactory.php';

class DashboardTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	public function testRenderWithOneWidget() {
		$kernelMock = $this->mockFactory->getKernelMock();
		$kernelMock->expects($this->once())
				->method('getConfig')
				->will($this->returnValue( array(
					'widgets'=> array( array(
						'name' => 'test',
					)),
					'template' => 'test',
				) ));
		$kernelMock->expects($this->once())
				->method('findFileOnPath')
				->will($this->returnValue(__DIR__ . '/../../templates/empty.phtml'));

		$widgetMock = $this->getWidgetMock($kernelMock);

		$widgetProviderMock = $this->getWidgetMockProvider();
		$widgetProviderMock->injectMock($widgetMock);

		$dashboard = new Dashboard($kernelMock, $widgetProviderMock);
		$dashboard->render();
	}

	protected function getWidgetMock($kernel) {
		$mock = $this->getMock('\Hoborg\Dashboard\Widget',
				array('render', 'bootstrap', 'hasHead'),
				array($kernel));
		return $mock;
	}

	protected function getWidgetProviderMock() {
		$mock = $this->getMock('\Hoborg\Dashboard\WidgetProvider', array('createWidget'));
		return $mock;
	}

	protected function getWidgetMockProvider() {
		return new WidgetMockProvider();
	}
}