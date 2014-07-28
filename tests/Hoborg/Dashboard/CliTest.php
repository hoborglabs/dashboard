<?php
namespace Hoborg\Dashboard;

require_once 'WidgetMockProvider.php';
require_once 'MockFactory.php';

class CliTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	/** @test */
	public function shouldAcceptKernel() {
		$kernel = $this->mockFactory->getKernelMock();
		$cli = new Cli($kernel);

		$this->assertInstanceOf('Hoborg\Dashboard\Cli', $cli);
	}

	/** @test */
	public function shouldWarnWhenCommandNotFound() {
		$kernel = $this->mockFactory->getKernelMock(array('log'));
		$cli = new Cli($kernel);

		$kernel->expects($this->at(1))
				->method('log')
				->with($this->stringContains('unknown command widget.WithCli.simpleeee'));

		$cli->handle(array('c' => 'widget.WithCli.simpleeee'));
	}

	/** @test */
	public function shoulLoadCliCommand() {
		$kernel = $this->mockFactory->getKernelMock(array('log'));
		$cli = new Cli($kernel);

		$kernel->expects($this->at(2))
				->method('log')
				->with($this->stringContains('Done'));

		$cli->handle(array('c' => 'widget.WithCli.simple'));
	}
}
