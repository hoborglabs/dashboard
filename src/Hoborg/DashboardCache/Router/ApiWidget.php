<?php
namespace Hoborg\DashboardCache\Router;


use Hoborg\DashboardCache\Api\DataGet;
use Hoborg\DashboardCache\Api\WidgetGet;

class ApiWidget {

	public function getHandler($url, $method) {
		$urlParts = explode('/', $url);

		if (!isset($urlParts[0])) {
			return null;
		}
		$widgetid = $urlParts[0];

		if (1 == count($urlParts)) {
			if ('GET' == strtoupper($method)) {
				return new WidgetGet($widgetid);
			}
		} else if (2 == count($urlParts)) {
			if ('data' == $urlParts[1]) {
				return new DataGet($widgetid);
			}
		}

	}
}
