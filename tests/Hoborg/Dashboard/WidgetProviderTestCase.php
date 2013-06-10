<?php
namespace Hoborg\Dashboard;

class WidgetProviderTestCase extends WidgetProvider {

	public function testGetWidgetSources($w) {
		return $this->getWidgetSources($w);
	}

	protected function loadWidgetFromCgi($widget, $src) {
		return array();
	}

	protected function loadWidgetFromPhp(Widget $widget, $src) {
		return array();
	}
}