<?php 
namespace Hoborg\Dashboard;

class WidgetProvider {

	public function createWidget(Kernel $kernel, array $widget) {
		return new Widget($kernel, $widget);
	}
}