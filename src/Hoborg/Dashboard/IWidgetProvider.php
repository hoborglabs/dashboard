<?php
namespace Hoborg\Dashboard;

interface IWidgetProvider {

	/**
	 * Returns ready to use Widget obcjet.
	 * Any custom logic should go here. Please keep your Widget class as simple
	 * as possible.
	 *
	 * @param Kernel $kernel
	 * @param array $widget
	 *
	 * @return Hoborg\Dashboard\Widget
	 */
	function createWidget(Kernel $kernel, array $widget);
}