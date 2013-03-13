<?php
namespace Hoborg\DashboardCache\Router;


use Hoborg\DashboardCache\Api\WidgetGet;

class ApiWidget {

	public function getHandler($url, $method) {
		$urlParts = explode('/', $url);

		if (!isset($urlParts[0])) {
			return null;
		}
		$widgetid = $urlParts[0];

		if ('GET' == strtoupper($method)) {
			return new WidgetGet($widgetid);
		}
	}
}
