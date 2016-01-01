<?php
namespace Hoborg\Dashboard;

require_once 'MockFactory.php';

class WidgetTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\MockFactory
	*/
	private $mockFactory = null;

	public function setup() {
		$this->mockFactory = new MockFactory($this);
	}

	public function testConstructor() {
		$widget = $this->createWidget();
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"data":[],"template":"","cacheable_for":0}'));

		$widget = $this->createWidget(array('test' => 'field'));
		$this->assertEquals(json_decode($widget->getJson()), json_decode('{"data":[],"template":"","cacheable_for":0,"test":"field"}'));
	}

	/** @test */
	public function shouldApplyDefaultsOnInit() {
		$kernel = $this->mockFactory->getKernelMock();
		$widget = new WidgetDefaultTestCase($kernel, array('config' => array('test' => 'value')));

		$config = $widget->get('config');
		// `test` is coming from initial data
		$this->assertEquals('value', $config['test']);
		// `view` is coming from WidgetDefaultTestCase::defaults
		$this->assertEquals('test-default', $config['view']);
	}

	/** @test */
	public function shouldReturnSelfOnBootstrap() {
		$widget = $this->createWidget();
		$this->assertEquals($widget, $widget->bootstrap());
	}



	public function testSetData() {
		$widget = $this->createWidget();
		$widget->extendData(array('name' => 'test'));
		$this->assertEquals(
			json_decode($widget->getJson()),
			json_decode('{"name": "test", "data": [], "template": "","cacheable_for":0}')
		);

		$widget = $this->createWidget(array('test' => 'this will be overriden'));
		$widget->extendData(array('name' => 'test', 'test' => 'value'));
		$this->assertEquals(
			json_decode($widget->getJson(), true),
			array(
				'template' => '',
				'data' => array (),
				'cacheable_for' => 0,
				'name' => 'test',
				'test' => 'value',
			)
		);
	}

	public function testDefaultValues() {
		$widget = $this->createWidget();
		$widget->extendData(array());
		$this->assertEquals(
			json_decode($widget->getJson(), true),
			array(
				'template' => '',
				'data' => array (),
				'cacheable_for' => 0,
			)
		);
	}

	/** @test */
	public function shouldAccessDataByKeyName() {
		$widget = $this->createWidget(array('testKey' => 'my test value'));
		$this->assertEquals('my test value', $widget->get('testKey'));
	}

	/** @test */
	public function shouldReturnJSClassName() {
		$widget = $this->createWidget(array('jsClass' => 'myTestClass'));
		$this->assertEquals('myTestClass', $widget->getJsClassName());
	}

	/** @test */
	public function shouldReturnJSAssets() {
		$widget = $this->createWidget(array('js' => 'file-1.js'));
		$this->assertTrue($widget->hasJS());
		$this->assertEquals(array('file-1.js'), $widget->getJS());

		$widget = $this->createWidget(array('js' => array('file-1.js', 'file-2.js')));
		$this->assertTrue($widget->hasJS());
		$this->assertEquals(array('file-1.js', 'file-2.js'), $widget->getJS());
	}

	/** @test */
	public function shoulReportIfJSIsAvailable() {
		$widget = $this->createWidget();
		$this->assertFalse($widget->hasJS());

		$widget = $this->createWidget(array('js' => 'file-1.js'));
		$this->assertTrue($widget->hasJS());
	}

	/** @test */
	public function shouldReturnCSSAssets() {
		$widget = $this->createWidget(array('css' => 'file-1.css'));
		$this->assertTrue($widget->hasCSS());
		$this->assertEquals(array('file-1.css'), $widget->getCSS());

		$widget = $this->createWidget(array('css' => array('file-1.css', 'file-2.css')));
		$this->assertTrue($widget->hasCSS());
		$this->assertEquals(array('file-1.css', 'file-2.css'), $widget->getCSS());
	}

	/** @test */
	public function shoulReportIfCssIsAvailable() {
		$widget = $this->createWidget();
		$this->assertFalse($widget->hasCSS());

		$widget = $this->createWidget(array('css' => 'file-1.css'));
		$this->assertTrue($widget->hasCSS());
	}

	/**
	 * @param array $data
	 *
	 * @return Hoborg\Dashboard\Widget
	 */
	private function createWidget(array $data = array()) {
		$kernel = $this->mockFactory->getKernelMock();
		$widget = new Widget($kernel, $data);

		return $widget;
	}
}

class WidgetDefaultTestCase extends Widget {
	protected $defaults = array(
		'config' => array(
			'view' => 'test-default'
		)
	);
}
