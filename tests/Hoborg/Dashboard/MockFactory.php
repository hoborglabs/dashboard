<?php
namespace Hoborg\Dashboard;

class MockFactory {

	private $testCase = null;

	public function __construct(\PHPUnit_Framework_TestCase $testCase) {
		$this->testCase = $testCase;
	}

	public function getDashboardMock($kernel) {
		$mock = $this->testCase->getMock('\Hoborg\Dashboard\Dashboard',
			array('render'),
			array($kernel)
		);

		return $mock;
	}

	public function getWidgetMock($kernel) {
		$mock = $this->testCase->getMock('\Hoborg\Dashboard\Widget',
			array('getJson', 'get', 'getData'),
			array($kernel)
		);

		return $mock;
	}

	public function getKernelMock() {
		$mock = $this->testCase->getMock('\Hoborg\Dashboard\Kernel',
			array('getParam', 'getConfig', 'findFileOnPath'),
			array(TST_ROORT)
		);

		return $mock;
	}
}