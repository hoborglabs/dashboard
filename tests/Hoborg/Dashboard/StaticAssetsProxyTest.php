<?php
namespace Hoborg\Dashboard;

require_once 'MockFactory.php';

class StaticAssetsProxyTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	private $kernel = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
		$this->kernel = $this->mockFactory->getKernelMock(array('log'));
	}

	/**
	 * @test
	 */
	public function shouldAcceptKernelOnCreation() {
		$proxy = new StaticAssetsProxy($this->kernel);

		$this->assertInstanceOf('Hoborg\Dashboard\StaticAssetsProxy', $proxy);
	}

	/**
	 * @test
	 */
	public function shouldProxyWidgetsFiles() {
		$proxy = $this->getMock('\Hoborg\Dashboard\StaticAssetsProxy',
			array('proxy'),
			array($this->kernel)
		);

		$proxy->expects($this->once())
				->method('proxy')
				->with(TST_ROORT . '/widgets/simple.php');

		$proxy->output('widgets/simple.php');
	}

	/**
	* @test
	*/
	public function shouldProxytemplatesFiles() {
		$proxy = $this->getMock('\Hoborg\Dashboard\StaticAssetsProxy',
			array('proxy'),
			array($this->kernel)
		);

		$proxy->expects($this->once())
				->method('proxy')
				->with(TST_ROORT . '/templates/empty.phtml');

		$proxy->output('templates/empty.phtml');
	}

	/**
	* @test
	*/
	public function shouldErrorOnWrongAssetPath() {
		$proxy = $this->getMock('\Hoborg\Dashboard\StaticAssetsProxy',
			array('proxy'),
			array($this->kernel)
		);

		$this->kernel->expects($this->at(0))
				->method('log')
				->with($this->stringContains('404'));

		$proxy->output('not-existing-type/test');
	}
}
