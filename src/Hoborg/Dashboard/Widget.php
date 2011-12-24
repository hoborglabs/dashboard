<?php
namespace Hoborg\Dashboard;

class Widget {

	protected $kernel = null;

	protected $data = array(
		'name' => '',
		'body' => '',
	);

	public function __construct(Kernel $kernel) {
		$this->kernel = $kernel;
	}

	public function bootstrap() {
		// bootstrap
		return $this;
	}

	public function hasHead() {
		return !empty($this->data['head']);
	}

	public function getJson() {
		return json_encode($this->data);
	}
}