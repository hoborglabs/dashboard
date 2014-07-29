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
	 * @test
	 * Simple test to make sure that one can access data from created widget.
	 */
	public function shouldCreateRowWidget() {
		$actualWidget = $this->widgetProviderTestCase->createRowWidget(array('testKey' => 'test value'));

		$this->assertEquals('test value', $actualWidget->get('testKey'));
	}

	/**
	 * @test
	 * @dataProvider widgetSourcesProvider
	 */
	public function shouldGetWidgetSources($widgetData, $expectedSources) {
		$widget = $this->mockFactory->getWidgetMock($this->kernel);
		$widget->expects($this->once())
				->method('get')
				->will($this->returnValue($widgetData));

		$sources = $this->widgetProviderTestCase->testGetWidgetSources($widget);
		$this->assertEquals($expectedSources, $sources);
	}

	public function widgetSourcesProvider() {
		$this->spec = new WidgetProviderSpec();
		return $this->spec->widgetSources();
	}

	/**
	 * @test
	 */
	public function shouldCreateWidget() {
		$widget = $this->widgetProviderTestCase->createWidget(array(
			'php' => 'empty.php'
		));

		$this->assertEquals('empty.php', $widget->get('php'));
	}

	/**
	 * @test
	 */
	public function shouldCreateWidgetFromStaticFileAndPhp() {
		$kernel = $this->mockFactory->getKernelMock(array('a'));
		$widgetProviderTestCase = new WidgetProvider($kernel);

		$widget = $widgetProviderTestCase->createWidget(array(
			'php' => 'empty.php',
			'static' => 'simple-key.json',
		));

		$this->assertEquals('empty.php', $widget->get('php'));
		$this->assertEquals('key', $widget->get('simple'));
	}

	/**
	 * @test
	 */
	public function shouldMergeDataAndDefaultsFromStaticFileAndPhp() {
		$kernel = $this->mockFactory->getKernelMock(array('a'));
		$widgetProviderTestCase = new WidgetProvider($kernel);

		$widget = $widgetProviderTestCase->createWidget(array(
			'php' => 'simple.php',
			'static' => array( 'simple-data-a.json', 'simple-data-b.json' )
		));
		$wData = $widget->get('data');

		$this->assertEquals('This is A', $wData['a']);
		$this->assertEquals('This is B', $wData['b']);
		$this->assertEquals('This is C', $wData['c']);
	}

	/**
	* @test
	*/
	public function shouldMergeDataFromMultiplePhpFiles() {
		$kernel = $this->mockFactory->getKernelMock(array('a'));
		$widgetProviderTestCase = new WidgetProvider($kernel);

		$widget = $widgetProviderTestCase->createWidget(array(
			'php' => array( 'simple-data-a.php', 'simple-data-b.php' )
		));
		$wData = $widget->get('data');

		$this->assertEquals('This is A', $wData['a']);
		$this->assertEquals('This is B', $wData['b']);
	}
}
