<?php
namespace Hoborg\Dashboard;

class WidgetProviderTestCase extends WidgetProvider {

	public function testGetWidgetSources($w) {
		return $this->getWidgetSources($w);
	}
}