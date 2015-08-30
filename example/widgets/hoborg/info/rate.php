<?php
namespace Example\Widget;

use Hoborg\Dashboard\Widget;

class Info extends Widget {

	public function bootstrap() {
		$this->setupTemplate();
		$this->data['data'] = $this->getData();
	}

	public function getData() {
		return [
			'services' => [
				[
					'name' => 'API Lorem Ipsum',
					'errors' => 0.3,
					'warnings' => 2.1,
				],
			]
		];
	}

	public function getViewFile() {
		$cfg = $this->get('config', array());
		return __DIR__ . '/view/' . (empty($cfg['view']) ? 'rate' : $cfg['view'] ) . '.html';
	}

	protected function setupTemplate() {
		$this->data['template'] = file_get_contents($this->getViewFile());
	}
}
