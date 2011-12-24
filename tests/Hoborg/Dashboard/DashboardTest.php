<?php
namespace Hoborg\Dashboard;

require_once 'WidgetMockProvider.php';

class DashboardTest extends \PHPUnit_Framework_TestCase {

	public function testRenderWithOneWidget() {
		$kernelMock = $this->getKernelMock();
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
		$widgetMock->expects($this->once())
				->method('bootstrap')
				->will($this->returnValue($widgetMock));
		$widgetMock->expects($this->once())
				->method('hasHead')
				->will($this->returnValue(false));

		$widgetProviderMock = $this->getWidgetMockProvider();
		$widgetProviderMock->injectMock($widgetMock);

		$dashboard = new Dashboard($kernelMock, $widgetProviderMock);
		$dashboard->render();
	}

	protected function getKernelMock() {
		$mock = $this->getMock('\Hoborg\Dashboard\Kernel',
				array('getParam', 'getConfig', 'findFileOnPath'));
		return $mock;
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