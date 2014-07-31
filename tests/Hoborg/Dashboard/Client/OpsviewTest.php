<?php
namespace Hoborg\Dashboard\Client;

class OpsviewTest extends \PHPUnit_Framework_TestCase {

	/**
	* @var Hoborg\Dashboard\Client\Opsview
	*/
	protected $fixture = null;

	public function setUp() {
	}

	/** @test */
	public function shoudAcceptConfigOnCreation() {
		$opsview = new Opsview('fakeUrl', array());
		$this->assertInstanceOf('Hoborg\\Dashboard\\Client\\Opsview', $opsview);
	}

}
